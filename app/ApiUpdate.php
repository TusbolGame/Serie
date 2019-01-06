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
        'show_id', 'data_update_id', 'api_updated_at'
    ];

    public function show() {
        return $this->belongsTo(Show::class);
    }

    public function dataUpdate() {
        return $this->belongsTo(DataUpdate::class);
    }
}
