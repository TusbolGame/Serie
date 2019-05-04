<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\ImageHelper;
use App\Poster;
use App\TempPoster;
use Illuminate\Support\Str;
use Whoops\Exception\ErrorException;

class PosterController extends Controller {
    private $image = [];

    public function newImage($url, $owner, $folderRoot) {
        $result = $this->downloadImage($url);
        if ($result == 0) {
            return 2;                       // Folder creation error
        }

        if ($result['response_code'] == 0) {
            return 1;                       // Network Error
        }
        if (!in_array($this->image['extension'], ImageHelper::ALLOWED_EXTENSIONS)) {
            $newImagePath = $folderRoot . $this->image['name'] . '.jpg';
            $imageConverter = new ImageHelper();
            $conversionResult = $imageConverter->convertImage($this->image['path'], $newImagePath);

            if ($conversionResult == TRUE) {
                $this->image['path'] = $newImagePath;
                $this->image['extension'] = 'jpg';
            } else {
                return 3;                   // Failed to convert the image
            }
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
            $poster = $this->saveNewPoster($this->image['name']);

            $newImagePath = $folderRoot . $this->image['name'] . '.jpg';
            rename($this->image['path'], $newImagePath);
        } else {
            $oldFileName = $folderRoot . $latestPoster->name . '.jpg';

            if (!file_exists($oldFileName)) {
                $latestPoster->delete();
                $poster = $this->saveNewPoster($this->image['name']);

                $newImagePath = $folderRoot . $this->image['name'] . '.jpg';
                rename($this->image['path'], $newImagePath);
            } else if (md5_file($this->image['path']) != md5_file($oldFileName)) {
                $poster = $this->saveNewPoster($this->image['name']);

                $newImagePath = $folderRoot . $this->image['name'] . '.jpg';
                rename($this->image['path'], $newImagePath);
            } else {
                // TODO Explore possibility/feasibility of adding a "md5" Poster column to check current file against all files
                if (file_exists($this->image['path'])) {
//                    TempPoster::firstOrCreate([
//                        'path' => $this->image['path'],
//                        'outcome' => $this->image['path'],
//                    ]);
                    try {
                        // TODO Solve the unlink issue (Resource temporarily unavailable)
                        if (!unlink($this->image['path'])) {               // Delete downloaded image if already exists in database. If delete failed, file added to database to be deleted later
                            TempPoster::firstOrCreate([
                                'path' => $this->image['path'],
                                'outcome' => $this->image['path'],
                            ]);
                            throw new \ErrorException('The file counld not be deleted');
                        }
                    } catch (ErrorException $e) {
                        TempPoster::firstOrCreate([
                            'path' => $this->image['path'],
                            'outcome' => $this->image['path'],
                        ]);
                        return 4;
                    }
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

    private function downloadImage($url) {
        $this->image['extension'] = pathinfo($url, PATHINFO_EXTENSION);
        $this->image['name'] = Str::orderedUuid()->toString();
        $this->image['path'] = config('custom.imgTempFolder') . $this->image['name'] . '.' . $this->image['extension'];

        if ($this->makeDirectory(config('custom.imgTempFolder'))) {   // if path existing or created
            $file = fopen($this->image['path'], 'w');
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url, ['save_to' => $file]);
            fclose($file);
            return ['response_code' => $response->getStatusCode()];
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
