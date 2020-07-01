<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/7/10
 * Time: 10:29
 */

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'openid', 'headimgurl', 'gender', 'province', 'city', 'country', 'email', 'password', 'remember_token'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function rules()
    {
        return [
            'id' => 'integer|min:0|max:4294967295',
            'name' => 'max:191',
            'openid' => 'max:191',
            'headimgurl' => 'max:191',
            'gender' => 'integer|min:-128|max:127',
            'province' => 'max:100',
            'city' => 'max:100',
            'country' => 'max:100',
            'email' => 'email',
            'password' => 'max:191',
            'remember_token' => 'max:100',
            'created_at' => '',
            'updated_at' => '',
        ];
    }

    /**
     * @param $username
     * @return mixed
     */
    public function findForPassport($username) {
        return $this->where('openid', $username)->first();
    }

    /**
     * 重写验证密码函数，密码等于加密后的openid
     * @param $password
     * @return bool
     */
    public function validateForPassportPasswordGrant($password)
    {
        return Crypt::decryptString($password) == $this->password;
    }
}