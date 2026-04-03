<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = [
        'season_id',
        'tmdb_id',
        'episode_number',
        'name',
        'overview',
        'still_path',
        'air_date',
        'runtime',
        'vote_average',
        'vote_count',
    ];

    protected function casts(): array
    {
        return [
            'air_date' => 'date',
            'vote_average' => 'decimal:2',
        ];
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
