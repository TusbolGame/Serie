<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoQuality extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'priority'
    ];

    public function torrent() {
        return $this->hasMany(Torrent::class);
    }
}
