<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/7/29
 * Time: 16:14
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

/**
 * Question table model
 *
 * @property int $id Id
 * @property string $openid 用户的openid
 * @property string $nickname Nickname
 * @property string $headimgurl Headimgurl
 * @property string $name 垃圾名称
 * @property int $type 1是否  2多选
 * @property int $agree 赞同的票数
 * @property int $oppose 反对的票数
 * @property string $category_votes 各个分类票数的json数据
 * @property string $created_at Created At
 * @property string $updated_at Updated At
 * @property string $deleted_at Deleted At
 */
class Question extends Model
{
    //问题类型一，判断是否是某类垃圾
    const TYPE_ONE = 1;

    //问题类型二，不知道具体是哪类垃圾，从所有分类里面投票出一个
    const TYPE_TWO = 2;

    protected $table = 'question';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['openid', 'nickname', 'headimgurl', 'name', 'type', 'agree', 'oppose', 'category_votes'];

    public static function rules()
    {
        return [
            'id' => 'integer|min:-2147483648|max:2147483647',
            'openid' => 'max:500',
            'nickname' => 'max:500',
            'headimgurl' => 'max:500',
            'name' => 'max:200',
            'type' => 'integer|min:0|max:255',
            'agree' => 'integer|min:-32768|max:32767',
            'oppose' => 'integer|min:-32768|max:32767',
            'category_votes' => 'max:500',
            'created_at' => '',
            'updated_at' => '',
            'deleted_at' => '',
        ];
    }

    public function getCategoryVotesAttribute($value){
        return json_decode($value);
    }
}