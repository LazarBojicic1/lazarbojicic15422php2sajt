<?php

namespace App\Http\Controllers;

use App\Models\Title;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Title::where('is_published', true)
            ->whereNotNull('backdrop_path')
            ->where('vote_average', '>=', 7)
            ->inRandomOrder()
            ->with('genres')
            ->first();

        $trending = Title::where('is_published', true)
            ->orderByDesc('popularity')
            ->limit(20)
            ->get();

        $popularMovies = Title::where('is_published', true)
            ->where('tmdb_type', 'movie')
            ->orderByDesc('popularity')
            ->limit(20)
            ->get();

        $popularSeries = Title::where('is_published', true)
            ->where('tmdb_type', 'tv')
            ->orderByDesc('popularity')
            ->limit(20)
            ->get();

        $topRated = Title::where('is_published', true)
            ->where('vote_count', '>=', 100)
            ->orderByDesc('vote_average')
            ->limit(20)
            ->get();

        $recentMovies = Title::where('is_published', true)
            ->where('tmdb_type', 'movie')
            ->whereNotNull('release_date')
            ->orderByDesc('release_date')
            ->limit(20)
            ->get();

        return view('home', compact(
            'featured', 'trending', 'popularMovies', 'popularSeries', 'topRated', 'recentMovies'
        ));
    }
}
