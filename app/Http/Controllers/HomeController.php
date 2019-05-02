<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Events\EpisodeCreated;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
//        $update = new DataUpdateController();
//        $update->updateManager(0);

        $episodes = Episode::whereRaw('airing_at < CONVERT_TZ(DATE_SUB(NOW(), INTERVAL 60 MINUTE), @@session.time_zone, \'+00:00\')')
        ->whereHas('show.users', function($query) {
            $query->where('id', Auth::user()->id);
        })
        ->doesnthave('videoView', 'and', function($query) {
            $query->where('ended_at', '!=', NULL);
        })->groupBy('show_id')
        ->with(['show','show.posters', 'videoView'
            , 'videoView.bookmark' => function($query) {
                $query->orderBy('time', 'desc');
            }, 'torrent' => function($query) {
                $query->orderBy('status', 'asc');
        }])
        ->withCount('torrent')
        ->orderBy('airing_at', 'desc')
        ->get();

        $scheduleInterval = 14;
        $date = Carbon::today();
        $schedule = [];
        for ($i = 0; $i < $scheduleInterval; $i++) {
            $currentDate = Carbon::today()->addDays($i);
            $schedule[$currentDate->toDateString()] = Episode::whereDate('airing_at', '=', $currentDate->toDateString())
                ->whereHas('show.users', function($query) {
                    $query->where('id', Auth::user()->id);
                })
                ->with(['show'
                    ])
                ->withCount(['videoView' => function($query) {
                    $query->where('ended_at', '!=', NULL);
                }])
                ->withCount('torrent')
                ->orderBy('airing_at', 'asc')
                ->get();
        }

//        $search = new DataUpdateController();
//        $result = $search->searchShowController('person of interest');
//        var_dump($result);

        return view('home', ['episodes' => $episodes, 'schedule' => $schedule]);
    }
}
