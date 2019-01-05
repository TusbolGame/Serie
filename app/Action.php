<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'video_view_id', 'action_type_id'
    ];

    public function videoView() {
        return $this->belongsTo(VideoView::class);
    }

    public function actionType() {
        return $this->belongsTo(ActionType::class);
    }
}
