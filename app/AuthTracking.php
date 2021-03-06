<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthTracking extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'type_id', 'ip', 'useragent'
    ];

    public function authType() {
        return $this->belongsTo(AuthType::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
