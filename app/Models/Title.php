<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $fillable = [
        'tmdb_id',
        'tmdb_type',
        'imdb_id',
        'slug',
        'name',
        'original_name',
        'overview',
        'poster_path',
        'backdrop_path',
        'release_date',
        'first_air_date',
        'last_air_date',
        'runtime',
        'number_of_seasons',
        'number_of_episodes',
        'status',
        'original_language',
        'country',
        'vote_average',
        'vote_count',
        'popularity',
        'adult',
        'is_published',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'first_air_date' => 'date',
            'last_air_date' => 'date',
            'synced_at' => 'datetime',
            'adult' => 'boolean',
            'is_published' => 'boolean',
            'vote_average' => 'decimal:2',
            'popularity' => 'decimal:2',
        ];
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function titleViews()
    {
        return $this->hasMany(TitleView::class);
    }

    public function searchSelections()
    {
        return $this->hasMany(SearchLog::class, 'selected_title_id');
    }
}
