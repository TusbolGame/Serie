<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionType extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'group'
    ];

    public function streamAction() {
        return $this->hasMany(Action::class);
    }
}
