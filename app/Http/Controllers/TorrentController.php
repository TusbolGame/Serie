<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Torrent;
use Illuminate\Http\Request;

class TorrentController extends Controller {

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
            } else {
                $episode = Episode::where('uuid', $episode)->with('show', 'torrent')->first();

                return AjaxSuccessController::response("Torrent already present and returned.", $episode);
            }
        }

        $torrent = new Torrent();
        $torrent->fill([
            'hash' => strtoupper($matches[1]),
            'episode_id' => $episodeCheck->id,
        ]);
        $torrent->save();

        $episode = Episode::where('uuid', $episode)->with('show', 'torrent')->first();

        return AjaxSuccessController::response("Torrent addition Successful", $episode);
    }
}
