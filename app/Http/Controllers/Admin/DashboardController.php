<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\ImportLog;
use App\Models\Role;
use App\Models\SearchLog;
use App\Models\Title;
use App\Models\TitleRequest;
use App\Models\TitleView;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'titles' => Title::count(),
            'published_titles' => Title::where('is_published', true)->count(),
            'movies' => Title::where('tmdb_type', 'movie')->count(),
            'series' => Title::where('tmdb_type', 'tv')->count(),
            'genres' => Genre::count(),
            'favorites' => Favorite::count(),
            'comments' => Comment::count(),
            'pending_comments' => Comment::where('is_approved', false)->count(),
            'pending_reports' => CommentReport::where('status', 'pending')->count(),
            'pending_requests' => TitleRequest::where('status', 'pending')->count(),
            'searches' => SearchLog::count(),
            'searches_today' => SearchLog::where('searched_at', '>=', now()->startOfDay())->count(),
            'views' => TitleView::count(),
            'imports' => ImportLog::count(),
        ];

        $zeroResultSearches = SearchLog::where('results_count', 0)->count();

        $dailyViews = $this->trendMap(
            TitleView::query()
                ->selectRaw('DATE(viewed_at) as day, COUNT(*) as total')
                ->where('viewed_at', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('day')
                ->orderBy('day')
                ->get(),
            'day'
        );

        $dailySearches = $this->trendMap(
            SearchLog::query()
                ->selectRaw('DATE(searched_at) as day, COUNT(*) as total')
                ->where('searched_at', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('day')
                ->orderBy('day')
                ->get(),
            'day'
        );

        $dailyUsers = $this->trendMap(
            User::query()
                ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('day')
                ->orderBy('day')
                ->get(),
            'day'
        );

        $roleBreakdown = Role::query()
            ->withCount('users')
            ->orderByDesc('users_count')
            ->get()
            ->map(fn (Role $role) => [
                'name' => $role->name,
                'description' => $role->description,
                'count' => $role->users_count,
            ]);

        $topViewedTitles = Title::query()
            ->join('title_views', 'title_views.title_id', '=', 'titles.id')
            ->select('titles.id', 'titles.name', 'titles.slug', 'titles.tmdb_type')
            ->selectRaw('COUNT(title_views.id) as views_count')
            ->groupBy('titles.id', 'titles.name', 'titles.slug', 'titles.tmdb_type')
            ->orderByDesc('views_count')
            ->limit(6)
            ->get();

        $mostFavoritedTitles = Title::query()
            ->join('favorites', 'favorites.title_id', '=', 'titles.id')
            ->select('titles.id', 'titles.name', 'titles.slug', 'titles.tmdb_type')
            ->selectRaw('COUNT(favorites.id) as favorites_count')
            ->groupBy('titles.id', 'titles.name', 'titles.slug', 'titles.tmdb_type')
            ->orderByDesc('favorites_count')
            ->limit(6)
            ->get();

        $topSearchQueries = SearchLog::query()
            ->select('query')
            ->selectRaw('COUNT(*) as search_count')
            ->selectRaw('SUM(CASE WHEN results_count = 0 THEN 1 ELSE 0 END) as zero_result_count')
            ->groupBy('query')
            ->orderByDesc('search_count')
            ->limit(8)
            ->get();

        $recentReports = CommentReport::query()
            ->with(['comment.title', 'reportedBy', 'reviewedBy'])
            ->latest()
            ->limit(6)
            ->get();

        $recentTitleRequests = TitleRequest::query()
            ->with(['user', 'reviewedBy'])
            ->latest()
            ->limit(6)
            ->get();

        $recentImportLogs = ImportLog::query()
            ->with('admin')
            ->latest()
            ->limit(6)
            ->get();

        $recentUsers = User::query()
            ->with('role')
            ->latest()
            ->limit(6)
            ->get();

        $kpis = [
            'search_success_rate' => $stats['searches'] > 0
                ? round(((($stats['searches'] - $zeroResultSearches) / $stats['searches']) * 100), 1)
                : null,
            'average_views_per_title' => $stats['titles'] > 0
                ? round($stats['views'] / $stats['titles'], 1)
                : null,
            'average_favorites_per_user' => $stats['users'] > 0
                ? round($stats['favorites'] / $stats['users'], 1)
                : null,
        ];

        return view('admin.dashboard.index', compact(
            'stats',
            'dailyViews',
            'dailySearches',
            'dailyUsers',
            'roleBreakdown',
            'topViewedTitles',
            'mostFavoritedTitles',
            'topSearchQueries',
            'recentReports',
            'recentTitleRequests',
            'recentImportLogs',
            'recentUsers',
            'kpis',
        ));
    }

    private function trendMap(Collection $rows, string $key): array
    {
        $indexed = $rows->keyBy(fn ($row) => Carbon::parse($row->{$key})->toDateString());

        return collect(range(6, 0))
            ->map(function (int $offset) use ($indexed) {
                $day = now()->subDays($offset)->toDateString();

                return [
                    'date' => $day,
                    'label' => Carbon::parse($day)->format('M j'),
                    'total' => (int) ($indexed[$day]->total ?? 0),
                ];
            })
            ->values()
            ->all();
    }
}
