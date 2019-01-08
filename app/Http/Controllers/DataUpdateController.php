<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataUpdateController extends Controller {
    public function update() {
        $array = Show::all();
        foreach($array as $show) {
            echo $show->name . ' Started</br>';
            $showHelper = new ShowHelper();
            if ($options['type'] == 0) {
                $result = $showHelper->updateData($show->api_id, $dataUpdate);
                echo 'Show done in (' . (microtime(true) - $single) . ') </br>';
                $single = microtime(true);

                if ($result !== 0) {
                    $seasonHelper = new SeasonHelper();
                    $seasonResult = $seasonHelper->updateData($result['show'], $result['serverData']->seasons);
                    echo 'Season Ended</br>';
                    echo 'Season done in (' . (microtime(true) - $single) . ') </br>';
                    $single = microtime(true);

                    $episodeHelper = new EpisodeHelper();
                    $episodeResult = $episodeHelper->updateData($result['show'], $result['serverData']->episodes);
                    echo 'Episode Ended</br>';
                    echo 'Episode done in (' . (microtime(true) - $single) . ') </br>';
                    $single = microtime(true);
                }
            }
            echo (microtime(true) - $start) . '</br>';
            $start = microtime(true);
        }
    }



    private function fetchData($api_ID, $type) {
        if ($type == NULL) {
            $api_link = "http://api.tvmaze.com/shows/" . $api_ID . "";
        } else if ($type == TRUE) {
            $api_link = "http://api.tvmaze.com/shows/" . $api_ID . "?embed[]=episodes&embed[]=seasons";
        }

        $apiClient = new Client([
            'base_uri' => $api_link,
            'timeout'  => 60.0,
        ]);

        $apiResponse = $apiClient->request('GET');

        if ($apiResponse->getStatusCode() !== 200) {
            return [0, $apiResponse->getStatusCode()];
        }

        return json_decode($apiResponse->getBody()->getContents());
    }

}
