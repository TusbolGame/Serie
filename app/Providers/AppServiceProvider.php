<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
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
