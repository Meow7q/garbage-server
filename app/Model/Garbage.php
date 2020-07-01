<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/7/26
 * Time: 11:06
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

/**
 * Garbage table model
 *
 * @property int $id Id
 * @property string $name 名称
 * @property int $category_id Category Id
 * @property string $category_name 分类名称
 * @property string $created_at Created At
 * @property string $updated_at Updated At
 * @property string $deleted_at Deleted At
 */
class Garbage extends Model
{
    protected $table = 'garbage';

    protected $fillable = ['name', 'category_id', 'category_name'];

    public static function rules()
    {
        return [
            'id' => 'integer|min:-2147483648|max:2147483647',
            'name' => 'max:100',
            'category_id' => 'integer|min:-128|max:127',
            'category_name' => 'max:100',
            'created_at' => '',
            'updated_at' => '',
            'deleted_at' => '',
        ];
    }
}