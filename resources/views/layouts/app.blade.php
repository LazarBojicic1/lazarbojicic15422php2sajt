<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('vite')
</head>
<body class="bg-[#0a0a0f] text-white min-h-screen overflow-x-hidden font-sans antialiased">
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4 md:gap-8 min-w-0">
                    <a href="/" class="text-[22px] font-extrabold tracking-tight shrink-0">
                        @if(Str::startsWith(config('app.name'), 'Stream'))
                            <span class="text-red-600">Stream</span><span class="text-white">{{ Str::after(config('app.name'), 'Stream') }}</span>
                        @else
                            <span class="text-white">{{ config('app.name') }}</span>
                        @endif
                    </a>
                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ route('home') }}" class="text-[13px] font-medium {{ request()->routeIs('home') ? 'text-white/90' : 'text-white/50' }} hover:text-white px-3 py-1.5 rounded transition">Home</a>
                        <a href="{{ route('movies') }}" class="text-[13px] font-medium {{ request()->routeIs('movies') ? 'text-white/90' : 'text-white/50' }} hover:text-white px-3 py-1.5 rounded transition">Movies</a>
                        <a href="{{ route('series') }}" class="text-[13px] font-medium {{ request()->routeIs('series') ? 'text-white/90' : 'text-white/50' }} hover:text-white px-3 py-1.5 rounded transition">Series</a>
                        <a href="{{ asset('documentation.pdf') }}" target="_blank" rel="noopener noreferrer" class="text-[13px] font-medium text-white/50 hover:text-white px-3 py-1.5 rounded transition">Documentation</a>
                        <a href="{{ route('title-requests.create') }}" class="text-[13px] font-medium {{ request()->routeIs('title-requests.*') ? 'text-white/90' : 'text-white/50' }} hover:text-white px-3 py-1.5 rounded transition">Request</a>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Search --}}
                    <div class="relative hidden sm:block" id="search-wrapper">
                        <button type="button" id="search-toggle" class="w-8 h-8 flex items-center justify-center text-white/50 hover:text-white transition rounded-full">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
                        </button>
                        <form id="search-panel" method="GET" action="{{ route('search') }}" class="hidden absolute right-0 top-1/2 -translate-y-1/2 flex items-center">
                            <input
                                type="text"
                                name="q"
                                id="search-input"
                                placeholder="Search movies & series..."
                                autocomplete="off"
                                class="w-[250px] sm:w-[310px] bg-white/[0.08] backdrop-blur-xl text-[13px] text-white placeholder-white/30 pl-9 pr-10 py-2 rounded-lg border border-white/[0.08] focus:border-white/20 focus:outline-none transition"
                            >
                            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/30 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
                            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-[11px] font-semibold text-white/40 transition hover:text-white/80">
                                Go
                            </button>
                        </form>
                        <div id="search-results" class="hidden absolute right-0 top-full mt-2 w-[250px] sm:w-[310px] bg-[#141419]/95 backdrop-blur-xl border border-white/[0.08] rounded-lg shadow-2xl overflow-hidden z-50 max-h-[70vh] overflow-y-auto custom-scrollbar"></div>
                    </div>

                    @auth
                        <div id="user-menu" class="relative hidden md:block">
                            <button
                                type="button"
                                id="user-menu-toggle"
                                aria-expanded="false"
                                class="flex items-center gap-2 rounded-full border border-white/[0.06] bg-white/[0.03] pl-2 pr-3 py-1.5 text-left transition hover:bg-white/[0.06]"
                            >
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-8 h-8 rounded-full object-cover ring-1 ring-white/10" alt="">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-red-600/20 flex items-center justify-center text-[11px] font-bold text-red-400 ring-1 ring-white/5">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="max-w-[120px] truncate text-[13px] text-white/75">{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4 text-white/30 transition-transform" id="user-menu-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div id="user-menu-panel" class="hidden absolute right-0 top-full mt-3 w-64 rounded-[24px] border border-white/[0.06] bg-[#11131b]/98 p-3 shadow-2xl backdrop-blur-xl">
                                <div class="rounded-2xl border border-white/[0.06] bg-white/[0.03] px-4 py-3">
                                    <p class="truncate text-[14px] font-semibold text-white">{{ Auth::user()->name }}</p>
                                    <p class="truncate mt-1 text-[12px] text-white/35">{{ Auth::user()->email }}</p>
                                </div>

                                <div class="mt-3 grid gap-1">
                                    <a href="{{ route('profile.edit') }}" class="rounded-xl px-4 py-3 text-[13px] font-medium {{ request()->routeIs('profile.*') ? 'bg-white/[0.08] text-white' : 'text-white/70 hover:bg-white/[0.05] hover:text-white' }} transition">Edit Profile</a>
                                    <a href="{{ route('my-list') }}" class="rounded-xl px-4 py-3 text-[13px] font-medium {{ request()->routeIs('my-list') ? 'bg-white/[0.08] text-white' : 'text-white/70 hover:bg-white/[0.05] hover:text-white' }} transition">My List</a>
                                    <a href="{{ route('my-requests') }}" class="rounded-xl px-4 py-3 text-[13px] font-medium {{ request()->routeIs('my-requests') ? 'bg-white/[0.08] text-white' : 'text-white/70 hover:bg-white/[0.05] hover:text-white' }} transition">My Requests</a>
                                    @if(Route::has('admin.dashboard') && Auth::user()->canAccessAdminPanel())
                                        <a href="{{ route('admin.dashboard') }}" class="rounded-xl px-4 py-3 text-[13px] font-medium {{ request()->routeIs('admin.*') ? 'bg-red-500/15 text-red-300' : 'text-red-200/80 hover:bg-red-500/10 hover:text-red-200' }} transition">Admin Panel</a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full rounded-xl px-4 py-3 text-left text-[13px] font-medium text-white/70 transition hover:bg-white/[0.05] hover:text-white">
                                            Sign Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="hidden md:flex items-center gap-1">
                            <a href="{{ route('login') }}" class="text-[13px] font-medium text-white/70 hover:text-white transition px-3 py-1.5">Sign In</a>
                            <a href="{{ route('register') }}" class="text-[13px] font-medium bg-red-600 hover:bg-red-500 text-white px-4 py-1.5 rounded transition">Sign Up</a>
                        </div>
                    @endauth

                    @auth
                        <div class="md:hidden flex items-center gap-2">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-8 h-8 rounded-full object-cover ring-1 ring-white/10" alt="">
                            @else
                                <div class="w-8 h-8 rounded-full bg-red-600/20 flex items-center justify-center text-[11px] font-bold text-red-400 ring-1 ring-white/5">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    @endauth

                    <button
                        type="button"
                        id="mobile-nav-toggle"
                        class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/[0.08] bg-white/[0.03] text-white/70 transition hover:bg-white/[0.08] hover:text-white"
                        aria-expanded="false"
                        aria-controls="mobile-nav-panel"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div id="mobile-nav-overlay" class="fixed inset-0 z-40 hidden bg-black/60 backdrop-blur-sm md:hidden"></div>
    <div id="mobile-nav-panel" class="fixed inset-x-0 top-16 z-50 hidden border-b border-white/[0.06] bg-[#0f1016]/98 shadow-2xl md:hidden">
        <div class="max-w-[1400px] mx-auto px-4 pb-6 pt-4">
            <form method="GET" action="{{ route('search') }}" class="mb-5">
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        placeholder="Search movies & series..."
                        class="w-full rounded-xl border border-white/[0.08] bg-white/[0.05] py-3 pl-10 pr-4 text-[14px] text-white placeholder-white/25 focus:border-white/20 focus:outline-none"
                    >
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
            </form>

            <div class="grid gap-1">
                <a href="{{ route('home') }}" class="rounded-xl px-4 py-3 text-[14px] font-medium {{ request()->routeIs('home') ? 'bg-white/[0.08] text-white' : 'text-white/70 hover:bg-white/[0.05] hover:text-white' }} transition">Home</a>
                <a href="{{ route('movies') }}" class="rounded-xl px-4 py-3 text-[14px] font-medium {{ request()->routeIs('movies') ? 'bg-white/[0.08] text-white' : 'text-white/70 hover:bg-white/[0.05] hover:text-white' }} transition">Movies</a>
                <a href="{{ route('series') }}" class="rounded-xl px-4 py-3 text-[14px] font-medium {{ request()->routeIs('series') ? 'bg-white/[0.08] text-white' : 'text-white/70 hover:bg-white/[0.05] hover:text-white' }} transition">Series</a>
                <a href="{{ route('search') }}" class="rounded-xl px-4 py-3 text-[14px] font-medium {{ request()->routeIs('search') ? 'bg-white/[0.08] text-white' : 'text-white/70 hover:bg-white/[0.05] hover:text-white' }} transition">Search</a>
                <a href="{{ asset('documentation.pdf') }}" target="_blank" rel="noopener noreferrer" class="rounded-xl px-4 py-3 text-[14px] font-medium text-white/70 hover:bg-white/[0.05] hover:text-white transition">Documentation</a>
                <a href="{{ route('title-requests.create') }}" class="rounded-xl px-4 py-3 text-[14px] font-medium {{ request()->routeIs('title-requests.*') ? 'bg-white/[0.08] text-white' : 'text-white/70 hover:bg-white/[0.05] hover:text-white' }} transition">Request a Title</a>

                @auth
                    <a href="{{ route('profile.edit') }}" class="rounded-xl px-4 py-3 text-[14px] font-medium {{ request()->routeIs('profile.*') ? 'bg-white/[0.08] text-white' : 'text-white/70 hover:bg-white/[0.05] hover:text-white' }} transition">Edit Profile</a>
                    <a href="{{ route('my-list') }}" class="rounded-xl px-4 py-3 text-[14px] font-medium {{ request()->routeIs('my-list') ? 'bg-white/[0.08] text-white' : 'text-white/70 hover:bg-white/[0.05] hover:text-white' }} transition">My List</a>
                    <a href="{{ route('my-requests') }}" class="rounded-xl px-4 py-3 text-[14px] font-medium {{ request()->routeIs('my-requests') ? 'bg-white/[0.08] text-white' : 'text-white/70 hover:bg-white/[0.05] hover:text-white' }} transition">My Requests</a>
                    @if(Route::has('admin.dashboard') && Auth::user()->canAccessAdminPanel())
                        <a href="{{ route('admin.dashboard') }}" class="rounded-xl px-4 py-3 text-[14px] font-medium {{ request()->routeIs('admin.*') ? 'bg-red-500/15 text-red-300' : 'text-red-200/80 hover:bg-red-500/10 hover:text-red-200' }} transition">Admin Panel</a>
                    @endif

                    <div class="mt-4 rounded-2xl border border-white/[0.06] bg-white/[0.03] px-4 py-4">
                        <div class="flex items-center gap-3">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="h-10 w-10 rounded-full object-cover ring-1 ring-white/10" alt="">
                            @else
                                <div class="h-10 w-10 rounded-full bg-red-600/20 flex items-center justify-center text-[13px] font-bold text-red-400 ring-1 ring-white/5">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="truncate text-[14px] font-semibold text-white/90">{{ Auth::user()->name }}</p>
                                <p class="truncate text-[12px] text-white/35">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                            @csrf
                            <button type="submit" class="w-full rounded-xl border border-white/[0.08] bg-white/[0.04] px-4 py-3 text-left text-[13px] font-medium text-white/70 transition hover:bg-white/[0.08] hover:text-white">
                                Sign Out
                            </button>
                        </form>
                    </div>
                @else
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" class="rounded-xl border border-white/[0.08] bg-white/[0.04] px-4 py-3 text-center text-[13px] font-medium text-white/75 transition hover:bg-white/[0.08] hover:text-white">Sign In</a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-red-600 px-4 py-3 text-center text-[13px] font-semibold text-white transition hover:bg-red-500">Sign Up</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    @if(session('message'))
        <div class="fixed top-20 right-4 z-[60] bg-emerald-500/90 backdrop-blur text-white px-5 py-3 rounded-lg shadow-2xl text-sm font-medium" id="flash-message">
            {{ session('message') }}
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-white/[0.04] mt-20">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-[15px] font-extrabold tracking-tight">
                        @if(Str::startsWith(config('app.name'), 'Stream'))
                            <span class="text-red-600">Stream</span><span class="text-white/80">{{ Str::after(config('app.name'), 'Stream') }}</span>
                        @else
                            <span class="text-white/80">{{ config('app.name') }}</span>
                        @endif
                    </span>
                    <span class="text-[12px] text-white/20 ml-2">&copy; {{ date('Y') }}</span>
                </div>
                <div class="flex items-center gap-5">
                    <a href="#" class="text-[12px] text-white/25 hover:text-white/50 transition">Privacy</a>
                    <a href="#" class="text-[12px] text-white/25 hover:text-white/50 transition">Terms</a>
                    <a href="#" class="text-[12px] text-white/25 hover:text-white/50 transition">Contact</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
