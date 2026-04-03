<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Title;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index(Request $request)
    {
        $query = Title::where('is_published', true)
            ->where('tmdb_type', 'tv')
            ->with('genres');

        if ($request->filled('genre')) {
            $query->whereHas('genres', fn ($q) => $q->where('genres.id', $request->genre));
        }

        if ($request->filled('year')) {
            $query->whereYear('first_air_date', $request->year);
        }

        $sort = $request->get('sort', 'popular');
        $query = match ($sort) {
            'rating' => $query->where('vote_count', '>=', 50)->orderByDesc('vote_average'),
            'newest' => $query->orderByDesc('first_air_date'),
            'oldest' => $query->orderBy('first_air_date'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('popularity'),
        };

        $titles = $query->paginate(30)->withQueryString();

        $genres = Genre::whereHas('titles', fn ($q) => $q->where('tmdb_type', 'tv')->where('is_published', true))
            ->orderBy('name')
            ->get();

        $years = Title::where('is_published', true)
            ->where('tmdb_type', 'tv')
            ->whereNotNull('first_air_date')
            ->selectRaw('YEAR(first_air_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $featured = Title::where('is_published', true)
            ->where('tmdb_type', 'tv')
            ->whereNotNull('backdrop_path')
            ->where('vote_average', '>=', 7.5)
            ->orderByDesc('popularity')
            ->first();

        return view('series', compact('titles', 'genres', 'years', 'featured'));
    }
}
