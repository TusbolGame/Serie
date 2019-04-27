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
        'episode_id', 'file_name', 'hash', 'file_size', 'used', 'deleted', 'status',
        'video_quality_id', 'started_at', 'finished_at', 'converted_at'
    ];

    public function episode() {
        return $this->belongsTo(Episode::class);
    }

    public function videoQuality() {
        return $this->belongsTo(VideoQuality::class);
    }

    public function videoView() {
        return $this->hasMany(VideoView::class);
    }
}
