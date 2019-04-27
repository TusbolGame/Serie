<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'email_verified_at', 'password', 'firstname', 'lastname', 'privileges', 'role', 'timezone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function authTracking() {
        return $this->hasMany(AuthTracking::class);
    }

    public function shows() {
        return $this->belongsToMany(Show::class);
    }

    public function videoView() {
        return $this->hasMany(AuthTracking::class);
    }
}
