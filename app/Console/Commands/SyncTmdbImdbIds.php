<?php

namespace App\Console\Commands;

use App\Models\ImportLog;
use App\Models\Title;
use App\Services\TmdbService;
use Illuminate\Console\Command;

class SyncTmdbImdbIds extends Command
{
    protected $signature = 'tmdb:sync-imdb
                            {--type=tv : Which titles to sync: movie, tv or all}
                            {--limit=0 : Limit how many titles to process}
                            {--force : Re-sync titles even when imdb_id already exists}';

    protected $description = 'Sync IMDb IDs for imported titles from TMDB';

    public function handle(TmdbService $tmdb): int
    {
        $type = strtolower((string) $this->option('type'));
        $limit = max(0, (int) $this->option('limit'));
        $force = (bool) $this->option('force');

        if (! in_array($type, ['movie', 'tv', 'all'], true)) {
            $this->error('The --type option must be movie, tv or all.');

            return Command::INVALID;
        }

        $query = Title::query()->orderBy('id');

        if ($type !== 'all') {
            $query->where('tmdb_type', $type);
        }

        if (! $force) {
            $query->where(function ($builder) {
                $builder->whereNull('imdb_id')->orWhere('imdb_id', '');
            });
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $titles = $query->get();

        if ($titles->isEmpty()) {
            $this->info('No titles need IMDb sync.');

            return Command::SUCCESS;
        }

        $updated = 0;
        $missing = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar($titles->count());

        foreach ($titles as $title) {
            try {
                $imdbId = $this->resolveImdbId($tmdb, $title);

                if (blank($imdbId)) {
                    $missing++;
                    $this->recordImportLog($title, 'warning', 'IMDb ID not found.');
                } elseif ($title->imdb_id !== $imdbId) {
                    $title->forceFill([
                        'imdb_id' => $imdbId,
                        'synced_at' => now(),
                    ])->save();

                    $updated++;
                    $this->recordImportLog($title, 'success', "IMDb ID synced to {$imdbId}.");
                }
            } catch (\Throwable $e) {
                $failed++;
                $this->recordImportLog($title, 'error', $e->getMessage());
                $this->newLine();
                $this->warn("Failed to sync {$title->tmdb_type} {$title->tmdb_id}: {$e->getMessage()}");
            }

            usleep(300000);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Processed', 'Updated', 'Missing', 'Failed'],
            [[
                $titles->count(),
                $updated,
                $missing,
                $failed,
            ]]
        );

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function resolveImdbId(TmdbService $tmdb, Title $title): ?string
    {
        if ($title->tmdb_type === 'movie') {
            return $tmdb->getMovieDetails($title->tmdb_id)['imdb_id'] ?? null;
        }

        return $tmdb->getTvExternalIds($title->tmdb_id)['imdb_id'] ?? null;
    }

    private function recordImportLog(Title $title, string $status, string $message): void
    {
        ImportLog::create([
            'admin_user_id' => null,
            'tmdb_id' => $title->tmdb_id,
            'tmdb_type' => $title->tmdb_type,
            'action' => 'sync_imdb',
            'status' => $status,
            'message' => $message,
        ]);
    }
}
