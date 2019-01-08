<?php

namespace App\Http\Controllers;

use App\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller {
    public function genreHandler($genreArray) {
        $genreIDs = [];
        foreach ($genreArray as $dataGenre) {
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
        return $genreIDs;
    }
}
