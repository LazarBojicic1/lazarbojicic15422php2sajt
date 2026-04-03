<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Title;
use App\Services\TmdbService;
use App\Services\TmdbTitleImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TitleController extends Controller
{
    public function index(Request $request)
    {
        $titles = Title::query()
            ->with(['genres'])
            ->withCount(['favorites', 'comments', 'seasons'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->input('q'));

                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('original_name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('imdb_id', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('type'), fn ($query) => $query->where('tmdb_type', $request->input('type')))
            ->when($request->filled('published'), function ($query) use ($request) {
                if ($request->input('published') === 'yes') {
                    $query->where('is_published', true);
                }

                if ($request->input('published') === 'no') {
                    $query->where('is_published', false);
                }
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.titles.index', compact('titles'));
    }

    public function create(Request $request, TmdbService $tmdb)
    {
        $query = trim((string) $request->input('q', ''));
        $searchType = $request->input('type', 'multi');
        $searchType = in_array($searchType, ['multi', 'movie', 'tv'], true) ? $searchType : 'multi';
        $seasonLimit = max(0, min(5, (int) $request->input('season_limit', 1)));
        $searchResults = collect();
        $searchError = null;

        return view('admin.titles.create', [
            'query' => $query,
            'searchType' => $searchType,
            'seasonLimit' => $seasonLimit,
            'searchResults' => $this->buildSearchResults($tmdb, $query, $searchType, $searchError),
            'searchError' => $searchError,
        ]);
    }

    public function store(Request $request, TmdbTitleImporter $importer)
    {
        $data = $request->validate([
            'tmdb_id' => ['required', 'integer', 'min:1'],
            'tmdb_type' => ['required', Rule::in(['movie', 'tv'])],
            'season_limit' => ['nullable', 'integer', 'min:0', 'max:5'],
        ]);

        try {
            $title = $importer->importByTmdbId(
                $data['tmdb_id'],
                $data['tmdb_type'],
                (int) ($data['season_limit'] ?? 1),
                $request->user()->id,
                $request->boolean('is_published', true),
                'admin_import',
            );
        } catch (\Throwable $e) {
            return back()
                ->withErrors([
                    'tmdb_search' => 'TMDb import failed: ' . $e->getMessage(),
                ])
                ->withInput();
        }

        return redirect()
            ->route('admin.titles.edit', $title)
            ->with('message', $title->wasRecentlyCreated
                ? 'Title imported from TMDb successfully.'
                : 'Existing title synced from TMDb successfully.');
    }

    public function edit(Title $title)
    {
        $title->load(['genres', 'seasons.episodes']);
        $title->loadCount(['favorites', 'comments', 'seasons']);
        $genres = Genre::query()->orderBy('name')->get();

        return view('admin.titles.edit', compact('title', 'genres'));
    }

    public function update(Request $request, Title $title)
    {
        $data = $this->validatedData($request, $title);
        $genreIds = $data['genres'] ?? [];
        unset($data['genres']);

        $title->update($data);
        $title->genres()->sync($genreIds);

        return redirect()
            ->route('admin.titles.edit', $title)
            ->with('message', 'Title updated successfully.');
    }

    public function destroy(Title $title)
    {
        $title->delete();

        return redirect()
            ->route('admin.titles.index')
            ->with('message', 'Title deleted successfully.');
    }

    private function validatedData(Request $request, ?Title $title = null): array
    {
        $validated = $request->validate([
            'tmdb_id' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('titles', 'tmdb_id')
                    ->where(fn ($query) => $query->where('tmdb_type', $request->input('tmdb_type')))
                    ->ignore($title?->id),
            ],
            'tmdb_type' => ['required', Rule::in(['movie', 'tv'])],
            'imdb_id' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('titles', 'slug')->ignore($title?->id)],
            'name' => ['required', 'string', 'max:255'],
            'original_name' => ['nullable', 'string', 'max:255'],
            'overview' => ['nullable', 'string'],
            'poster_path' => ['nullable', 'string', 'max:255'],
            'backdrop_path' => ['nullable', 'string', 'max:255'],
            'release_date' => ['nullable', 'date'],
            'first_air_date' => ['nullable', 'date'],
            'last_air_date' => ['nullable', 'date'],
            'runtime' => ['nullable', 'integer', 'min:0'],
            'number_of_seasons' => ['nullable', 'integer', 'min:0'],
            'number_of_episodes' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'max:255'],
            'original_language' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:10'],
            'vote_average' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'vote_count' => ['nullable', 'integer', 'min:0'],
            'popularity' => ['nullable', 'numeric', 'min:0'],
            'synced_at' => ['nullable', 'date'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['integer', 'exists:genres,id'],
        ]);

        $validated['adult'] = $request->boolean('adult');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['synced_at'] = $validated['synced_at'] ?? ($title?->synced_at ?? now());

        return $validated;
    }

    private function buildSearchResults(TmdbService $tmdb, string $query, string $searchType, ?string &$searchError): \Illuminate\Support\Collection
    {
        if ($query === '' || mb_strlen($query) < 2) {
            return collect();
        }

        try {
            $payload = $tmdb->searchTitles($query, $searchType);
        } catch (\Throwable $e) {
            $searchError = 'TMDb search failed: ' . $e->getMessage();

            return collect();
        }

        $rawResults = collect($payload['results'] ?? [])
            ->filter(function (array $item) use ($searchType) {
                $itemType = $item['media_type'] ?? $searchType;

                return in_array($itemType, ['movie', 'tv'], true);
            })
            ->values();

        if ($rawResults->isEmpty()) {
            return collect();
        }

        $existingTitles = Title::query()
            ->where(function ($queryBuilder) use ($rawResults) {
                foreach ($rawResults as $item) {
                    $queryBuilder->orWhere(function ($innerQuery) use ($item) {
                        $innerQuery
                            ->where('tmdb_id', $item['id'])
                            ->where('tmdb_type', $item['media_type'] ?? 'movie');
                    });
                }
            })
            ->get()
            ->keyBy(fn (Title $title) => $title->tmdb_type . '-' . $title->tmdb_id);

        return $rawResults->map(function (array $item) use ($existingTitles, $searchType) {
            $tmdbType = $item['media_type'] ?? $searchType;
            $releaseDate = $item['release_date'] ?? $item['first_air_date'] ?? null;
            $year = filled($releaseDate) ? Str::before($releaseDate, '-') : null;
            $existingTitle = $existingTitles->get($tmdbType . '-' . $item['id']);

            return [
                'tmdb_id' => $item['id'],
                'tmdb_type' => $tmdbType,
                'name' => $item['title'] ?? $item['name'] ?? 'Untitled',
                'original_name' => $item['original_title'] ?? $item['original_name'] ?? null,
                'overview' => $item['overview'] ?? null,
                'poster_path' => $item['poster_path'] ?? null,
                'year' => $year,
                'vote_average' => $item['vote_average'] ?? null,
                'existing_title' => $existingTitle,
            ];
        });
    }
}
