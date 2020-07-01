<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/7/10
 * Time: 16:02
 */

namespace App\Http\Controllers\Api;


use App\Events\RevokeOldTokens;
use App\Events\UserRegisterEvent;
use App\Http\Controllers\Controller;
use App\Model\User;
use EasyWeChat\Factory;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TokenController extends Controller
{
    public function getToken(Request $request){
        $code = $request->input('code');
        if(!$code){
            return $this->message('参数错误！', 0);
        }
        $rs =  $this->getInfoFromWxByCode($request->input('code'));
        if(isset($rs['errcode']) && $rs['errcode']!=0){
            return $this->message($rs['errmsg'], 0);
        }
        $openid = $rs['openid'];
        //注册
        event(new UserRegisterEvent($openid));
        $user = User::where('openid', $openid)->first();
        //删除原来的token
        event(new RevokeOldTokens($user->id));
        //获取私人令牌
        $token = $user->createToken($openid)->accessToken;
        return $this->success([
            'share_img_url' => 'https://garbage.meow7.cn/upload/img/share_img.jpg',
            'token_type' => 'Bearer',
            'token' => $token,
            'expire_in'=> '',
            'access_token'=> '',
            'refresh_token'=> '',
        ],201);
    }

    /**
     * 获取百度AItoken
     * @param Request $request
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTokenForBaidu(Request $request){
        $token_for_baidu = Cache::store('redis')->get('bd_token');
        if($token_for_baidu){
            return $this->success([
                'from_redis' => 1,
                'bd_token' => $token_for_baidu
            ],200);
        }
        //如果不存在
        $url = 'https://aip.baidubce.com/oauth/2.0/token';
        $params = [
            'grant_type' => 'client_credentials',
            'client_id' => 'rdFLW15M8huScdGmtsyhkTks',
            'client_secret' => 'SNVfh0HXkh9HAOMn1Gg1E6BdaZPXNBIi',
        ];

        $http = new Client([
            'http_errors' => false
        ]);
        $rs = $http->request('POST', $url, ['form_params'=>$params]);
        $content = json_decode((string) $rs->getBody(), true);

        if($rs->getStatusCode()!=200){
            return isset($content['error'])?$this->message($content['error_description'],0):$this->message('获取百度token失败，请检查网络！',0);
        }

        //存入redis一个月
        Cache::store('redis')->put('bd_token', $content['access_token'], 40000);
        return $this->success([
            'from_redis' => 0,
            'bd_token' => $content['access_token']
        ],200);
    }

    public function getInfoFromWxByCode($code){
        $config = [
            'app_id' => 'wx88fc0e27bd7c2f5d',
            'secret' => 'e3fe23a76d24e1ccaf99aa5485eaef2e',
        ];

        $app = Factory::miniProgram($config);
        return $app->auth->session($code);
    }
}