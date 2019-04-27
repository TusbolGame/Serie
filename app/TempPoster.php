<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempPoster extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path', 'outcome'
    ];
}
