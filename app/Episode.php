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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * Torrent status details:
     * - 5 => Unusable, avoid
     * - 0 => Default, just added to DB
     * - 1 => Started downloading
     * - 2 => Finished downloading
     * - 3 => Converted
     * - 4 => Used
     */
    public function torrent() {
        return $this->hasMany(Torrent::class)->orderByRaw('FIELD(status, 4, 3, 2, 1, 0, 5)');
    }

    public function posters() {
        return $this->morphMany(Poster::class, 'posterable');
    }
}
