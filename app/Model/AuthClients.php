<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/7/19
 * Time: 16:57
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

/**
 * Oauth clients table model
 *
 * @property int $id Id
 * @property int $user_id User Id
 * @property string $name Name
 * @property string $secret Secret
 * @property string $redirect Redirect
 * @property int $personal_access_client Personal Access Client
 * @property int $password_client Password Client
 * @property int $revoked Revoked
 * @property string $created_at Created At
 * @property string $updated_at Updated At
 */
class AuthClients extends Model
{
    protected $table = 'oauth_clients';

    protected $fillable = ['user_id', 'name', 'secret', 'redirect', 'personal_access_client', 'password_client', 'revoked'];

    public static function rules()
    {
        return [
            'id' => 'integer|min:0|max:4294967295',
            'user_id' => 'integer|min:-2147483648|max:2147483647',
            'name' => 'max:191',
            'secret' => 'max:100',
            'personal_access_client' => 'integer|min:-128|max:127',
            'password_client' => 'integer|min:-128|max:127',
            'revoked' => 'integer|min:-128|max:127',
            'redirect' => '',
            'created_at' => '',
            'updated_at' => '',
        ];
    }
}