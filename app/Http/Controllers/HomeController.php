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
        $dataUpdate = DataUpdate::create([
            'type' => 0
        ]);

        $options = [
            'type' => 0,        // 0 = All shows, regardless of last update, 1 = All shows with last update > 20 days, 2 = show with ID = $options['show_id'], 3 = New show
        ];
        $start = microtime(true);

        $array = [1,2,3,4,5,6,7,8,9,10];
//        $array = [2];
        foreach($array as $id) {
            $showHelper = new ShowHelper();
            if ($options['type'] == 0) {
                $result = $showHelper->updateData($id, $dataUpdate);

                if ($result !== 0) {
                    $seasonHelper = new SeasonHelper();
                    $seasonResult = $seasonHelper->updateData($result['show'], $result['serverData']->seasons);

                    $episodeHelper = new EpisodeHelper();
                    $episodeResult = $episodeHelper->updateData($result['show'], $result['serverData']->episodes);

                }
            }
            echo microtime(true) - $start . '</br>';
            $start = microtime(true);
        }


//        $models = [new Action(), new ActionType(), new ApiUpdate(), new AuthTracking(), new AuthType(), new Bookmark(), new BookmarkType(),
//            new ContentRating(), new Episode(), new Genre(), new Network(), new Rating(), new Season(), new Show(), new ShowPoster(),
//            new Status(), new Torrent(), new DataUpdate(), new User(), new VideoQuality(), new VideoView()];

        return view('home');
    }
}
