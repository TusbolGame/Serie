<?php

namespace App\Http\Controllers;

use App\Show;
use App\User;
use Illuminate\Support\Facades\Auth;

class ShowController extends Controller {
    public function removeShow($show) {
        $showCheck = Show::where('uuid', $show)->first();

        if (empty($showCheck)) {
            return new AjaxErrorController("The show does not exist", 409, 0);
        }

        $user = User::where('id', Auth::user()->id)->first();
        $user->shows()->detach($showCheck);

        return new AjaxSuccessController("Show removal Successful", []);
    }
}
