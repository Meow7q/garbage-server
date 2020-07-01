<?php

namespace App\Listeners;

use App\Events\UserRegisterEvent;
use App\Model\User;
use App\Service\Logger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Crypt;

class UserRegisterEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegisterEvent  $event
     * @return void
     */
    public function handle(UserRegisterEvent $event)
    {
        try{
            $openid = $event->openid;
            User::firstOrCreate(['openid'=>$openid],[
                'openid' => $openid,
                'password' => md5($openid)
            ]);
        }catch (\Exception $e){
            Logger::getLogger('userRegisterEvent')->warning($e->getMessage());
        }
        //
    }
}
