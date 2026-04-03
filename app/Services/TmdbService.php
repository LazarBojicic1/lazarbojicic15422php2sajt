<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TmdbService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
        $this->baseUrl = config('services.tmdb.base_url');
    }

    public function getPopularMovies(int $page = 1): array
    {
        return $this->get('/movie/popular', ['page' => $page]);
    }

    public function getMoviesByCategory(string $category, int $page = 1): array
    {
        return $this->get("/movie/{$category}", ['page' => $page]);
    }

    public function getPopularTv(int $page = 1): array
    {
        return $this->get('/tv/popular', ['page' => $page]);
    }

    public function getTvByCategory(string $category, int $page = 1): array
    {
        return $this->get("/tv/{$category}", ['page' => $page]);
    }

    public function getMovieDetails(int $id, array $params = []): array
    {
        return $this->get("/movie/{$id}", $params);
    }

    public function getTvDetails(int $id, array $params = []): array
    {
        return $this->get("/tv/{$id}", $params);
    }

    public function getTvExternalIds(int $id): array
    {
        return $this->get("/tv/{$id}/external_ids");
    }

    public function getTvSeason(int $tvId, int $seasonNumber): array
    {
        return $this->get("/tv/{$tvId}/season/{$seasonNumber}");
    }

    public function getMovieGenres(): array
    {
        return $this->get('/genre/movie/list');
    }

    public function getTvGenres(): array
    {
        return $this->get('/genre/tv/list');
    }

    public function searchTitles(string $query, string $type = 'multi', int $page = 1): array
    {
        $endpoint = match ($type) {
            'movie' => '/search/movie',
            'tv' => '/search/tv',
            default => '/search/multi',
        };

        return $this->get($endpoint, [
            'query' => $query,
            'page' => $page,
        ]);
    }

    private function get(string $endpoint, array $params = []): array
    {
        $params['api_key'] = $this->apiKey;

        $response = Http::get("{$this->baseUrl}{$endpoint}", $params);

        if ($response->failed()) {
            throw new \RuntimeException("TMDB API request failed: {$endpoint} - {$response->status()}");
        }

        return $response->json();
    }
}
