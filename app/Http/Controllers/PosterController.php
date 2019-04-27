<?php

namespace App\Http\Controllers;

use App\OldFile;
use App\Poster;
use App\TempPoster;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PosterController extends Controller {
    public function newImage($url, $owner, $folderRoot) {
        $allowedExtensions = ['jpg', 'jpeg', 'JPG', 'JPEG'];
        $image = $this->downloadImage($url, $folderRoot);
        if ($image == 0) {
            return 2;                       // Folder creation error
        }

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
                $result = unlink($image['path']);     // Delete downloaded image if already exists in database
                if ($result == FALSE) {               // If delete failed, file added to database to be deleted later
                    TempPoster::firstOrCreate([
                        'path' => $image['path'],
                        'outcome' => $image['path'],
                    ]);
                }
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

        if ($this->makeDirectory($path)) {   // if path existing or created
            $file = fopen($completePath, 'w');
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url, ['save_to' => $file]);
            return ['response_code' => $response->getStatusCode(), 'path' => $completePath, 'name' => $name, 'extension' => $urlExtension];
        } else {                // if path not created or issues
            return 0;
        }
    }

    private function makeDirectory($path) {
        if (!is_dir($path)){
            //Directory does not exist, so lets create it.
            return mkdir($path, 0777, true);
        } else {
            return TRUE;
        }
    }
}
