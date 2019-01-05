<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'show_id', 'season_id', 'episode_number', 'episode_code', 'airing_at', 'title',
        'api_id', 'api_link', 'synopsis', 'screenshot'
    ];

    public function show() {
        return $this->hasOne(Show::class);
    }

    public function season() {
        return $this->hasOne(Season::class);
    }

    public function videoView() {
        return $this->hasMany(VideoView::class);
    }
}
