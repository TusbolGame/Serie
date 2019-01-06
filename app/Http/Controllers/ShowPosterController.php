<?php

namespace App\Http\Controllers;

use App\ShowPoster;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShowPosterController extends Controller {
    public function newImage($url, $show) {
        $allowedExtensions = ['jpg', 'jpeg', 'JPG', 'JPEG'];
        $image = $this->downloadImage($url, config('custom.posterOriginalFolder'));
        if ($image['response_code'] != 0) {
            if (!in_array($image['extension'], $allowedExtensions)) {
                // TODO Convert file from other format to jpg
            }
            $latestPoster = ShowPoster::where('show_id', $show->id)->orderBy('created_at', 'desc')->first();

            if ($latestPoster == null) {
                $poster = $this->saveNewPoster($show, $image['name']);
            } else {
                $oldFileName = config('custom.posterOriginalFolder') . $latestPoster->name . '.jpg';
                if (md5_file($image['path']) != md5_file($oldFileName)) {
                    $poster = $this->saveNewPoster($show, $image['name']);
                } else {
                    unlink($image['path']);     // Delete downloaded image if already exists in database
                    $poster = 0;
                }
            }
            return $poster;
        } else {
            return 1;                           // Network Error
        }
    }

    private function saveNewPoster($show, $name) {
        $poster = new ShowPoster();

        $poster->name = $name;

        return $poster;
    }

    public function downloadImage($url, $path) {
        $urlExtension = pathinfo($url, PATHINFO_EXTENSION);
        $name = Str::orderedUuid()->toString();
        $completePath = $path . $name . '.' . $urlExtension;

        $file = fopen($completePath, 'w');
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url, ['save_to' => $file]);
        return ['response_code'=>$response->getStatusCode(), 'path' => $completePath, 'name' => $name, 'extension' => $urlExtension];
    }
}
