<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoView extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'episode_id', 'torrent_id', 'ended_at'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function episode() {
        return $this->belongsTo(Episode::class);
    }

    public function torrent() {
        return $this->belongsTo(Torrent::class);
    }

    public function bookmark() {
        return $this->hasMany(Bookmark::class);
    }
}
