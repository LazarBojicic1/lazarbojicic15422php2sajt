<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\TitleView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchController extends Controller
{
    public function show($slug, Request $request)
    {
        $title = Title::where('slug', $slug)
            ->where('is_published', true)
            ->with(['genres', 'seasons.episodes'])
            ->firstOrFail();

        [$season, $episode] = $this->normalizeWatchSelection(
            $title,
            (int) $request->query('s', 1),
            (int) $request->query('e', 1),
        );

        $embedSources = $this->getEmbedSources($title, $season, $episode);
        $embedUrl = $embedSources[0]['url'] ?? null;

        $related = Title::where('is_published', true)
            ->where('id', '!=', $title->id)
            ->where('tmdb_type', $title->tmdb_type)
            ->inRandomOrder()
            ->limit(12)
            ->get();

        return view('watch', compact('title', 'embedUrl', 'embedSources', 'related', 'season', 'episode'));
    }

    public function trackView(Request $request)
    {
        $request->validate(['title_id' => 'required|exists:titles,id']);

        $ipHash = hash('sha256', $request->ip());
        $userId = Auth::id();
        $titleId = $request->title_id;

        // Prevent duplicate views within 1 hour for the same user/ip
        $recent = TitleView::where('title_id', $titleId)
            ->where('viewed_at', '>=', now()->subHour())
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn ($q) => $q->where('ip_hash', $ipHash)->whereNull('user_id'))
            ->exists();

        if (!$recent) {
            TitleView::create([
                'title_id' => $titleId,
                'user_id' => $userId,
                'ip_hash' => $ipHash,
                'viewed_at' => now(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function getEmbedSources(Title $title, int $season = 1, int $episode = 1): array
    {
        return collect(config('services.streaming.providers', []))
            ->map(function (array $provider) use ($title, $season, $episode) {
                $url = $this->buildProviderUrl($title, $provider, $season, $episode);

                if (blank($url)) {
                    return null;
                }

                return [
                    'key' => $provider['key'] ?? md5($url),
                    'label' => $provider['label'] ?? 'Player',
                    'priority' => $this->getProviderPriority($title, $provider),
                    'url' => $url,
                ];
            })
            ->filter()
            ->sortBy('priority')
            ->unique('url')
            ->values()
            ->map(fn (array $source) => [
                'key' => $source['key'],
                'label' => $source['label'],
                'url' => $source['url'],
            ])
            ->all();
    }

    private function buildProviderUrl(Title $title, array $provider, int $season, int $episode): ?string
    {
        $idType = $provider['id'] ?? 'tmdb';
        $id = $idType === 'imdb' ? $title->imdb_id : $title->tmdb_id;

        if (blank($id)) {
            return null;
        }

        $template = $title->tmdb_type === 'movie'
            ? ($provider['movie'] ?? null)
            : ($provider['tv'] ?? null);

        if (blank($template)) {
            return null;
        }

        return strtr($template, [
            '{id}' => (string) $id,
            '{season}' => (string) $season,
            '{episode}' => (string) $episode,
        ]);
    }

    private function getProviderPriority(Title $title, array $provider): int
    {
        $priority = $provider['priority'] ?? 100;

        if (! is_array($priority)) {
            return (int) $priority;
        }

        $mediaType = $title->tmdb_type === 'tv' ? 'tv' : 'movie';

        return (int) ($priority[$mediaType] ?? 100);
    }

    private function normalizeWatchSelection(Title $title, int $season, int $episode): array
    {
        if ($title->tmdb_type !== 'tv') {
            return [1, 1];
        }

        $season = max(1, $season);
        $episode = max(1, $episode);

        $availableSeasons = $title->seasons->sortBy('season_number')->values();

        if ($availableSeasons->isEmpty()) {
            return [$season, $episode];
        }

        $currentSeason = $availableSeasons->firstWhere('season_number', $season) ?? $availableSeasons->first();
        $season = (int) $currentSeason->season_number;

        $availableEpisodes = $currentSeason->episodes
            ->sortBy('episode_number')
            ->pluck('episode_number')
            ->map(fn ($episodeNumber) => (int) $episodeNumber)
            ->values();

        if ($availableEpisodes->isNotEmpty()) {
            if (! $availableEpisodes->contains($episode)) {
                $episode = $availableEpisodes->first();
            }

            return [$season, $episode];
        }

        $maxEpisode = max(1, (int) ($currentSeason->episode_count ?: 1));

        return [$season, min($episode, $maxEpisode)];
    }
}
