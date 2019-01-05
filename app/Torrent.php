<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Torrent extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'episode_id', 'file_name', 'magnet_link', 'file_size', 'used', 'deleted',
        'video_quality_id', 'started_at', 'finished_at', 'converted_at'
    ];

    public function episode() {
        return $this->belongsTo(Episode::class);
    }

    public function videoQuality() {
        return $this->hasOne(VideoQuality::class);
    }
}
