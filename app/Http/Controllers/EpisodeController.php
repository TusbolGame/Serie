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

    public function actionAdd($buttonType) {
        $actionType = '';
        switch ($buttonType) {
            case 1:             // Search for torrents
                $newAction = 'searchTorrent';
                break;
            case 2:             // Add magnet link
                $newAction = 'addMagnetlink';
                break;
            case 3:             // Convert Torrent
                $newAction = 'convertTorrent';
                break;
            case 4:             // Play Episode
                $newAction = 'playEpisode';
                break;
            case 5:             // Mark as watched
                $newAction = 'markView';
                break;
            case 6:             // Check Torrent Status
                $newAction = 'checkTorrentStatus';
                break;
            case 7:             // Remove show
                $newAction = 'removeShow';
                break;
            case 8:             // Add bookmark
                $newAction = 'bookmarkAdd';
                break;
            case 9:             // Show more details
                $newAction = 'episodeDetails';
                break;
            case 10:             // Rate this episode
                $newAction = 'episodeRate';
                break;
            default:
                return new AjaxErrorController("The button type does not exist", 409, 0);
                break;
        }

        $actionType = ActionType::where('name', $newAction)->first();

        if (empty($actionType)) {
            return new AjaxErrorController("Backend error, the action type does not exist", 409, 1);
        }

        $action = new Action([
            'action_type_id' => $actionType->id,
        ]);

        $action->save();

        return new AjaxSuccessController("Action add Successful", []);
    }

    public function viewMark($episode, $state, $torrent = NULL) {
        $episodeCheck = Episode::where('uuid', $episode)->with('show')->first();

        if (empty($episodeCheck)) {
            return new AjaxErrorController("The episode does not exist", 409, 1);
        }

        if (!is_null($torrent)) {
            $torrentCheck = Torrent::where('id', $torrent)->first();

            if (empty($torrentCheck)) {
                return new AjaxErrorController("The torrent does not exist", 409, 1);
            }
        }

        switch ($state) {
            case 0:             // To be marked as unwatched
                $videoView = VideoView::where('ended_at', '!=', NULL)->first();
                $videoView->ended_at = NULL;
                $videoView->save();
                return new AjaxSuccessController("Video mark Successful", []);
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
                ])->doesnthave('videoView', 'and', function($query) {
                    $query->where('ended_at', '!=', NULL);
                })->orderBy('airing_at', 'ASC')->first();

                if (!empty($nextEpisode)) {
                    return new AjaxSuccessController("Video mark Successful", view('components.card-episode', ['episode' => $nextEpisode])->render());
                } else {
                    return new AjaxSuccessController("Video mark Successful", []);
                }

            case 2:            // ALL to be marked as unwatched
                break;
            case 3:            // ALL to be marked as watched
                break;
            default:
                return new AjaxErrorController("The view mark state is not correct", 409, 0);
                break;
        }

    }

    public function torrentAdd($episode, $magnetlink) {
        $hashCheck = preg_match('/([a-fA-F0-9]{40})/', $magnetlink, $matches);

        if ($hashCheck == FALSE || $hashCheck == 0) {
            return new AjaxErrorController("The string is not a valid magnet link", 409, 0);
        }

        $episodeCheck = Episode::where('uuid', $episode)->first();
        if (empty($episodeCheck)) {
            return new AjaxErrorController("The episode does not exist", 409, 1);
        }

        $torrentCheck = Torrent::where(['hash' => strtoupper($matches[1])])->first();

        if (!empty($torrentCheck)) {
            if ($torrentCheck->episode_id !== $episodeCheck->id) {
                return new AjaxErrorController("Wrong association between torrent and episode. The same torrent is already linked to an other episode", 409, 4);
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
            return new AjaxErrorController("The torrent client is not online", 404, 2);
        }

        $result = $utorrentClient->torrentAdd($magnetlink);

//        if ($result == FALSE) {
//            return new AjaxErrorController("The torrent could not be added", 409, 3);
//        }

        $result = $utorrentClient->torrentStart('magnet:?xt=urn:btih:' . $magnetlink);

        return new AjaxSuccessController("Torrent addition Successful", []);
    }

    public function torrentCheck($hash) {
        $torrentCheck = Torrent::where('hash', $hash)->first();

        if (empty($torrentCheck)) {
            return new AjaxErrorController("The hash does not exist", 409, 4);
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

        return new AjaxSuccessController("Torrent check Successful", $torrents[$currentTorrentKey][10]);
    }
}
