<?php

namespace App\Http\Controllers\Helpers;

use App\AuthTracking;
use App\AuthType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuthTrackingHelper {
    private $authTracking;
    public function __construct($type) {
        $this->authTracking = new AuthTracking();

        $this->authTracking->fill(['user_id' => Auth::user()->id]);
        $this->authTracking->fill(['ip' => Request::ip()]);
        $this->authTracking->fill(['useragent' => Request::userAgent()]);

        switch ($type) {
            case 'login':
                $this->authTracking->fill(['type_id' => AuthType::where('name', $type)->value('id')]);
                break;
            case 'logout':
                $this->authTracking->fill(['type_id' => AuthType::where('name', $type)->value('id')]);
                break;
            default:
                // TODO Add an exception/create a custom exception
                break;
        }
        $this->authTracking->save();
    }
}
