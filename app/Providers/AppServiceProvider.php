<?php

namespace App\Providers;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('access-admin-panel', fn (User $user) => $user->canAccessAdminPanel());
        Gate::define('manage-admin-resources', fn (User $user) => $user->isAdmin());
        Gate::define('moderate-content', fn (User $user) => $user->hasAnyRole(['admin', 'moderator']));

        View::composer('*', function ($view) {
            static $ids = null;
            static $resolved = false;

            if (!$resolved) {
                $ids = Auth::check()
                    ? Favorite::where('user_id', Auth::id())->pluck('title_id')->all()
                    : [];
                $resolved = true;
            }

            $view->with('userFavoriteIds', $ids);
        });
    }
}
