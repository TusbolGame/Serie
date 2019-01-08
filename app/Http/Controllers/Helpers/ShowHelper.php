<?php

namespace App\Http\Controllers\Helpers;

use App\ApiUpdate;
use App\ContentRating;
use App\Genre;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NetworkController;
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

    /**
     * @param {int} $api_ID - the show thet is being updated
     * @param {object} $dataUpdate DataUpdate object - the dataUpdate that started this update
     * @param {array} $options - array of options:
     *  $options['type'] => 0 = new show, 1 = check apiUpdate, 2 = force update all fields
     * @return
     */
    public function updateData($api_ID, $dataUpdate, $rawData, $options = []) {
        $defaults = [
            'airing_time' => '00:00',
            'timezone' => 'UTC',
        ];
        $show = new Show();

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
            $network = $networkHandler->createUpdate($networkData);
        } else if ($rawData->webChannel != "" && $rawData->webChannel != NULL) {
            $networkData = $rawData->webChannel;
            $network = $networkHandler->createUpdate($networkData);
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
            $genreIDs = [];
            foreach ($rawData->genres as $dataGenre) {
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

        return ['show' => $show->fresh(), 'serverData' => $rawData->_embedded];   // an update has been performed
    }
}
