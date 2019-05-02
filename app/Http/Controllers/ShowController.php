<?php

namespace App\Http\Controllers;

use App\Show;
use App\User;
use Illuminate\Support\Facades\Auth;

class ShowController extends Controller {
    public function show($show_id) {
        $showCheck = Show::where('uuid', $show_id)
            ->with(['season.posters', 'season.episode.posters', 'posters', 'season.episode.videoView' => function($query) {
                $query->where('ended_at', '!=', NULL);
            }])
            ->first();

        if (empty($showCheck)) {
            abort(404);
        }

        return view('show', ['show' => $showCheck]);
    }

    public function removeUserShow($show) {
        $showCheck = Show::where('uuid', $show)->first();

        if (empty($showCheck)) {
            return AjaxErrorController::response("The show does not exist", 409, 0);
        }

        $user = User::where('id', Auth::user()->id)->first();
        if ($user == NULL) {
            return AjaxErrorController::response("No user is logged in????", 409, 0);
        }
        $user->shows()->detach($showCheck);

        return AjaxSuccessController::response("Show removal Successful", []);
    }

    public function addUserShow($show) {
        $showCheck = Show::where('uuid', $show)->first();

        if (empty($showCheck)) {
            return AjaxErrorController::response("The show does not exist", 409, 0);
        }

        $user = User::where('id', Auth::user()->id)->first();
        if ($user == NULL) {
            return AjaxErrorController::response("No user is logged in????", 409, 0);
        }
        $user->shows()->attach($showCheck);

        return AjaxSuccessController::response("Show addition Successful", []);
    }
}
