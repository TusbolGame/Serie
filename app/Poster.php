<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poster extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'posterable_id', 'posterable_type', 'name'
    ];

    /**
     * Get all of the owning imageable models.
     */
    public function posterable() {
        return $this->morphTo();
    }
}
