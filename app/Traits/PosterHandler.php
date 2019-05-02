<?php

namespace App\Traits;


use App\Http\Controllers\PosterController;

trait PosterHandler {
    public function posterHandler($data, $owner, $folder) {
        if ($data == NULL || $data->original == NULL) {
            return NULL;
        }
        $posterController = new PosterController();
        $poster = $posterController->newImage($data->original, $owner, $folder);

        switch ($poster) {
            case NULL:          // Unknown error
                $poster = NULL;
                break;
            case 0:             // File is already present on disk
                $poster = NULL;
                break;
            case 1:             // Response error / Network error
                $poster = NULL;
                break;
            case 2:             // Save path not created or issues
                $poster = NULL;
                break;
            case 3:             // Failed to convert the image
                $poster = NULL;
                break;
            default:
                return $poster;

        }
    }
}
