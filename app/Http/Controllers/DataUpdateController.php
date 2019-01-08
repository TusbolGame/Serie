<?php

namespace App\Http\Controllers;

use App\ApiUpdate;
use App\ContentRating;
use App\DataUpdate;
use App\Genre;
use App\Http\Controllers\Helpers\ShowHelper;
use App\Show;
use App\Status;
use App\Traits\PosterHandler;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DataUpdateController extends Controller {
    use PosterHandler;

    private $dataUpdate;
    private $updateLimitDays = 1;

    public function __construct() {
        $this->dataUpdate = DataUpdate::create([
            'type' => 0
        ]);
    }

    private function dataSaver($show, $dataUpdate, $rawData) {
        $result = [];
        $result['show'] = $this->saveShowData($show, $this->dataUpdate, $rawData);
        if (isset($rawData->_embedded) && $rawData->_embedded != "") {
            // TODO Save the $this->dataupdate id to the episode table too to track their update??
            $this->saveEpisodeData($show, $rawData);
        }
    }

    public function updateController($mainOptions) {
        if ($mainOptions['type'] == 3) {
            $show = new Show();
            $rawData = $this->fetchData($show->api_id, 'everything');

            $results = $this->dataSaver($show, $this->dataUpdate, $rawData);

        } else {
            if ($mainOptions['type'] == 0) {
                $showList = Show::all();
                $options = [];
            } else if ($mainOptions['type'] == 1) {
                $limitDate = new Carbon($this->updateLimitDays . ' days ago midnight');
                $showList = Show::with('status')->where('updated_at', '<', $limitDate->getTimestamp());
                $options = [];
            } else if ($mainOptions['type'] == 2) {
                $showList = Show::where('uuid', $mainOptions['stream'])->get();
                $options = [];
            } else {
                // TODO Handle error on wrong update type
                return FALSE;
            }

            // TODO Add event to notify the number of shows to be upodated
            $results = [];
            foreach ($showList as $show) {
                $results[] = $this->updateHandler($show, $options);
            }
        }
    }

    public function updateHandler($show, $options = []) {
        if ($show->status->name == 'Ended') {
            $rawData = $this->fetchData($show->api_id, 'minimum');
        } else {
            $rawData = $this->fetchData($show->api_id, 'everything');
        }
        if ($rawData == 0) {
            return -1;
        }
        if ($rawData == 1) {
            return -2;
        }

        $apiUpdateCheck = ApiUpdate::where([
            'show_id' => $show->id,
            'api_updated_at' => Carbon::createFromTimestamp($rawData->updated)->toDateTimeString(),
        ])->first();

        if ($apiUpdateCheck != NULL) {
            return 0;   // No update needed
        }
        return $this->dataSaver($show, $this->dataUpdate, $rawData);
    }

    private function fetchData($api_ID, $type) {
        if ($type == 'minimum') {
            $api_link = "http://api.tvmaze.com/shows/" . $api_ID . "";
        } else if ($type == 'everything') {
            $api_link = "http://api.tvmaze.com/shows/" . $api_ID . "?embed[]=episodes&embed[]=seasons";
        } else {
            return 0;
        }

        $apiClient = new Client([
            'base_uri' => $api_link,
            'timeout'  => 60.0,
        ]);

        $apiResponse = $apiClient->request('GET');

        if ($apiResponse->getStatusCode() !== 200) {
            return 1;
        }

        return json_decode($apiResponse->getBody()->getContents());
    }

    /**
     * @param {int} $api_ID - the show thet is being updated
     * @param {object} $dataUpdate DataUpdate object - the dataUpdate that started this update
     * @param {array} $options - array of options:
     *  $options['type'] => 0 = new show, 1 = check apiUpdate, 2 = force update all fields
     * @return
     */
    public function saveShowData($show, $rawData, $options = []) {
        $defaults = [
            'airing_time' => '00:00',
            'timezone' => 'UTC',
        ];

        $apiUpdate = ApiUpdate::create([
            'api_updated_at' => Carbon::createFromTimestamp($rawData->updated)->toDateTimeString(),
        ]);

        $show->fill([
            'uuid' => Str::orderedUuid()->toString(),
            'name' => $rawData->name,
            'api_id' => $rawData->id,
            'api_link' => $rawData->url,
            'api_rating' => (int)($rawData->rating->average * 10),
            'description' => $rawData->summary,
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
            $crawler = new \Symfony\Component\DomCrawler\Crawler($imdbResponse->getBody()->getContents());

            $imdbVote = $crawler->filter('.ratingValue span[itemprop="ratingValue"]')->text();
            $imdbVote = preg_match('/(\d)?\d(.)\d/', $imdbVote, $imdbVoteMatches);
            if ($imdbVote == 1) {
                $show->imdb_vote = (int)(preg_replace('/,/', '.', $imdbVoteMatches[0])) * 10;
            }

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
            $show->genres()->sync($genreIDs);
        }

        return $show->fresh();   // an update has been performed
    }
}
