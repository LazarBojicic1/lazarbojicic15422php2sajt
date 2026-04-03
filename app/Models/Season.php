<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = [
        'title_id',
        'tmdb_id',
        'season_number',
        'name',
        'overview',
        'poster_path',
        'air_date',
        'episode_count',
    ];

    protected function casts(): array
    {
        return [
            'air_date' => 'date',
        ];
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
}
