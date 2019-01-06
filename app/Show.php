<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Show extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'name', 'alternative_name', 'api_id', 'api_link', 'api_rating', 'imdb_link',
        'imdb_vote', 'description', 'language', 'network_id', 'running_time', 'airing_time', 'content_rating_id',
        'status_id', 'timezone', 'banner', 'show_poster_id'
    ];

    public function network() {
        return $this->belongsTo(Network::class);
    }

    public function contentRating() {
        return $this->belongsTo(ContentRating::class);
    }

    public function status() {
        return $this->belongsTo(Status::class);
    }

    public function season() {
        return $this->hasMany(Season::class);
    }

    public function episode() {
        return $this->hasMany(Episode::class);
    }

    public function rating() {
        return $this->hasMany(Rating::class);
    }

    public function genres() {
        return $this->belongsToMany(Genre::class);
    }

    public function apiUpdate() {
        return $this->hasMany(ApiUpdate::class);
    }

    public function showPoster() {
        return $this->hasMany(ShowPoster::class);
    }
}
