<?php

namespace App\Http\Controllers;

use App\ApiUpdate;
use App\ContentRating;
use App\DataUpdate;
use App\Episode;
use App\Season;
use App\Show;
use App\Status;
use App\Traits\PosterHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class DataUpdateController extends Controller {
    use PosterHandler;

    private $dataUpdate;
    private $updateLimitDays = 20;
    private $updateEndedLimitDays = 100;

    public function updateManager($type, $api_id = NULL, $uuid = NULL) {
        if ($type == 0) {                   // Update all shows
            $options = [
                'type' => 0
            ];
        } else if ($type == 1) {            // Update shows where last update $this->updateLimitDays days ago
            $options = [
                'type' => 1
            ];
        } else if ($type == 2) {            // Update a specific show
            $options = [
                'type' => 2,
                'uuid' => $uuid
            ];
        } else if ($type == 3) {            // New show
            $options = [
                'type' => 3,
                'api_id' => $api_id,
            ];
        } else {
            return AjaxErrorController::response("The update type is not valid.", 409);
        }
        $this->dataUpdate = DataUpdate::create([
            'type' => $type
        ]);
        $results = $this->updateController($options);

        if ($results == -1) {
            return AjaxErrorController::response("Wrong update type.", 409);
        } else if ($results == -2) {
            return AjaxErrorController::response("The show is already present in the database.", 409);
        }

        $this->dataUpdate->finished_at = Carbon::now()->toDateTimeString();
        $this->dataUpdate->save();
        return AjaxSuccessController::response("Update successful", $results);
    }

    public function updateController($mainOptions) {
        if ($mainOptions['type'] == 3) {
            return $this->addShow($mainOptions['api_id']);
        } else {
            if ($mainOptions['type'] == 0) {
                $showList = Show::all();
                $options = [];
            } else if ($mainOptions['type'] == 1) {
                $limitDate = new Carbon($this->updateLimitDays . ' days ago midnight');
                $showList = Show::with('status')->where('updated_at', '<', $limitDate->getTimestamp())->get();
                $options = [];
            } else if ($mainOptions['type'] == 2) {
                $showList = Show::where('uuid', $mainOptions['uuid'])->get();
                $options = [];
            } else {
                return -1;
            }

            // TODO Add event to notify the number of shows to be updated
            $results = [];
            foreach ($showList as $show) {
                $results[] = $this->updateHandler($show, $options);
            }
        }

        return $results;
    }

    private function addShow($api_id) {
        $showCheck = Show::where('api_id', $api_id)->first();

        if ($showCheck != NULL) {
            return -2;
        }

        $show = new Show();
        $show->fill([
            'uuid' => Str::orderedUuid()->toString()
        ]);

        $link = $this->apiHandler(1, ['api_id' => $api_id]);
        $rawData = $this->fetchData($link);

        $results = $this->dataSaver($show, $rawData);
        $addedShow = $show->fresh();
        return $addedShow->uuid;
    }

    private function updateHandler($show, $options = []) {
        if (isset($show->status) && $show->status->name == 'Ended') {
            $type = 0;
            $limitDate = new Carbon($this->updateEndedLimitDays . ' days ago midnight');
            $lastUpdateCheck = ApiUpdate::select('api_updated_at')->where([
                'show_id' => $show->id,
            ])->orderBy('api_updated_at', 'desc')->value('api_updated_at')->first();     // Get last time this show was updated

            $lastUpdate = $first = Carbon::createFromTimestamp($lastUpdateCheck->api_updated_at);

            if ($lastUpdateCheck == NULL || $lastUpdate->lessThan($limitDate)) {        // If the show was last updated more than $this->updateEndedLimitDays days ago
                $type = 1;
            }
        } else {
            $type = 1;
        }
        $link = $this->apiHandler($type, ['api_id' => $show->api_id]);
        if ($link == NULL) {
            return -4;
        }

        $rawData = $this->fetchData($link);

        if (is_int($rawData) && $rawData == 0) {
            return -1;
        }
        if (is_int($rawData) && $rawData == 1) {
            return -2;
        }
        if (is_int($rawData) && $rawData == 2) {
            return -3;
        }

        $apiUpdateCheck = ApiUpdate::where([
            'show_id' => $show->id,
            'api_updated_at' => Carbon::createFromTimestamp($rawData->updated)->toDateTimeString(),
        ])->first();

        if ($apiUpdateCheck != NULL) {
            return 0;   // No update needed
        }
        return $this->dataSaver($show, $rawData);
    }

    private function dataSaver($show, $rawData) {
        $result = [];
        $this->saveShowData($show, $rawData);
        if (isset($rawData->_embedded) && $rawData->_embedded != "") {
            $result['seasons'] = $this->saveSeasonData($show, $rawData->_embedded->seasons);
            $result['episodes'] = $this->saveEpisodeData($show, $rawData->_embedded->episodes);
        }

        return $result;
    }

    private function apiHandler($type, $info) {
        $apiBaseURL = 'http://api.tvmaze.com/';
        switch ($type) {
            case 0:         // fetch minimal show info
                $link = $apiBaseURL . "shows/" . $info['api_id'] . "";
                break;
            case 1:         // fetch complete show info (with episodes and seasons)
                $link = $apiBaseURL . "shows/" . $info['api_id'] . "?embed[]=episodes&embed[]=seasons";
                break;
            case 2:         // search show by string
                $link = $apiBaseURL . "search/shows?q=" . $info['show_name'] . "";
                break;
            default:
                return NULL;
                break;
        }

        return $link;
    }

    private function fetchData($link) {
        $apiClient = new Client([
            'base_uri' => $link,
            'timeout'  => 60.0,
        ]);

        try {
            $apiResponse = $apiClient->request('GET');
        } catch (ClientException $e) {
            return 1;
        }

        if ($apiResponse->getStatusCode() !== 200) {
            return 2;
        }

        $response = $apiResponse->getBody()->getContents();

        if ($response == '') {
            return 0;
        }

        return json_decode($response);
    }

    public function searchShowController($show_name) {
        if (trim($show_name) == '') {
            return AjaxErrorController::response("The show name is not valid.", 409);
        }

        $link = $this->apiHandler(2, ['show_name' => $show_name]);
        $rawData = $this->fetchData($link);

        if (is_int($rawData) && $rawData == 0) {
            return AjaxErrorController::response("Network response invalid: empty.", 409);
        }
        if (is_int($rawData) && $rawData == 1) {
            return AjaxErrorController::response("GuzzleHttp api exception.", 409);
        }
        if (is_int($rawData) && $rawData == 2) {
            return AjaxErrorController::response("Network response error code.", 409);
        }

        if (empty($rawData)) {
            return AjaxSuccessController::response("No shows found", []);
        }

        $results = [];
        foreach ($rawData as $key => $show) {
            $results[] = $this->searchShowHandler($show);
        }

        if (empty($results)) {
            return AjaxSuccessController::response("No shows found", []);
        }

        return AjaxSuccessController::response("Search successful", $results);
    }

    private function searchShowHandler($rawData) {
        $show = [
            'name' => $rawData->show->name,
            'score' => $rawData->score,
            'api_id' => $rawData->show->id,
            'api_link' => $rawData->show->url,
            'api_rating' => $rawData->show->rating->average,
            'description' => strip_tags($rawData->show->summary),
            'language' => $rawData->show->language,
            'running_time' => $rawData->show->runtime,
            'genres' => $rawData->show->genres,
        ];

        if (isset($rawData->show->network)) {
            $show['network'] = $rawData->show->network->name;
        } else if (isset($rawData->show->webChannel)) {
            $show['network'] = $rawData->show->webChannel->name;
        } else {
            $show['network'] = NULL;
        }

        $show['poster'] = isset($rawData->show->image) ? $rawData->show->image->medium : NULL;

        return $show;
    }

    private function saveShowData($show, $rawData, $options = []) {
        $defaults = [
            'airing_time' => '00:00',
            'timezone' => 'UTC',
        ];

        $apiUpdate = ApiUpdate::create([
            'api_updated_at' => Carbon::createFromTimestamp($rawData->updated)->toDateTimeString(),
        ]);

        $show->fill([
            'name' => $rawData->name,
            'api_id' => $rawData->id,
            'api_link' => $rawData->url,
            'api_rating' => $rawData->rating->average,
            'description' => strip_tags($rawData->summary),
            'language' => $rawData->language,
            'running_time' => $rawData->runtime,
        ]);


        $status = Status::firstOrCreate([
            'name' => $rawData->status
        ]);

        $show->status()->associate($status);

        $networkHandler = new NetworkController();
        $networkType = ['network', 'webChannel'];
        if ($rawData->network != "" && $rawData->network != NULL) {
            $networkData = $rawData->network;
            $network = $networkHandler->createUpdate($networkData, 0);
        } else if ($rawData->webChannel != "" && $rawData->webChannel != NULL) {
            $networkData = $rawData->webChannel;
            $network = $networkHandler->createUpdate($networkData, 1);
        } else {
            $network = $networkHandler->returnDefaultNetwork();
        }
        $show->network()->associate($network);

        $show->fill([
            'airing_time' => ($rawData->schedule->time !== "") ? $rawData->schedule->time : $defaults['airing_time'],
            'timezone' => ($rawData->{$networkType[$network->type]}->country != NULL) ? $rawData->{$networkType[$network->type]}->country->timezone : $defaults['timezone'],
            'imdb_link' => ($rawData->externals->imdb != NULL) ? "http://www.imdb.com/title/" . $rawData->externals->imdb . "/" : NULL,
        ]);


        if ($show->imdb_link != NULL) {
            $imdbClient = new Client([
                'base_uri' => $show->imdb_link,
                'timeout' => 20.0,
                'verify' => false,
            ]);

            $imdbResponse = $imdbClient->request('GET');
            $crawler = new Crawler($imdbResponse->getBody()->getContents());

            $imdbVoteResult = $crawler->filter('.ratingValue span[itemprop="ratingValue"]');
            if ($imdbVoteResult->count() !== 0) {
                $imdbVote = $imdbVoteResult->text();
                $imdbVote = preg_match('/(\d)?\d(.)\d/', $imdbVote, $imdbVoteMatches);
                if ($imdbVote == 1) {
                    $show->imdb_rating = (preg_replace('/,/', '.', $imdbVoteMatches[0]));
                }
            }

            // TODO re-do the content rating parsing logic: wrong results (see table)
            $check = preg_match('/[\n\s]*(.*)?[\n\s]*?</', $crawler->filter('.subtext')->html(), $imdbContentRatingMatches);
            if ($check == 1) {
                $contentRating = ContentRating::firstOrCreate([
                    'name' => $imdbContentRatingMatches[1],
                ]);

                $show->contentRating()->associate($contentRating);
            }
        }

        $poster = $this->posterHandler($rawData->image, $show, config('custom.posterOriginalFolder'));
        if ($poster == NULL) {
            $show->fill(['poster_id', NULL]);
        }

        if (!empty($rawData->genres)) {
            $genreController = new GenreController();
            $genreIDs = $genreController->genreHandler($rawData->genres);
        }



        $show->save();
        $apiUpdate->show()->associate($show);
        $apiUpdate->dataUpdate()->associate($this->dataUpdate);
        $apiUpdate->save();

        if ($poster != NULL) {
            $show->posters()->save($poster);
            $freshPoster = $poster->fresh();
            $show->update(['poster_id' => $freshPoster->id]);
        }
        if (isset($genreIDs) && is_array($genreIDs)) {
            $show->genres()->sync($genreIDs, false);
        }

        return $show->fresh();   // an update has been performed
    }

    /* Season Handling */

    private function saveSeasonData($show, $rawData) {
        return $this->updateSeasonData($show, $rawData);
    }

    private function updateSeasonData($show, $rawData) {
        if (!isset($rawData) || $rawData == "") {
            return 0;
        }

        $newSeasons = new Collection();
        foreach ($rawData as $seasonData) {
            $season = $this->addSeasonData($show, $seasonData);
            if ($season !== NULL) {
                $newSeasons->push($season);
            }
        }
        if ($newSeasons->count() == 0) {
            return NULL;
        }
        return $newSeasons;
    }

    private function addSeasonData($show, $rawData) {
        $flagNewSeason = FALSE;
        $seasonCheck = Season::where('api_id', $rawData->id)->first();

        if ($seasonCheck == NULL) {
            $season = Season::create([
                'uuid' => Str::orderedUuid()->toString(),
                'api_id' => $rawData->id,
                'season' => $rawData->number,
                'episodes' => ($rawData->episodeOrder !== NULL) ? $rawData->episodeOrder : NULL,
                'date_start' => ($rawData->premiereDate !== NULL && $rawData->premiereDate !== "") ? $rawData->premiereDate : NULL,
                'date_end' => ($rawData->endDate !== NULL && $rawData->endDate !== "") ? $rawData->endDate : NULL,
            ]);
            $flagNewSeason = TRUE;
        } else {
            $season = $seasonCheck;
            $season->update([
                'season' => $rawData->number,
                'episodes' => ($rawData->episodeOrder !== NULL) ? $rawData->episodeOrder : NULL,
                'date_start' => ($rawData->premiereDate !== NULL && $rawData->premiereDate !== "") ? $rawData->premiereDate : NULL,
                'date_end' => ($rawData->endDate !== NULL && $rawData->endDate !== "") ? $rawData->endDate : NULL,
            ]);
        }
        $season->save();

        $poster = $this->posterHandler($rawData->image, $season, config('custom.seasonOriginalFolder'));
        if ($poster == NULL) {
            $season->fill(['poster_id', NULL]);
        } else {
            $season->posters()->save($poster);
            $freshPoster = $poster->fresh();
            $season->update(['poster_id' => $freshPoster->id]);
        }

        $season->show()->associate($show);
        $season->save();

        if ($flagNewSeason === TRUE) {
            return $season->fresh();
        } else {
            return NULL;
        }
    }

    /* Episode Handling */

    private function saveEpisodeData($show, $rawData) {
        return $this->updateEpisodeData($show, $rawData);
    }

    private function updateEpisodeData($show, $rawData) {
        if (!isset($rawData) || $rawData == "") {
            return 0;
        }
        $newEpisodes = new Collection();
        foreach ($rawData as $episodeData) {
            $episode = $this->addEpisodeData($show, $episodeData);
            if ($episode !== NULL) {
                $newEpisodes->push($episode);
            }
        }
        if ($newEpisodes->count() == 0) {
            return NULL;
        }
        return $newEpisodes;
    }

    private function addEpisodeData($show, $rawData) {
        $episodeController = new EpisodeController();
        $flagNewEpisode = FALSE;
        $episodeCheck = Episode::where('api_id', $rawData->id)->first();

        if ($episodeCheck == NULL) {
            $episode = Episode::create([
                'uuid' => Str::orderedUuid()->toString(),
                'api_id' => $rawData->id,
                'api_link' => $rawData->url,
                'episode_number' => $rawData->number,
                'episode_code' => $episodeController->codeGenerator($rawData->season, $rawData->number),
                'title' => $rawData->name,
                'summary' => strip_tags($rawData->summary),
            ]);

            $flagNewEpisode = TRUE;
        } else {
            $episode = $episodeCheck;
            $episode->update([
                'api_link' => $rawData->url,
                'episode_number' => $rawData->number,
                'episode_code' => $episodeController->codeGenerator($rawData->season, $rawData->number),
                'title' => $rawData->name,
                'summary' => strip_tags($rawData->summary),
            ]);
        }

        $airstamp = NULL;
        if (isset($rawData->airstamp) && $rawData->airstamp != "") {
            $airstamp = \DateTime::createFromFormat(\DateTime::W3C, $rawData->airstamp);
            if ($airstamp == FALSE) {
                $airstamp = NULL;
            }
        }
        $episode->fill(['airing_at' => $airstamp]);
        $episode->save();

        $poster = $this->posterHandler($rawData->image, $episode, config('custom.episodeOriginalFolder'));
        if ($poster == NULL) {
            $episode->fill(['poster_id', NULL]);
        } else {
            $episode->posters()->save($poster);
            $freshPoster = $poster->fresh();
            $episode->update(['poster_id' => $freshPoster->id]);
        }

        $seasonCheck = Season::where([
            'show_id' => $show->id,
            'season' => $rawData->season,
        ])->first();

        if ($seasonCheck != NULL) {
            $episode->season()->associate($seasonCheck);
        }

        $episode->show()->associate($show);
        $episode->save();

        if ($flagNewEpisode === TRUE) {
            return $episode->fresh();
        } else {
            return NULL;
        }
    }
}
