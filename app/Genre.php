<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'image'
    ];

    public function shows() {
        return $this->belongsToMany(Show::class);
    }
}
