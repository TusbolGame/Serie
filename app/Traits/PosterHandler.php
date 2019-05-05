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

        if ($poster == NULL || $poster === 0 ||  $poster === 1 ||  $poster === 2 ||  $poster === 3 ||  $poster === 4) {
                // case NULL: Unknown error
                // case 0: File is already present on disk
                // case 1: Response error / Network error
                // case 2: Save path not created or issues
                // case 3: Failed to convert the image
                // case 4: Failed to convert the image
            $poster = NULL;
        }
        return $poster;
    }
}
