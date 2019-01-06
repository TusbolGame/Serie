<?php

namespace App\Http\Controllers\Helpers;

use App\ApiUpdate;
use App\ContentRating;
use App\Genre;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PosterController;
use App\Network;
use App\Show;
use App\Status;
use App\Traits\PosterHandler;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ShowHelper extends Controller {
    use PosterHandler;

    public function updateData($api_ID, $dataUpdate) {
        $defaults = [
            'airing_time' => '00:00',
            'timezone' => 'UTC',
        ];

        $data = $this->fetchData($api_ID);

        $showCheck = Show::where(['api_id' => $api_ID])->first();
        $apiUpdateCheck = FALSE;
        if ($showCheck == NULL) {
            $show = new Show();
        } else {
            $apiUpdateCheck = ApiUpdate::where([
                'show_id' => $showCheck->id,
                'api_updated_at' => Carbon::createFromTimestamp($data->updated)->toDateTimeString(),
            ])->first();

            if ($apiUpdateCheck != NULL) {
                return 0;   // No update needed
            }

            $show = $showCheck;
        }

        $apiUpdate = ApiUpdate::create([
            'api_updated_at' => Carbon::createFromTimestamp($data->updated)->toDateTimeString(),
        ]);

        $show->fill([
            'uuid' => Str::orderedUuid()->toString(),
            'name' => $data->name,
            'api_id' => $data->id,
            'api_link' => $data->url,
            'api_rating' => (int)($data->rating->average * 10),
            'description' => $data->summary,
            'language' => $data->language,
            'running_time' => $data->runtime,
        ]);


        $status = Status::firstOrCreate([
            'name' => $data->status
        ]);

        $show->status()->associate($status);

        $network = new Network();
        $networkType = ['network', 'webChannel'];
        if ($data->network != "") {
            $network->fill(['type' => 0]);
        } else if ($data->webChannel != "") {
            $network->fill(['type' => 1]);
        } else {
            // TODO Check what needs to be done if none of the previous options are set (currently the API has no alternatives)
        }

        $network->fill([
            'name' => ($data->{$networkType[$network->type]}->name != NULL) ? $data->{$networkType[$network->type]}->name : 'No Network Available',
            'country_code' => ($data->{$networkType[$network->type]}->country != NULL) ? $data->{$networkType[$network->type]}->country->code : NULL,
            'country_name' => ($data->{$networkType[$network->type]}->country != NULL) ? $data->{$networkType[$network->type]}->country->name : NULL,
        ]);

        if (Network::where(['name' => $network->name])->first() == null) {
            $network->save();
        } else {
            $network = Network::where(['name' => $network->name])->first();
            $network->update([
                'type' => $network->type,
                'country_code' => $network->country_code,
                'country_name' => $network->country_name,
            ]);
            $network->fresh();
        }
        $show->network()->associate($network);

        $show->fill([
            'airing_time' => ($data->schedule->time !== "") ? $data->schedule->time : $defaults['airing_time'],
            'timezone' => ($data->{$networkType[$network->type]}->country != NULL) ? $data->{$networkType[$network->type]}->country->timezone : $defaults['timezone'],
            'imdb_link' => ($data->externals->imdb != NULL) ? "http://www.imdb.com/title/" . $data->externals->imdb . "/" : NULL,
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

        $poster = $this->posterHandler($data->image, $show, config('custom.posterOriginalFolder'));
        if ($poster == NULL) {
            $show->fill(['poster_id', NULL]);
        }

        if (!empty($data->genres)) {
            $genreIDs = [];
            foreach ($data->genres as $dataGenre) {
                $genre = Genre::where(['name' => $dataGenre])->first();
                if ($genre == NULL) {       // Add new genres only if they don't exist (it's useless to update the pivot table or the name of the genre if it exists
                    $newGenre = Genre::Create([
                        'name' => strtolower($dataGenre)
                    ]);
                    $newGenre = $newGenre->fresh();

                    $genreIDs[] = $newGenre->id;
                } else {
                    $genreIDs[] = $genre->id;
                }
            }
        }

        $show->save();
        $apiUpdate->show()->associate($show);
        $apiUpdate->dataUpdate()->associate($dataUpdate);
        $apiUpdate->save();

        if ($poster != NULL) {
            $show->posters()->save($poster);
            $freshPoster = $poster->fresh();
            $show->update(['poster_id' => $freshPoster->id]);
        }
        if (isset($genreIDs) && is_array($genreIDs)) {
            $show->genres()->sync($genreIDs);
        }

        return ['show' => $show->fresh(), 'serverData' => $data->_embedded];   // an update has been performed
    }

    private function fetchData($api_ID) {
        $api_link = "http://api.tvmaze.com/shows/" . $api_ID . "?embed[]=episodes&embed[]=seasons";

        $apiClient = new Client([
            'base_uri' => $api_link,
            'timeout'  => 20.0,
        ]);

        $apiResponse = $apiClient->request('GET');

        if ($apiResponse->getStatusCode() !== 200) {
            return [0, $apiResponse->getStatusCode()];
        }

        return json_decode($apiResponse->getBody()->getContents());
    }
}
