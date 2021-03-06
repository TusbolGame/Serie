<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthType extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function authTrackings() {
        return $this->hasMany(AuthTracking::class, 'type_id');
    }
}
