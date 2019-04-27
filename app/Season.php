<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Season extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'show_id', 'api_id', 'season', 'episodes', 'date_start', 'date_end', 'poster_id'
    ];

    public function show() {
        return $this->belongsTo(Show::class);
    }

    public function episode() {
        return $this->hasMany(Episode::class);
    }

    public function posters() {
        return $this->morphMany(Poster::class, 'posterable');
    }
}
