<?php

namespace App\Http\Controllers;

use App\Action;
use App\ActionType;
use App\ApiUpdate;
use App\AuthTracking;
use App\AuthType;
use App\Bookmark;
use App\BookmarkType;
use App\ContentRating;
use App\Episode;
use App\Genre;
use App\Http\Controllers\Helpers\EpisodeHelper;
use App\Http\Controllers\Helpers\SeasonHelper;
use App\Http\Controllers\Helpers\ShowHelper;
use App\Network;
use App\Rating;
use App\Season;
use App\Show;
use App\Poster;
use App\Status;
use App\Torrent;
use App\DataUpdate;
use App\User;
use App\VideoQuality;
use App\VideoView;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
//        $models = [new Action(), new ActionType(), new ApiUpdate(), new AuthTracking(), new AuthType(), new Bookmark(), new BookmarkType(),
//            new ContentRating(), new Episode(), new Genre(), new Network(), new Rating(), new Season(), new Show(), new ShowPoster(),
//            new Status(), new Torrent(), new DataUpdate(), new User(), new VideoQuality(), new VideoView()];

        return view('home');
    }
}
