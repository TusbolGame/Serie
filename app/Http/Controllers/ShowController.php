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
