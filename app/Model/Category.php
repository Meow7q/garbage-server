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
 * Category table model
 *
 * @property int $id Id
 * @property int $category_id Category Id
 * @property string $category_name Category Name
 * @property string $created_at Created At
 * @property string $updated_at Updated At
 * @property string $deleted_at Deleted At
 */
class Category extends Model
{
    protected $table = 'category';

    protected $fillable = ['category_id', 'category_name'];

    public static function rules()
    {
        return [
            'id' => 'integer|min:-32768|max:32767',
            'category_id' => 'integer|min:0|max:65535',
            'category_name' => 'max:100',
            'created_at' => '',
            'updated_at' => '',
            'deleted_at' => '',
        ];
    }
}