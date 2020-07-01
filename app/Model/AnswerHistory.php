<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/7/30
 * Time: 15:29
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

/**
 * Answer history table model
 *
 * @property int $id Id
 * @property string $openid Openid
 * @property int $q_id 问题id
 * @property string $opt 操作 0赞同 -1返对 其他数字代表给对应的分类id投了一票
 * @property string $created_at Created At
 * @property string $updated_at Updated At
 * @property string $deleted_at Deleted At
 */
class AnswerHistory extends Model
{
    protected $table = 'answer_history';

    protected $fillable = ['openid', 'q_id', 'opt'];

    public static function rules()
    {
        return [
            'id' => 'integer|min:-2147483648|max:2147483647',
            'openid' => 'max:200',
            'q_id' => 'integer|min:-2147483648|max:2147483647',
            'opt' => 'max:100',
            'created_at' => '',
            'updated_at' => '',
            'deleted_at' => '',
        ];
    }
}