<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Genre;
use App\Models\ImportLog;
use App\Models\Season;
use App\Models\Title;
use Illuminate\Support\Str;

class TmdbTitleImporter
{
    public function __construct(private readonly TmdbService $tmdb)
    {
    }

    public function syncGenres(): void
    {
        $movieGenres = $this->tmdb->getMovieGenres();
        $tvGenres = $this->tmdb->getTvGenres();

        $allGenres = collect($movieGenres['genres'] ?? [])
            ->merge($tvGenres['genres'] ?? [])
            ->unique('id');

        foreach ($allGenres as $genre) {
            Genre::updateOrCreate(
                ['tmdb_id' => $genre['id']],
                ['name' => $genre['name']]
            );
        }
    }

    public function importByTmdbId(
        int $tmdbId,
        string $tmdbType,
        int $seasonLimit = 1,
        ?int $adminUserId = null,
        bool $isPublished = true,
        ?string $action = null,
    ): Title {
        $this->syncGenres();

        return match ($tmdbType) {
            'movie' => $this->importMovie($tmdbId, $adminUserId, $isPublished, $action ?? 'import_movie'),
            'tv' => $this->importTv($tmdbId, $seasonLimit, $adminUserId, $isPublished, $action ?? 'import_tv'),
            default => throw new \InvalidArgumentException('Unsupported TMDb type [' . $tmdbType . '].'),
        };
    }

    private function importMovie(int $tmdbId, ?int $adminUserId, bool $isPublished, string $action): Title
    {
        $details = $this->tmdb->getMovieDetails($tmdbId);
        $slug = Str::slug($details['title'] ?? $details['original_title'] ?? 'movie') . '-' . $tmdbId;

        $title = Title::updateOrCreate(
            ['tmdb_id' => $tmdbId, 'tmdb_type' => 'movie'],
            [
                'imdb_id' => $details['imdb_id'] ?? null,
                'slug' => $slug,
                'name' => $details['title'] ?? $details['original_title'] ?? 'Untitled Movie',
                'original_name' => $details['original_title'] ?? null,
                'overview' => $details['overview'] ?? null,
                'poster_path' => $details['poster_path'] ?? null,
                'backdrop_path' => $details['backdrop_path'] ?? null,
                'release_date' => ! empty($details['release_date']) ? $details['release_date'] : null,
                'first_air_date' => null,
                'last_air_date' => null,
                'runtime' => $details['runtime'] ?? null,
                'number_of_seasons' => null,
                'number_of_episodes' => null,
                'status' => $details['status'] ?? null,
                'original_language' => $details['original_language'] ?? null,
                'country' => data_get($details, 'production_countries.0.iso_3166_1'),
                'vote_average' => $details['vote_average'] ?? 0,
                'vote_count' => $details['vote_count'] ?? 0,
                'popularity' => $details['popularity'] ?? 0,
                'adult' => $details['adult'] ?? false,
                'is_published' => $isPublished,
                'synced_at' => now(),
            ]
        );

        $this->syncTitleGenres($title, $details['genres'] ?? []);
        $this->recordImportLog(
            $tmdbId,
            'movie',
            $action,
            'success',
            sprintf('%s (%s)', $title->name, $title->wasRecentlyCreated ? 'created' : 'updated'),
            $adminUserId,
        );

        return $title;
    }

    private function importTv(int $tmdbId, int $seasonLimit, ?int $adminUserId, bool $isPublished, string $action): Title
    {
        $details = $this->tmdb->getTvDetails($tmdbId, ['append_to_response' => 'external_ids']);
        $slug = Str::slug($details['name'] ?? $details['original_name'] ?? 'series') . '-' . $tmdbId;

        $title = Title::updateOrCreate(
            ['tmdb_id' => $tmdbId, 'tmdb_type' => 'tv'],
            [
                'imdb_id' => data_get($details, 'external_ids.imdb_id'),
                'slug' => $slug,
                'name' => $details['name'] ?? $details['original_name'] ?? 'Untitled Series',
                'original_name' => $details['original_name'] ?? null,
                'overview' => $details['overview'] ?? null,
                'poster_path' => $details['poster_path'] ?? null,
                'backdrop_path' => $details['backdrop_path'] ?? null,
                'release_date' => null,
                'first_air_date' => ! empty($details['first_air_date']) ? $details['first_air_date'] : null,
                'last_air_date' => ! empty($details['last_air_date']) ? $details['last_air_date'] : null,
                'runtime' => null,
                'number_of_seasons' => $details['number_of_seasons'] ?? null,
                'number_of_episodes' => $details['number_of_episodes'] ?? null,
                'status' => $details['status'] ?? null,
                'original_language' => $details['original_language'] ?? null,
                'country' => data_get($details, 'production_countries.0.iso_3166_1'),
                'vote_average' => $details['vote_average'] ?? 0,
                'vote_count' => $details['vote_count'] ?? 0,
                'popularity' => $details['popularity'] ?? 0,
                'adult' => $details['adult'] ?? false,
                'is_published' => $isPublished,
                'synced_at' => now(),
            ]
        );

        $this->syncTitleGenres($title, $details['genres'] ?? []);
        $this->importSeasons($title, $details, $seasonLimit);
        $this->recordImportLog(
            $tmdbId,
            'tv',
            $action,
            'success',
            sprintf('%s (%s)', $title->name, $title->wasRecentlyCreated ? 'created' : 'updated'),
            $adminUserId,
        );

        return $title;
    }

    private function syncTitleGenres(Title $title, array $genres): void
    {
        $genreIds = Genre::whereIn('tmdb_id', collect($genres)->pluck('id')->filter()->all())
            ->pluck('id')
            ->all();

        $title->genres()->sync($genreIds);
    }

    private function importSeasons(Title $title, array $details, int $seasonLimit): void
    {
        foreach ($details['seasons'] ?? [] as $seasonData) {
            if (($seasonData['season_number'] ?? 0) === 0) {
                continue;
            }

            $season = Season::updateOrCreate(
                ['tmdb_id' => $seasonData['id']],
                [
                    'title_id' => $title->id,
                    'season_number' => $seasonData['season_number'],
                    'name' => $seasonData['name'] ?? 'Season ' . $seasonData['season_number'],
                    'overview' => $seasonData['overview'] ?? null,
                    'poster_path' => $seasonData['poster_path'] ?? null,
                    'air_date' => ! empty($seasonData['air_date']) ? $seasonData['air_date'] : null,
                    'episode_count' => $seasonData['episode_count'] ?? 0,
                ]
            );

            if ($seasonLimit > 0 && $seasonData['season_number'] <= $seasonLimit) {
                $this->importEpisodes($title, $season, (int) $seasonData['season_number']);
            }
        }
    }

    private function importEpisodes(Title $title, Season $season, int $seasonNumber): void
    {
        $seasonDetails = $this->tmdb->getTvSeason($title->tmdb_id, $seasonNumber);

        foreach ($seasonDetails['episodes'] ?? [] as $episodeData) {
            Episode::updateOrCreate(
                ['tmdb_id' => $episodeData['id']],
                [
                    'season_id' => $season->id,
                    'episode_number' => $episodeData['episode_number'],
                    'name' => $episodeData['name'] ?? 'Episode ' . $episodeData['episode_number'],
                    'overview' => $episodeData['overview'] ?? null,
                    'still_path' => $episodeData['still_path'] ?? null,
                    'air_date' => ! empty($episodeData['air_date']) ? $episodeData['air_date'] : null,
                    'runtime' => $episodeData['runtime'] ?? null,
                    'vote_average' => $episodeData['vote_average'] ?? 0,
                    'vote_count' => $episodeData['vote_count'] ?? 0,
                ]
            );
        }
    }

    private function recordImportLog(
        int $tmdbId,
        string $tmdbType,
        string $action,
        string $status,
        ?string $message,
        ?int $adminUserId,
    ): void {
        ImportLog::create([
            'admin_user_id' => $adminUserId,
            'tmdb_id' => $tmdbId,
            'tmdb_type' => $tmdbType,
            'action' => $action,
            'status' => $status,
            'message' => $message,
        ]);
    }
}
