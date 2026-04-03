<?php

namespace App\Http\Controllers;

use App\Models\SearchLog;
use App\Models\Title;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->input('q', ''));
        $titles = Title::query()->whereRaw('1 = 0')->paginate(24)->withQueryString();
        $searchLog = null;

        if (mb_strlen($query) >= 2) {
            $titles = $this->baseSearchQuery($query)
                ->paginate(24)
                ->withQueryString();

            $searchLog = $this->recordSubmittedSearch($request, $query, $titles->total());
        }

        $userFavoriteIds = Auth::check()
            ? Auth::user()->favorites()->pluck('title_id')->all()
            : [];

        return view('search', [
            'query' => $query,
            'titles' => $titles,
            'searchLog' => $searchLog,
            'userFavoriteIds' => $userFavoriteIds,
        ]);
    }

    public function suggest(Request $request)
    {
        $query = trim($request->input('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $titles = $this->baseSearchQuery($query)
            ->limit(10)
            ->get([
                'id',
                'name',
                'slug',
                'poster_path',
                'tmdb_type',
                'vote_average',
                'release_date',
                'first_air_date',
            ]);

        $results = $titles->map(fn (Title $title) => [
            'id' => $title->id,
            'name' => $title->name,
            'slug' => $title->slug,
            'poster' => $title->poster_path
                ? 'https://image.tmdb.org/t/p/w92' . $title->poster_path
                : null,
            'type' => $title->tmdb_type === 'tv' ? 'Series' : 'Movie',
            'rating' => $title->vote_average ? number_format($title->vote_average, 1) : null,
            'year' => $title->tmdb_type === 'tv'
                ? ($title->first_air_date?->format('Y'))
                : ($title->release_date?->format('Y')),
        ]);

        return response()->json(['results' => $results]);
    }

    public function log(Request $request)
    {
        $payload = $request->validate([
            'search_log_id' => ['nullable', 'integer', 'exists:search_logs,id'],
            'q' => ['nullable', 'string', 'max:255'],
            'selected_title_id' => ['required', 'integer', 'exists:titles,id'],
        ]);

        $selectedTitleId = (int) $payload['selected_title_id'];
        $searchLogId = $payload['search_log_id'] ?? null;

        if ($searchLogId) {
            $searchLog = SearchLog::find($searchLogId);

            if ($searchLog && $searchLog->selected_title_id === null) {
                $searchLog->update([
                    'selected_title_id' => $selectedTitleId,
                ]);
            }

            return response()->json(['status' => 'ok']);
        }

        $query = trim((string) ($payload['q'] ?? ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['status' => 'ok']);
        }

        $resultsCount = $this->baseSearchQuery($query)->count();

        SearchLog::create([
            'user_id' => Auth::id(),
            'query' => mb_substr($query, 0, 255),
            'results_count' => $resultsCount,
            'selected_title_id' => $selectedTitleId,
            'searched_at' => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function baseSearchQuery(string $query): Builder
    {
        return Title::query()
            ->where('is_published', true)
            ->where(function ($builder) use ($query) {
                $builder
                    ->where('name', 'LIKE', '%' . $query . '%')
                    ->orWhere('original_name', 'LIKE', '%' . $query . '%');
            })
            ->orderByRaw(
                "CASE
                    WHEN name LIKE ? THEN 0
                    WHEN original_name LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    WHEN original_name LIKE ? THEN 3
                    ELSE 4
                END",
                [$query, $query, $query.'%', $query.'%']
            )
            ->orderByDesc('popularity');
    }

    private function recordSubmittedSearch(Request $request, string $query, int $resultsCount): SearchLog
    {
        $signature = sha1(mb_strtolower($query).'|'.$resultsCount);
        $lastSearch = $request->session()->get('search.last');

        if (
            is_array($lastSearch)
            && ($lastSearch['signature'] ?? null) === $signature
            && isset($lastSearch['logged_at'], $lastSearch['id'])
            && now()->diffInSeconds($lastSearch['logged_at']) < 30
        ) {
            $existing = SearchLog::find($lastSearch['id']);

            if ($existing) {
                return $existing;
            }
        }

        $searchLog = SearchLog::create([
            'user_id' => Auth::id(),
            'query' => mb_substr($query, 0, 255),
            'results_count' => $resultsCount,
            'selected_title_id' => null,
            'searched_at' => now(),
        ]);

        $request->session()->put('search.last', [
            'id' => $searchLog->id,
            'signature' => $signature,
            'logged_at' => now()->toIso8601String(),
        ]);

        return $searchLog;
    }
}
