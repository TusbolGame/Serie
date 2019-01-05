<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShowPoster extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'show_id', 'name'
    ];

    public function show() {
        return $this->belongsTo(Show::class);
    }
}
