<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'show_id', 'value'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function show() {
        return $this->belongsTo(Show::class);
    }
}
