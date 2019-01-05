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
        'name', 'alternative_name', 'api_id', 'api_link', 'rating', 'imdb_link',
        'imdb_vote', 'description', 'language', 'network_id', 'running_time', 'content_rating_id',
        'status_id', 'timezone', 'banner', 'poster'
    ];

    public function network() {
        return $this->hasOne(Network::class);
    }

    public function contentRating() {
        return $this->hasOne(ContentRating::class);
    }

    public function status() {
        return $this->hasOne(Status::class);
    }

    public function season() {
        return $this->hasMany(Season::class);
    }
}
