<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentRating extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'icon'
    ];

    public function show() {
        return $this->hasMany(Show::class);
    }
}
