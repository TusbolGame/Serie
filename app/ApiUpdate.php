<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiUpdate extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'show_id', 'update_id', 'api_update_at'
    ];

    public function show() {
        return $this->belongsToMany(Show::class);
    }

    public function update() {
        return $this->belongsTo(Update::class);
    }
}
