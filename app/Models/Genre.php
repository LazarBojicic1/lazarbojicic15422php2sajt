<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = [
        'tmdb_id',
        'name',
    ];

    public function titles()
    {
        return $this->belongsToMany(Title::class);
    }
}
