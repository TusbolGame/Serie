<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Network extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'country_code', 'country_name', 'link', 'banner'
    ];

    public function show() {
        return $this->hasMany(Show::class);
    }
}
