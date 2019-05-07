<?php

namespace App\Listeners;

use App\Http\Controllers\Helpers\AuthTrackingHelper;
use Illuminate\Auth\Events\Logout;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogoutSuccessListener {
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
    public function handle(Logout $event) {
        $authTracking = new AuthTrackingHelper('logout');
    }
}
