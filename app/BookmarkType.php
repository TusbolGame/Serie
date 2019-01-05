<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookmarkType extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function bookmark() {
        return $this->hasMany(Bookmark::class);
    }
}
