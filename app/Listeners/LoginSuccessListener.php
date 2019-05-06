<?php

namespace App\Listeners;

use App\Http\Controllers\Helpers\AuthTrackingHelper;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoginSuccessListener {
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
     * @param  object  $event
     * @return void
     */
    public function handle(Login $event) {
        $authTracking = new AuthTrackingHelper('login');
    }
}
