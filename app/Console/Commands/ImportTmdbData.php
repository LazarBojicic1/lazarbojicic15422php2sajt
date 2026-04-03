<?php

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Genre;
use App\Models\Season;
use App\Models\Title;
use App\Services\TmdbService;
use App\Services\TmdbTitleImporter;
use Illuminate\Console\Command;

class ImportTmdbData extends Command
{
    private array $processedMovieIds = [];
    private array $processedTvIds = [];

    protected $signature = 'tmdb:import
        {--pages=2 : Number of pages to import per feed}
        {--season-limit=1 : Number of TV seasons to import episodes for}';

    protected $description = 'Import movies and TV series from TMDB API';

    public function handle(TmdbService $tmdb, TmdbTitleImporter $importer): int
    {
        $pages = (int) $this->option('pages');
        $seasonLimit = max(0, (int) $this->option('season-limit'));
        $movieFeeds = ['popular'];
        $tvFeeds = ['popular'];

        $this->info('Importing genres...');
        $importer->syncGenres();
        $this->info('  Genres synced');

        $this->info(sprintf(
            'Importing movie feeds (%s, %d pages each)...',
            implode(', ', $movieFeeds),
            $pages,
        ));
        $this->importFeed($tmdb, $importer, $movieFeeds, $pages, 'movie', $seasonLimit);

        $this->info(sprintf(
            'Importing TV feeds (%s, %d pages each, %d seasons with episodes)...',
            implode(', ', $tvFeeds),
            $pages,
            $seasonLimit,
        ));
        $this->importFeed($tmdb, $importer, $tvFeeds, $pages, 'tv', $seasonLimit);

        $this->newLine();
        $this->info('Import completed!');
        $this->table(
            ['Type', 'Count'],
            [
                ['Movies', Title::where('tmdb_type', 'movie')->count()],
                ['TV Series', Title::where('tmdb_type', 'tv')->count()],
                ['Genres', Genre::count()],
                ['Seasons', Season::count()],
                ['Episodes', Episode::count()],
            ]
        );

        return Command::SUCCESS;
    }

    private function importFeed(
        TmdbService $tmdb,
        TmdbTitleImporter $importer,
        array $feeds,
        int $pages,
        string $type,
        int $seasonLimit,
    ): void {
        $bar = $this->output->createProgressBar(count($feeds) * $pages * 20);

        foreach ($feeds as $feed) {
            for ($page = 1; $page <= $pages; $page++) {
                $data = $type === 'movie'
                    ? $tmdb->getMoviesByCategory($feed, $page)
                    : $tmdb->getTvByCategory($feed, $page);

                foreach ($data['results'] ?? [] as $item) {
                    $tmdbId = (int) ($item['id'] ?? 0);

                    if ($tmdbId < 1 || $this->isAlreadyProcessed($tmdbId, $type)) {
                        $bar->advance();
                        continue;
                    }

                    try {
                        $importer->importByTmdbId(
                            $tmdbId,
                            $type,
                            $type === 'tv' ? $seasonLimit : 0,
                            null,
                            true,
                            $type === 'movie' ? 'import_movie' : 'import_tv',
                        );
                    } catch (\Throwable $e) {
                        $this->warn("  Failed to import {$type} {$tmdbId}: {$e->getMessage()}");
                    }

                    $bar->advance();
                    usleep(300000);
                }

                usleep(300000);
            }
        }

        $bar->finish();
        $this->newLine();
    }

    private function isAlreadyProcessed(int $tmdbId, string $type): bool
    {
        if ($type === 'movie') {
            if (in_array($tmdbId, $this->processedMovieIds, true)) {
                return true;
            }

            $this->processedMovieIds[] = $tmdbId;

            return false;
        }

        if (in_array($tmdbId, $this->processedTvIds, true)) {
            return true;
        }

        $this->processedTvIds[] = $tmdbId;

        return false;
    }
}
