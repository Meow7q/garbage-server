<?php

namespace App\Listeners;

use App\Events\RevokeOldTokens;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Passport\Token;

class RevokeOldTokensListener
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
     * @param  RevokeOldTokens  $event
     * @return void
     */
    public function handle(RevokeOldTokens $event)
    {
        $user_id = $event->user_id;
        Token::where('id', $event->user_id)
            ->where('revoked', 0)
            ->delete();
        //
    }
}
