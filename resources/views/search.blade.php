@extends('layouts.app')

@section('title', 'Search - ' . config('app.name'))

@section('content')
    <div class="pt-24 pb-6 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10">
        <p class="text-red-500 text-[12px] font-bold uppercase tracking-[0.2em] mb-2">Search</p>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-[-0.02em]">Find Movies and Series</h1>
        <p class="text-white/35 text-[14px] mt-2 max-w-2xl">Use the full search page when you want complete results. We keep track of submitted searches so you can later see what people looked for and what was missing.</p>

        <form method="GET" action="{{ route('search') }}" class="mt-6 max-w-2xl">
            <div class="flex items-center gap-2 rounded-xl border border-white/[0.08] bg-white/[0.04] p-2 backdrop-blur-xl">
                <div class="relative flex-1">
                    <input
                        type="text"
                        name="q"
                        value="{{ $query }}"
                        placeholder="Search titles, franchises, or original names..."
                        autocomplete="off"
                        class="w-full bg-transparent pl-10 pr-3 py-3 text-[14px] text-white placeholder-white/25 focus:outline-none"
                    >
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-white/30 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <button type="submit" class="shrink-0 rounded-lg bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">
                    Search
                </button>
            </div>
        </form>

        @if($query === '')
            <p class="mt-4 text-[13px] text-white/25">Start with at least 2 characters to search across the catalog.</p>
        @elseif(mb_strlen($query) < 2)
            <p class="mt-4 text-[13px] text-amber-300/70">Enter at least 2 characters to run a search.</p>
        @else
            <div class="mt-4 flex flex-wrap items-center gap-3 text-[13px]">
                <span class="text-white/60">{{ $titles->total() }} {{ Str::plural('result', $titles->total()) }} for</span>
                <span class="rounded-full bg-white/[0.06] px-3 py-1 text-white/85">"{{ $query }}"</span>
            </div>
        @endif
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10 mt-2 pb-12">
        @if($query !== '' && mb_strlen($query) >= 2 && $titles->count())
            <div class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2.5 sm:gap-4">
                @foreach($titles as $item)
                    <div class="group/card relative">
                        <a
                            href="{{ route('watch', ['slug' => $item->slug]) }}"
                            data-search-result-link
                            data-title-id="{{ $item->id }}"
                            data-search-log-id="{{ $searchLog?->id }}"
                            data-search-query="{{ $query }}"
                        >
                            <div class="relative aspect-[2/3] rounded-lg overflow-hidden bg-white/[0.03]">
                                @if($item->poster_path)
                                    <img src="https://image.tmdb.org/t/p/w342{{ $item->poster_path }}" alt="{{ $item->name }}" class="w-full h-full object-cover transition-all duration-300 group-hover/card:scale-[1.04] group-hover/card:brightness-75" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white/10">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                                    </div>
                                @endif

                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-3">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        @if($item->vote_average)
                                            <span class="text-yellow-400/90 text-[11px] font-bold flex items-center gap-0.5">
                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                {{ number_format($item->vote_average, 1) }}
                                            </span>
                                        @endif
                                        <span class="text-white/40 text-[10px] capitalize">{{ $item->tmdb_type === 'tv' ? 'Series' : 'Movie' }}</span>
                                        @if($item->tmdb_type === 'tv' && $item->first_air_date)
                                            <span class="text-white/30 text-[10px]">{{ $item->first_air_date->format('Y') }}</span>
                                        @elseif($item->release_date)
                                            <span class="text-white/30 text-[10px]">{{ $item->release_date->format('Y') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-white text-[11px] font-semibold leading-tight line-clamp-2">{{ $item->name }}</p>
                                    @if($item->original_name && $item->original_name !== $item->name)
                                        <p class="mt-0.5 text-white/30 text-[10px] truncate">{{ $item->original_name }}</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @auth
                            @php $isFav = in_array($item->id, $userFavoriteIds ?? []); @endphp
                            <button
                                type="button"
                                data-favorite-toggle
                                data-title-id="{{ $item->id }}"
                                class="favorite-btn absolute top-1.5 right-1.5 z-10 w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center transition-all opacity-0 group-hover/card:opacity-100 {{ $isFav ? 'bg-red-600/90 text-white !opacity-100 fav-active' : 'bg-black/60 backdrop-blur text-white/70 hover:bg-black/80 hover:text-white' }}"
                                title="{{ $isFav ? 'Remove from My List' : 'Add to My List' }}"
                            >
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="{{ $isFav ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/></svg>
                            </button>
                        @endauth
                    </div>
                @endforeach
            </div>

            <div class="mt-10 flex justify-center">
                {{ $titles->links('partials.pagination') }}
            </div>
        @elseif($query !== '' && mb_strlen($query) >= 2)
            <div class="rounded-2xl border border-white/[0.06] bg-white/[0.03] px-6 py-12 text-center">
                <svg class="w-16 h-16 mx-auto text-white/10 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                    <circle cx="11" cy="11" r="8"/>
                    <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
                </svg>
                <p class="text-white/45 text-[16px] font-medium">No results found for "{{ $query }}".</p>
                <p class="text-white/25 text-[13px] mt-2">Try a broader title, original name, or browse the movie and series catalogs.</p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('movies') }}" class="inline-flex items-center rounded-lg bg-white/[0.05] px-4 py-2.5 text-[13px] text-white/75 transition hover:bg-white/[0.08] hover:text-white">Browse Movies</a>
                    <a href="{{ route('series') }}" class="inline-flex items-center rounded-lg bg-white/[0.05] px-4 py-2.5 text-[13px] text-white/75 transition hover:bg-white/[0.08] hover:text-white">Browse Series</a>
                    <a href="{{ route('title-requests.create', ['q' => $query]) }}" class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2.5 text-[13px] font-semibold text-white transition hover:bg-red-500">
                        @auth
                            Request This Title
                        @else
                            Sign In To Request
                        @endauth
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
