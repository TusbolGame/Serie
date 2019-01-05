<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'video_view_id', 'bookmark_type_id', 'time'
    ];

    public function bookmarkType() {
        return $this->belongsTo(BookmarkType::class);
    }

    public function videoView() {
        return $this->belongsTo(VideoView::class);
    }
}
