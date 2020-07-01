<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/7/23
 * Time: 15:03
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;


/**
 * Search history table model
 *
 * @property int $id Id
 * @property string $keyword 用户所搜索的词
 * @property int $category_id Category Id
 * @property string $category_name 分类名称
 * @property string $created_at Created At
 * @property string $updated_at Updated At
 * @property string $deleted_at Deleted At
 */
class SearchHistory extends Model
{
    protected $table = 'search_history';

    protected $fillable = ['keyword', 'category_id', 'category_name'];

    public static function rules()
    {
        return [
            'id' => 'integer|min:-2147483648|max:2147483647',
            'keyword' => 'max:200',
            'category_id' => 'integer|min:0|max:65535',
            'category_name' => 'max:200',
            'created_at' => '',
            'updated_at' => '',
            'deleted_at' => '',
        ];
    }
}