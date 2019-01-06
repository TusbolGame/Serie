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
use App\Http\Controllers\Helpers\ShowHelper;
use App\Network;
use App\Rating;
use App\Season;
use App\Show;
use App\ShowPoster;
use App\Status;
use App\Torrent;
use App\DataUpdate;
use App\User;
use App\VideoQuality;
use App\VideoView;
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
        $showHandler = new ShowHelper();

        $dataUpdate = DataUpdate::create([
            'type' => 0
        ]);

        $options = [
            'type' => 0,        // 0 = All shows, regardless of last update, 1 = All shows with last update > 20 days, 2 = show with ID = $options['show_id'], 3 = New show
        ];

        if ($options['type'] == 0) {
            $result = $showHandler->updateData(335, $dataUpdate);
            if ($result !== 0) {

            }
        }


//        $models = [new Action(), new ActionType(), new ApiUpdate(), new AuthTracking(), new AuthType(), new Bookmark(), new BookmarkType(),
//            new ContentRating(), new Episode(), new Genre(), new Network(), new Rating(), new Season(), new Show(), new ShowPoster(),
//            new Status(), new Torrent(), new DataUpdate(), new User(), new VideoQuality(), new VideoView()];

        return view('home');
    }
}
