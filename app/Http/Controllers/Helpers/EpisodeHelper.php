<?php

namespace App\Http\Controllers\Helpers;

use App\Episode;
use App\Season;
use App\Traits\PosterHandler;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EpisodeHelper extends Controller {
    use PosterHandler;

    private $show;

    public function updateData($show, $data) {
        $this->show = $show;
        if (!isset($data) || $data == "") {
            return 0;
        }
        $newEpisodes = new Collection();
        foreach ($data as $episodeData) {
            $episode = $this->addData($episodeData);
            $newEpisodes->push($episode->fresh());
        }
        if (empty($newEpisodes)) {
            return 0;
        }
        return $newEpisodes;
    }

    public function addData($data) {
        $episode = Episode::firstOrNew([
            'uuid' => Str::orderedUuid()->toString(),
            'api_id' => $data->id,
            'api_link' => $data->url,
            'episode_number' => $data->number,
            'episode_code' => $this->episodeCodeGenerator($data->season, $data->number),
            'title' => $data->name,
            'summary' => $data->summary,
        ]);

        $airstamp = NULL;
        if (isset($data->airstamp) && $data->airstamp != "") {
            $airstamp = \DateTime::createFromFormat(\DateTime::W3C, $data->airstamp);
            if ($airstamp == FALSE) {
                $airstamp = NULL;
            }
        }
        $episode->fill(['airing_at' => $airstamp]);

        $poster = $this->posterHandler($data->image, $episode, config('custom.episodeOriginalFolder'));
        if ($poster == NULL) {
            $episode->fill(['poster_id', NULL]);
        } else {
            $episode->posters()->save($poster);
            $freshPoster = $poster->fresh();
            $episode->update(['poster_id' => $freshPoster->id]);
        }

        $season = Season::where([
            'show_id' => $this->show->id,
            'season' => $data->season,
            ])->first();

        if ($season != NULL) {
            $episode->season()->associate($season);
        }

        $episode->show()->associate($this->show);
        $episode->save();

        return $episode;
    }

    public function episodeCodeGenerator($season, $episode) {
        $code = "S" . str_pad($season, 2, '0', STR_PAD_LEFT) .
            "E" . str_pad($episode, 2, '0', STR_PAD_LEFT);
        return $code;
    }
}
