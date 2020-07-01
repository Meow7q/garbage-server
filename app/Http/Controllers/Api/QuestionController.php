<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/7/29
 * Time: 9:58
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\AnswerHistory;
use App\Model\Category;
use App\Model\Garbage;
use App\Model\Question;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * 新增问题
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'category_id' => 'Integer',
            'type' => 'required|Integer',
        ]);
        if ($validator->fails()) {
            return $this->message($validator->errors()->first(), 0);
        }

        $user = Auth::user();

        $name = trim($request->input('name'));
        $is_safe = $this->contentSecurity($name);
        if(!$is_safe){
            return $this->message('您的输入包含违法内容，请检查后重新提交!', 0);
        }
        $have_created = Question::where('name', $name)->first();
        if($have_created){
            return $this->message('该垃圾正在审核入库中，换个词吧！', 0);
        }
        $have_existed = Garbage::where('name', $name)->first();
        if($have_existed){
            return $this->message('该垃圾已经在词库中啦，换个词吧！', 0);
        }

        $type = $request->input('type');
        if($type==Question::TYPE_ONE){
            $category_id = $request->input('category_id');
            $category_info = Category::where('category_id', $category_id)->first();
            if(!$category_info){
                return $this->message('该垃圾分类不存在！', 0);
            }
            $category_votes = [
                'category_id' => $category_id,
                'category_name' => $category_info->category_name
            ];
        }else{
            $all_categories = Category::select('category_id', 'category_name')->get();
            $category_votes = $all_categories->toArray();
            foreach ($category_votes as $k=>$v){
                $category_votes[$k]['votes'] = 0;
            }
        }

        $rs = Question::create([
            'nickname' => $user->name,
            'openid' => $user->openid,
            'headimgurl' => $user->headimgurl,
            'name' => $name,
            'type' => $type,
            'category_votes' => json_encode($category_votes),
        ]);
        return $this->success([
            'q_id' => $rs->id,
            'shareImgUrl' => '',
            'message' => '提交成功,等待网友审核!'
        ], 201);
    }


    /**
     * 获取问题列表
     * @param Request $request
     * @return mixed
     */
    public function getQuestionList(Request $request){
        $user = Auth::user();
        $list = Question::whereNotIn('id', function ($query)use($user){
            $query->select('q_id')->from('answer_history')->where('answer_history.openid', $user->openid);
        })->select(['id', 'headimgurl', 'name', 'type', 'agree', 'oppose', 'category_votes'])
            ->orderBy('updated_at', 'desc')->paginate(20);

        return $this->success($list, 200);
    }

    /**
     * 根据id返回题目信息
     * @param Request $request
     * @return mixed
     */
    public function getQuestionbyId(Request $request, $q_id){
        if(!$q_id || !is_numeric($q_id)){
            return $this->message('参数错误！', 0);
        }

        $rs = Question::where('id', $q_id)
            ->select(['id', 'openid', 'nickname', 'headimgurl', 'name', 'type', 'agree', 'oppose', 'category_votes'])
            ->first();
        if($rs){
            return $this->success($rs, 200);
        }
        return $this->message('该词已经成功入库，你可以通过搜索看到啦!', 0);
    }


    /**
     * 投票
     * @param Request $request
     * @return mixed
     */
    public function vote(Request $request){
        //操作 0赞同 -1返对 其他数字代表给对应的分类id投了一票
        $opt = $request->input('opt');
        $q_id = $request->input('q_id');
        if(!$q_id || !is_numeric($opt)){
            return $this->message('参数非法！', 0);
        }

        return $this->executeOpt($q_id, $opt);
    }

    /**
     * 根据opt参数执行操作
     * @param $q_id
     * @param $opt
     * @return mixed
     */
    protected function executeOpt($q_id, $opt){
        $opt = intval($opt);
        $q_info = Question::where('id', $q_id)
            ->select(['openid', 'nickname', 'headimgurl', 'name', 'type', 'agree', 'oppose', 'category_votes'])
            ->first();

        $have_answered = AnswerHistory::where('q_id', $q_id)
            ->where('openid', Auth::user()->openid)
            ->first();
        //重复回答
        if($have_answered){
            return $this->message('repeate!!!', 0);
        }
        if(!$q_info){
            return $this->message('该条记录不存在!', 0);
        }
        $over_votes = 10;
        $reach_votes = 20;
        switch ($opt){
            //反对
            case -1:
                Question::where('id', $q_id)
                    ->increment('oppose');
                //如果反对数比同意数大10,则从审核列表删除
                if(($q_info->oppose-$q_info->agree)>=($over_votes-1)){
                    $this->addToDictionary(1, $q_id, $q_info->name, $q_info->category_votes->category_id, $q_info->category_votes->category_name);
                }
                break;
            case 0:
             //支持
                Question::where('id', $q_id)
                    ->increment('agree');
                if(($q_info->agree-$q_info->oppose)>=($over_votes-1)){
                    $this->addToDictionary(0, $q_id, $q_info->name, $q_info->category_votes->category_id, $q_info->category_votes->category_name);
                }
                break;
            default:
             //为某个分类投一票
                $category_votes = $q_info->category_votes;
                foreach ($category_votes as $k=>$v){
                    if($v->category_id == $opt){
                        $category_votes[$k]->votes++;

                        //如果大于设定的阈值则加入词库
                        if($category_votes[$k]->votes>=($reach_votes-1)){
                            $this->addToDictionary(0, $q_id, $q_info->name, $category_votes[$k]->category_id, $category_votes[$k]->category_name);
                        }
                    }
                }
                Question::where('id', $q_id)
                    ->update([
                        'category_votes' => json_encode($category_votes)
                    ]);
                break;
        }

        AnswerHistory::create([
            'openid' => Auth::user()->openid,
            'q_id' => $q_id,
            'opt' => $opt,
        ]);

        return $this->message('操作成功!', 1);
    }

    /**
     * 满足条件填入词库或者从审核列表删除
     * @param $is_oppose 反对的情况
     * @param $q_id
     * @param $name
     * @param $category_id
     * @param $category_name
     */
    protected function addToDictionary($is_oppose, $q_id, $name, $category_id, $category_name){
        //入库
        if(!$is_oppose){
            $rs = Garbage::firstOrCreate(['name'=>$name],[
                'name' => $name,
                'category_id' => $category_id,
                'category_name' => $category_name
            ])->toSql();
        }

        //从审核列表删除
        Question::where('id', $q_id)->delete();
    }

    /**
     * 文本内容安全监测
     * @param $content
     * @return bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    protected function contentSecurity($content){
        $config = [
            'app_id' => 'wx88fc0e27bd7c2f5d',
            'secret' => 'e3fe23a76d24e1ccaf99aa5485eaef2e',
        ];

        $app = Factory::miniProgram($config);
        $rs = $app->content_security->checkText($content);
        if(isset($rs['errcode']) && $rs['errcode']==0){
           return true;
        }
        return false;
    }
}