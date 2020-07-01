<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/7/23
 * Time: 15:40
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function updateInfo(Request $request){
        $info = $request->only('nickName','gender', 'avatarUrl', 'country', 'province', 'city');
        if(!$info || !is_array($info)){
            return $this->message('参数错误！', 0);
        }
        $info['name'] = $info['nickName'];
        unset($info['nickName']);
        $info['headimgurl'] = $info['avatarUrl'];
        unset($info['avatarUrl']);
        $user = Auth::user();
        $rs = User::where('id', $user->id)
            ->update($info);
        return $this->setStatusCode(201)->message('更新成功！', 1);
    }
}