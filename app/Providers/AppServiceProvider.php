<?php

namespace App\Providers;

use App\Episode;
use App\Events\EpisodeCreated;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Relation::morphMap([
            'App\Show',
            'App\Season',
            'App\Episode',
        ]);

//        Episode::created(function ($item) {
//            Event::fire(new EpisodeCreated(Auth::user(), $item));
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }
}
