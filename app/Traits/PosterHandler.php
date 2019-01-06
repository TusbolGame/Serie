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

        if ($poster == NULL || $poster === 0 ||  $poster === 1) {
            $poster = NULL;
        }

        return $poster;
    }
}
