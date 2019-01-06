<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataUpdate extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'finished_at'
    ];

    public function apiUpdate() {
        return $this->hasMany(ApiUpdate::class);
    }
}
