<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Episode extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'show_id', 'season_id', 'episode_number', 'episode_code', 'airing_at', 'title',
        'api_id', 'api_link', 'summary', 'poster_id'
    ];

    public function show() {
        return $this->belongsTo(Show::class);
    }

    public function season() {
        return $this->belongsTo(Season::class);
    }

    public function videoView() {
        return $this->hasMany(VideoView::class);
    }

    public function posters() {
        return $this->morphMany(Poster::class, 'posterable');
    }

    public function unwatched() {
        return DB::table('episodes')
            ->where('airing_at', '!=', NULL);
    }
}
