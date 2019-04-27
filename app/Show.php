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
        'imdb_rating', 'description', 'language', 'network_id', 'running_time', 'airing_time', 'content_rating_id',
        'status_id', 'timezone', 'banner', 'poster_id'
    ];

    public function network() {
        return $this->belongsTo(Network::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
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

    public function unwatched() {
        return $this->episode()->where('airing_at', '<', 'NOW()')->orderBy('airing_at', 'asc');
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

    public function posters() {
        return $this->morphMany(Poster::class, 'posterable');
    }
}
