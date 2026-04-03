<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - ' . config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('admin-styles')
</head>
<body class="min-h-screen overflow-x-hidden bg-[#07090f] text-white antialiased">
    <div class="pointer-events-none fixed inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(220,38,38,0.12),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(255,255,255,0.05),_transparent_28%)]"></div>

    @php
        $adminUser = Auth::user();
        $isAdmin = $adminUser?->isAdmin();
        $navigation = [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'M3 13h8V3H3v10Zm10 8h8V3h-8v18Zm-10 0h8v-6H3v6Zm10 0h8v-10h-8v10Z', 'roles' => ['admin', 'moderator']],
            ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'M17 20h5v-2a4 4 0 0 0-5-3.87M17 20H7m10 0v-2c0-.653-.12-1.277-.34-1.85M7 20H2v-2a4 4 0 0 1 5-3.87m0 5v-2c0-.653.12-1.277.34-1.85M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6-1.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-12 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z', 'roles' => ['admin']],
            ['label' => 'Roles', 'route' => 'admin.roles.index', 'icon' => 'M9 2a7 7 0 1 0 4.95 11.95L20 20l-1.5 1.5-6.05-6.05A7 7 0 1 0 9 2Zm0 2a5 5 0 1 1 0 10A5 5 0 0 1 9 4Z', 'roles' => ['admin']],
            ['label' => 'Titles', 'route' => 'admin.titles.index', 'icon' => 'M4 4h16v16H4V4Zm4 3v10l8-5-8-5Z', 'roles' => ['admin', 'moderator']],
            ['label' => 'Genres', 'route' => 'admin.genres.index', 'icon' => 'M7 3h10l4 4v14H7V3Zm2 2v16h10V8h-3V5H9Zm1.5 5h7v1.5h-7V10Zm0 3h7v1.5h-7V13Zm0 3h4v1.5h-4V16Z', 'roles' => ['admin']],
            ['label' => 'Seasons', 'route' => 'admin.seasons.index', 'icon' => 'M4 5a2 2 0 0 1 2-2h12l2 2v14a2 2 0 0 1-2 2H6l-2-2V5Zm3 2v12h10V7H7Zm2 2h6v2H9V9Zm0 4h6v2H9v-2Z', 'roles' => ['admin', 'moderator']],
            ['label' => 'Episodes', 'route' => 'admin.episodes.index', 'icon' => 'M4 6h16v12H4V6Zm2 2v8h12V8H6Zm2 1.5h8V11H8V9.5Zm0 3h5v1.5H8v-1.5Z', 'roles' => ['admin', 'moderator']],
            ['label' => 'Comments', 'route' => 'admin.comments.index', 'icon' => 'M4 5h16v10H7l-3 3V5Zm3 3v2h10V8H7Zm0 4v2h7v-2H7Z', 'roles' => ['admin', 'moderator']],
            ['label' => 'Reports', 'route' => 'admin.reports.index', 'icon' => 'M12 2 1 21h22L12 2Zm0 4.5L18.5 19h-13L12 6.5Zm-1 4h2v4h-2v-4Zm0 5h2v2h-2v-2Z', 'roles' => ['admin', 'moderator']],
            ['label' => 'Requests', 'route' => 'admin.title-requests.index', 'icon' => 'M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm1 5h-2v5H7v2h4v5h2v-5h4v-2h-4V7Z', 'roles' => ['admin', 'moderator']],
            ['label' => 'Favorites', 'route' => 'admin.favorites.index', 'icon' => 'M12 21s-7-4.35-9.5-8.3C.35 8.35 2.3 4.5 6.5 4.5c2.02 0 3.38 1.04 4.5 2.42C12.12 5.54 13.48 4.5 15.5 4.5c4.2 0 6.15 3.85 4 8.2C19 16.65 12 21 12 21Z', 'roles' => ['admin']],
            ['label' => 'Search Logs', 'route' => 'admin.search-logs.index', 'icon' => 'M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Zm8-1 3 3-1.5 1.5-3-3V17l-1-.9A9 9 0 1 0 17 17l1 .9v.1Z', 'roles' => ['admin', 'moderator']],
            ['label' => 'Title Views', 'route' => 'admin.title-views.index', 'icon' => 'M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Zm11 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z', 'roles' => ['admin', 'moderator']],
            ['label' => 'Import Logs', 'route' => 'admin.import-logs.index', 'icon' => 'M4 4h16v16H4V4Zm8 3 4 4h-3v4h-2v-4H8l4-4Z', 'roles' => ['admin', 'moderator']],
        ];
    @endphp

    <div class="relative z-10 min-h-screen lg:pl-72">
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-white/[0.06] bg-[#0a0d13]/95 backdrop-blur-xl lg:flex lg:flex-col">
            <div class="border-b border-white/[0.06] px-6 py-6">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-lg font-extrabold tracking-tight">
                    <span class="text-red-500">Stream</span><span class="text-white">Admin</span>
                </a>
                <p class="mt-2 text-[12px] leading-5 text-white/35">Full control panel for catalog, moderation, and platform statistics.</p>
            </div>

            <div class="flex-1 overflow-y-auto px-4 py-5 custom-scrollbar">
                <div class="mb-5 rounded-2xl border border-white/[0.06] bg-white/[0.03] p-4">
                    <div class="flex items-center gap-3">
                        @if($adminUser?->avatar)
                            <img src="{{ asset('storage/' . $adminUser->avatar) }}" class="h-11 w-11 rounded-full object-cover ring-1 ring-white/10" alt="">
                        @else
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-red-500/15 text-sm font-bold text-red-300 ring-1 ring-red-500/10">
                                {{ strtoupper(substr($adminUser?->name ?? 'A', 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="truncate text-[14px] font-semibold text-white">{{ $adminUser?->name ?? 'Admin' }}</p>
                            <p class="truncate text-[12px] text-white/35">{{ $adminUser?->email ?? '' }}</p>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="rounded-full bg-red-500/15 px-3 py-1 text-[11px] font-semibold text-red-200">{{ $isAdmin ? 'Admin' : 'Moderator' }}</span>
                        <span class="rounded-full bg-white/[0.05] px-3 py-1 text-[11px] text-white/45">Online</span>
                    </div>
                </div>

                <nav class="space-y-1">
                    @foreach($navigation as $item)
                        @if($adminUser?->hasAnyRole($item['roles']))
                            <a
                                href="{{ route($item['route']) }}"
                                class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-[13px] font-medium transition {{ request()->routeIs($item['route'].'*') ? 'bg-white/[0.07] text-white shadow-[0_0_0_1px_rgba(255,255,255,0.06)]' : 'text-white/55 hover:bg-white/[0.04] hover:text-white' }}"
                            >
                                <svg class="h-4.5 w-4.5 shrink-0 {{ request()->routeIs($item['route'].'*') ? 'text-red-400' : 'text-white/30 group-hover:text-white/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                </svg>
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>

            <div class="border-t border-white/[0.06] p-4">
                <a href="{{ route('home') }}" class="flex items-center justify-between rounded-2xl border border-white/[0.06] bg-white/[0.03] px-4 py-3 text-[13px] text-white/60 transition hover:bg-white/[0.06] hover:text-white">
                    <span>Back to site</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l7-7-7-7M17 12H3"/></svg>
                </a>
            </div>
        </aside>

        <header class="sticky top-0 z-30 border-b border-white/[0.06] bg-[#07090f]/90 backdrop-blur-xl">
            <div class="mx-auto flex max-w-[1600px] items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button
                        type="button"
                        id="admin-mobile-toggle"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/[0.08] bg-white/[0.03] text-white/70 transition hover:bg-white/[0.08] hover:text-white lg:hidden"
                        aria-controls="admin-mobile-drawer"
                        aria-expanded="false"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <p class="truncate text-[11px] font-semibold uppercase tracking-[0.26em] text-red-300/80">Admin Panel</p>
                        <h1 class="truncate text-[18px] font-bold tracking-[-0.02em] text-white">@yield('page-title', 'Dashboard')</h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="hidden rounded-full border border-white/[0.08] bg-white/[0.03] px-4 py-2 text-[13px] text-white/60 transition hover:bg-white/[0.08] hover:text-white sm:inline-flex">View site</a>
                    @if($adminUser)
                        <div class="hidden items-center gap-3 rounded-full border border-white/[0.08] bg-white/[0.03] px-3 py-2 sm:flex">
                            <span class="text-[12px] text-white/40">{{ $adminUser->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-full bg-red-500/15 px-3 py-1.5 text-[12px] font-semibold text-red-200 transition hover:bg-red-500/25">Sign out</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            @if(session('message'))
                <div class="mb-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-5 py-4 text-[14px] text-emerald-100">
                    {{ session('message') }}
                </div>
            @endif

            @if(session('status'))
                <div class="mb-6 rounded-2xl border border-blue-500/20 bg-blue-500/10 px-5 py-4 text-[14px] text-blue-100">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <div id="admin-mobile-overlay" class="fixed inset-0 z-40 hidden bg-black/60 backdrop-blur-sm lg:hidden"></div>
    <aside id="admin-mobile-drawer" class="fixed inset-y-0 left-0 z-50 hidden w-[86vw] max-w-sm border-r border-white/[0.06] bg-[#0a0d13]/98 backdrop-blur-xl lg:hidden">
        <div class="flex items-center justify-between border-b border-white/[0.06] px-5 py-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.26em] text-red-300/80">Admin Menu</p>
                <p class="mt-1 text-[13px] text-white/35">{{ $adminUser?->name ?? 'Admin' }}</p>
            </div>
            <button type="button" id="admin-mobile-close" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/[0.08] bg-white/[0.03] text-white/70">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="h-[calc(100%-5rem)] overflow-y-auto px-4 py-4 custom-scrollbar">
            <div class="mb-4 rounded-2xl border border-white/[0.06] bg-white/[0.03] p-4">
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Quick Links</p>
                <div class="mt-3 grid gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="rounded-xl bg-white/[0.05] px-4 py-3 text-[13px] text-white/80">Dashboard</a>
                    <a href="{{ route('home') }}" class="rounded-xl bg-white/[0.05] px-4 py-3 text-[13px] text-white/80">Back to site</a>
                </div>
            </div>

            <nav class="space-y-1">
                @foreach($navigation as $item)
                    @if($adminUser?->hasAnyRole($item['roles']))
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-[13px] font-medium {{ request()->routeIs($item['route'].'*') ? 'bg-white/[0.07] text-white' : 'text-white/65 hover:bg-white/[0.04] hover:text-white' }}">
                            <svg class="h-4.5 w-4.5 shrink-0 {{ request()->routeIs($item['route'].'*') ? 'text-red-400' : 'text-white/30' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                            </svg>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>
    </aside>

    @stack('admin-scripts')

    <script>
        (() => {
            const toggle = document.getElementById('admin-mobile-toggle');
            const closeBtn = document.getElementById('admin-mobile-close');
            const drawer = document.getElementById('admin-mobile-drawer');
            const overlay = document.getElementById('admin-mobile-overlay');
            if (!toggle || !closeBtn || !drawer || !overlay) return;

            const setOpen = (open) => {
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                drawer.classList.toggle('hidden', !open);
                overlay.classList.toggle('hidden', !open);
                document.body.classList.toggle('overflow-hidden', open);
            };

            toggle.addEventListener('click', () => setOpen(drawer.classList.contains('hidden')));
            closeBtn.addEventListener('click', () => setOpen(false));
            overlay.addEventListener('click', () => setOpen(false));
            drawer.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setOpen(false)));
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) setOpen(false);
            });
        })();
    </script>
</body>
</html>
