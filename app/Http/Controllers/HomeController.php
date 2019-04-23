<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Show;
use Illuminate\Support\Facades\DB;

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
//        $episodes = Episode::groupBy('show_id')->get();
//        $episodes = new Episode();
//        $episodes = $episodes->unwatched()->get();
//        $episodesSql = Episode::whereRaw('airing_at < NOW()')->doesnthave('videoView', 'and', function($query) {
//            $query->where('ended_at', '!=', NULL);
//        })->groupBy('show_id')->with(['show', 'show'])->toSql();
//        $episodes = Episode::whereRaw('CONVERT_TZ(DATE_ADD(airing_at, INTERVAL shows.running_time MINUTE), "UTC", Europe/Rome) <= NOW()')->doesnthave('videoView', 'and', function($query) {

//            $episodes = Episode::whereRaw('airing_at < NOW()')->doesnthave('videoView', 'and', function($query) {
//            $query->where('ended_at', '!=', NULL);
//        })->groupBy('show_id')->with(['show', 'videoView', 'videoView.bookmark' => function($query) {
//            $query->orderBy('time', 'desc');
//        }])->get();
////        dd($episodesSql);
////        $episodes = Episode::doesnthave('videoView')->with(['show', 'show.posters'])->get();
//        var_dump($episodes->toArray()); //$episodes->toArray()[0]['show'],

        return view('home');
    }
}
