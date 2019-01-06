<?php

namespace App\Http\Controllers;

use App\Poster;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PosterController extends Controller {
    public function newImage($url, $owner, $folderRoot) {
        $allowedExtensions = ['jpg', 'jpeg', 'JPG', 'JPEG'];
        $image = $this->downloadImage($url, $folderRoot);

        if ($image['response_code'] == 0) {
            return 1;                       // Network Error
        }
        if (!in_array($image['extension'], $allowedExtensions)) {
            // TODO Convert file from other format to jpg
        }

        if ($owner->id != NULL) {
            $latestPoster = Poster::where([
                'posterable_type' => $owner->getTable(),
                'posterable_id' => $owner->id
            ])->orderBy('created_at', 'desc')->first();
        } else {                   // The owner either does not yet exist or has a new resource that wasn't present before
            $latestPoster = NULL;
        }

        if ($latestPoster == NULL) {
            $poster = $this->saveNewPoster($image['name']);
        } else {
            $oldFileName = $folderRoot . $latestPoster->name . '.jpg';
            if (md5_file($image['path']) != md5_file($oldFileName)) {
                $poster = $this->saveNewPoster($image['name']);
            } else {
                unlink($image['path']);     // Delete downloaded image if already exists in database
                $poster = 0;
            }
        }
        return $poster;
    }

    private function saveNewPoster($name) {
        $poster = new Poster();
        $poster->name = $name;

        return $poster;
    }

    private function downloadImage($url, $path) {
        $urlExtension = pathinfo($url, PATHINFO_EXTENSION);
        $name = Str::orderedUuid()->toString();
        $completePath = $path . $name . '.' . $urlExtension;

        $file = fopen($completePath, 'w');
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url, ['save_to' => $file]);
        return ['response_code'=>$response->getStatusCode(), 'path' => $completePath, 'name' => $name, 'extension' => $urlExtension];
    }
}
