<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\PosterController;
use App\Season;
use App\Traits\PosterHandler;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class SeasonHelper extends Controller {
    use PosterHandler;

    private $show;

    public function updateData($show, $data) {
        $this->show = $show;
        if (!isset($data) || $data == "") {
            return 0;
        }
        $newSeasons = new Collection();
        foreach ($data as $seasonData) {
            $season = $this->addData($seasonData);
            $newSeasons->push($season->fresh());
        }
        if (empty($newSeasons)) {
            return 0;
        }
        return $newSeasons;
    }

    public function addData($data) {
        $season = Season::firstOrNew([
            'uuid' => Str::orderedUuid()->toString(),
            'api_id' => $data->id,
            'season' => $data->number,
            'episodes' => ($data->episodeOrder !== NULL) ? $data->episodeOrder : NULL,
            'date_start' => ($data->premiereDate !== NULL && $data->premiereDate !== "") ? $data->premiereDate : NULL,
            'date_end' => ($data->endDate !== NULL && $data->endDate !== "") ? $data->endDate : NULL,
        ]);

        $poster = $this->posterHandler($data->image, $season, config('custom.seasonOriginalFolder'));
        if ($poster == NULL) {
            $season->fill(['poster_id', NULL]);
        } else {
            $season->posters()->save($poster);
            $freshPoster = $poster->fresh();
            $season->update(['poster_id' => $freshPoster->id]);
        }

        $season->show()->associate($this->show);
        $season->save();

        return $season;
    }
}
