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
}
