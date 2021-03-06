<?php

namespace App\Http\Controllers;

use App\Action;
use App\ActionType;
use App\Episode;
use App\Torrent;
use App\VideoQuality;
use App\VideoView;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use uTorrent\Api;

class EpisodeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function episode($episode_id) {
        $episodeCheck = Episode::where('uuid', $episode_id)
            ->with(['posters', 'torrent', 'videoView' => function($query) {
                $query->where('ended_at', '!=', NULL);
            }, 'show'])
            ->first();

        if (empty($episodeCheck)) {
            abort(404);
        }

        return view('episode', ['episode' => $episodeCheck]);
    }

    public function getUnwatched() {
        $episodes = Episode::whereRaw('airing_at < CONVERT_TZ(DATE_SUB(NOW(), INTERVAL 60 MINUTE), @@session.time_zone, \'+00:00\')')
            ->whereHas('show.users', function($query) {
                $query->where('id', Auth::user()->id);
            })
            ->doesnthave('videoView', 'and', function($query) {
                $query->where('ended_at', '!=', NULL);
            })->groupBy('show_id')
            ->with(['show' => function($query) {
                $query->withCount('unwatched');
            },
                'show.posters',
                'videoView',
                'videoView.bookmark' => function($query) {
                    $query->orderBy('time', 'desc');
                },
                'torrent' => function($query) {
                    $query->orderBy('status', 'asc');
                },
            ])
            ->withCount('torrent')
            ->orderBy('airing_at', 'desc')
            ->get();

        return AjaxSuccessController::response("Get Unwatched Successful", $episodes);
    }

    public function actionAdd($buttonGroup, $buttonType) {
        $actionTypesCheck = ActionType::where('id', $buttonType)->first();

        if ($actionTypesCheck !== NULL) {
            $action = new Action([
                'action_type_id' => $actionTypesCheck->id,
            ]);

            $action->save();

            return AjaxSuccessController::response("Action add Successful", []);
        } else {
            return AjaxErrorController::response("The button type does not exist", 409, 0);
        }
    }

    public function viewMark($episode, $state, $torrent = NULL) {
        $episodeCheck = Episode::where('uuid', $episode)->with('show')->first();

        if (empty($episodeCheck)) {
            return AjaxErrorController::response("The episode does not exist", 409, 1);
        }

        if (!is_null($torrent)) {
            $torrentCheck = Torrent::where('id', $torrent)->first();

            if (empty($torrentCheck)) {
                return AjaxErrorController::response("The torrent does not exist", 409, 2);
            }
        }

        switch ($state) {
            case 0:             // To be marked as unwatched
                $videoView = VideoView::where('ended_at', '!=', NULL)->first();
                $videoView->ended_at = NULL;
                $videoView->save();
                return AjaxSuccessController::response("Video mark Successful", []);
            case 1:             // To be marked as watched
                $videoView = new VideoView([
                    'user_id' => Auth::user()->id,
                    'episode_id' => $episodeCheck->id,
                    'torrent_id' => $torrent,
                    'ended_at' => Carbon::now()->toDateTimeString(),
                ]);
                $videoView->save();

                $nextEpisode = Episode::where([
                        ['show_id', $episodeCheck->show_id],
                        ['airing_at', '<', Carbon::now()->subMinutes($episodeCheck->show->running_time)],
                    ])->whereHas('show.users', function($query) {
                        $query->where('id', Auth::user()->id);
                    })
                    ->doesnthave('videoView', 'and', function($query) {
                        $query->where('ended_at', '!=', NULL);
                    })
                    ->with(['show' => function($query) {
                        $query->withCount('unwatched');
                    },
                        'show.posters',
                        'videoView',
                        'videoView.bookmark' => function($query) {
                            $query->orderBy('time', 'desc');
                        },
                        'torrent' => function($query) {
                            $query->orderBy('status', 'asc');
                        },
                    ])
                    ->withCount('torrent')
                    ->orderBy('airing_at', 'ASC')
                    ->first();

                if (!empty($nextEpisode)) {
                    return AjaxSuccessController::response("Video mark Successful", $nextEpisode);
                } else {
                    return AjaxSuccessController::response("Video mark Successful", []);
                }

            case 2:            // ALL to be marked as unwatched
                break;
            case 3:            // ALL to be marked as watched
                break;
            default:
                return AjaxErrorController::response("The view mark state is not correct", 409, 0);
                break;
        }

    }

    public function torrentAdd($episode, $magnetlink) {
        $hashCheck = preg_match('/([a-fA-F0-9]{40})/', $magnetlink, $matches);

        if ($hashCheck == FALSE || $hashCheck == 0) {
            return AjaxErrorController::response("The string is not a valid magnet link", 409, 0);
        }

        $episodeCheck = Episode::where('uuid', $episode)->first();
        if (empty($episodeCheck)) {
            return AjaxErrorController::response("The episode does not exist", 409, 1);
        }

        $torrentCheck = Torrent::where(['hash' => strtoupper($matches[1])])->first();

        if (!empty($torrentCheck)) {
            if ($torrentCheck->episode_id !== $episodeCheck->id) {
                return AjaxErrorController::response("Wrong association between torrent and episode. The same torrent is already linked to an other episode", 409, 4);
            }
            $torrent = $torrentCheck;
        } else {
            $torrent = new Torrent();
            $torrent->fill([
                'hash' => strtoupper($matches[1]),
                'episode_id' => $episodeCheck->id,
            ]);
            $torrent->save();
        }

        $utorrentClient = new Api(
            config('custom.torrentClient.host'),
            config('custom.torrentClient.port'),
            config('custom.torrentClient.username'),
            config('custom.torrentClient.password')
        );

        if (!$utorrentClient->is_online()) {
            return AjaxErrorController::response("The torrent client is not online", 404, 2);
        }

        $result = $utorrentClient->torrentAdd($magnetlink);

//        if ($result == FALSE) {
//            return AjaxErrorController::response("The torrent could not be added", 409, 3);
//        }

        $result = $utorrentClient->torrentStart('magnet:?xt=urn:btih:' . $magnetlink);

        return AjaxSuccessController::response("Torrent addition Successful", []);
    }

    public function torrentCheck($hash) {
        $torrentCheck = Torrent::where('hash', $hash)->first();

        if (empty($torrentCheck)) {
            return AjaxErrorController::response("The hash does not exist", 409, 4);
        }

        $utorrent = new Api(
            config('custom.torrentClient.host'),
            config('custom.torrentClient.port'),
            config('custom.torrentClient.username'),
            config('custom.torrentClient.password')
        );

        $torrents = $utorrent->getTorrents();
        $currentTorrentKey = array_search($hash, array_column($torrents, 0));

        if ($torrentCheck->started_at == NULL) {
            if ($torrents[$currentTorrentKey][23] != NULL || $torrents[$currentTorrentKey][23] != 0) {
                $torrentCheck->status = 1;
                $torrentCheck->started_at = Carbon::createFromTimestamp($torrents[$currentTorrentKey][23])->toDateTimeString();
            }
        }

        if ($torrents[$currentTorrentKey][10] == 0) {                 // If ETA 0 = finished
            if ($torrentCheck->finished_at == NULL) {
                if ($torrents[$currentTorrentKey][24] != NULL || $torrents[$currentTorrentKey][24] != 0) {
                    $torrentCheck->status = 2;
                    $torrentCheck->finished_at = Carbon::createFromTimestamp($torrents[$currentTorrentKey][24])->toDateTimeString();
                }
            }
        }

        // To get file_name and file_size
        $torrentFiles = $utorrent->getFiles($hash);

        foreach ($torrentFiles[1] as $key => $row) {        // Get all the filesizes to sort the torrents array
            $newTorrentFiles[$key] = $row[1];
        }

        array_multisort($newTorrentFiles, SORT_DESC, $torrentFiles[1]);

        if ($torrentCheck->file_name == NULL) {
            if ($torrents[$currentTorrentKey][26] !== config('custom.torrentFolder')) {                     // If the actual video file is in a folder
                $torrentCheck->file_name = $torrents[$currentTorrentKey][2] . '/' . $torrentFiles[1][0][0];     // Add the name of the folder to the name of the file
            } else {
                $torrentCheck->file_name = $torrentFiles[1][0][0];
            }
        }

        $videoQualities = VideoQuality::get();

        $torrentCheck->video_quality_id = NULL;
        foreach ($videoQualities as $videoQuality) {
            if (strpos($torrentFiles[1][0][0], $videoQuality->code) !== false) {
                $torrentCheck->video_quality_id = $videoQuality->id;
            }
        }

        $torrentCheck->save();

        return AjaxSuccessController::response("Torrent check Successful", $torrents[$currentTorrentKey][10]);
    }

    public function codeGenerator($season, $episode) {
        $code = "S" . str_pad($season, 2, '0', STR_PAD_LEFT) .
            "E" . str_pad($episode, 2, '0', STR_PAD_LEFT);
        return $code;
    }
}
