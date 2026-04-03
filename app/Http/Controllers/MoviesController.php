<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Title;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
    public function index(Request $request)
    {
        $query = Title::where('is_published', true)
            ->where('tmdb_type', 'movie')
            ->with('genres');

        if ($request->filled('genre')) {
            $query->whereHas('genres', fn ($q) => $q->where('genres.id', $request->genre));
        }

        if ($request->filled('year')) {
            $query->whereYear('release_date', $request->year);
        }

        $sort = $request->get('sort', 'popular');
        $query = match ($sort) {
            'rating' => $query->where('vote_count', '>=', 50)->orderByDesc('vote_average'),
            'newest' => $query->orderByDesc('release_date'),
            'oldest' => $query->orderBy('release_date'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('popularity'),
        };

        $titles = $query->paginate(30)->withQueryString();

        $genres = Genre::whereHas('titles', fn ($q) => $q->where('tmdb_type', 'movie')->where('is_published', true))
            ->orderBy('name')
            ->get();

        $years = Title::where('is_published', true)
            ->where('tmdb_type', 'movie')
            ->whereNotNull('release_date')
            ->selectRaw('YEAR(release_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $featured = Title::where('is_published', true)
            ->where('tmdb_type', 'movie')
            ->whereNotNull('backdrop_path')
            ->where('vote_average', '>=', 7.5)
            ->orderByDesc('popularity')
            ->first();

        return view('movies', compact('titles', 'genres', 'years', 'featured'));
    }
}
