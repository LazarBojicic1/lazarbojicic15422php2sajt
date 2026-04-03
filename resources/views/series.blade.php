@extends('layouts.app')

@section('title', 'Series - ' . config('app.name'))

@section('content')
    @if($featured)
    <section class="relative h-[45vh] sm:h-[50vh] min-h-[340px] flex items-end overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://image.tmdb.org/t/p/original{{ $featured->backdrop_path }}" alt="{{ $featured->name }}" class="w-full h-full object-cover object-top">
            <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0f] via-[#0a0a0f]/70 to-[#0a0a0f]/30"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#0a0a0f]/80 via-transparent to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-[#0a0a0f] to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10 pb-10 w-full">
            <p class="text-red-500 text-[12px] font-bold uppercase tracking-[0.2em] mb-2">Explore</p>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-[-0.02em]">TV Series</h1>
            <p class="text-white/40 text-[14px] mt-2 max-w-md">Binge-worthy shows from around the world. Find your next obsession.</p>
        </div>
    </section>
    @else
    <div class="pt-24 pb-6 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10">
        <p class="text-red-500 text-[12px] font-bold uppercase tracking-[0.2em] mb-2">Explore</p>
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-[-0.02em]">TV Series</h1>
    </div>
    @endif

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10 {{ $featured ? '-mt-2' : 'mt-4' }}">
        <form method="GET" action="{{ route('series') }}" id="filter-form" class="mb-8">
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                @include('partials.filter-dropdown', [
                    'name' => 'genre',
                    'label' => 'All Genres',
                    'current' => request('genre', ''),
                    'options' => collect([['value' => '', 'text' => 'All Genres']])->concat($genres->map(fn ($g) => ['value' => $g->id, 'text' => $g->name]))->all(),
                ])

                @include('partials.filter-dropdown', [
                    'name' => 'year',
                    'label' => 'All Years',
                    'current' => request('year', ''),
                    'options' => collect([['value' => '', 'text' => 'All Years']])->concat($years->map(fn ($y) => ['value' => $y, 'text' => $y]))->all(),
                ])

                @include('partials.filter-dropdown', [
                    'name' => 'sort',
                    'label' => 'Most Popular',
                    'current' => request('sort', 'popular'),
                    'options' => [
                        ['value' => 'popular', 'text' => 'Most Popular'],
                        ['value' => 'rating', 'text' => 'Top Rated'],
                        ['value' => 'newest', 'text' => 'Newest First'],
                        ['value' => 'oldest', 'text' => 'Oldest First'],
                        ['value' => 'name', 'text' => 'A-Z'],
                    ],
                ])

                @if(request()->hasAny(['genre', 'year', 'sort']))
                    <a href="{{ route('series') }}" class="text-[12px] text-white/30 hover:text-white/60 transition">Clear</a>
                @endif

                <span class="text-[12px] text-white/20 ml-auto hidden sm:inline">{{ $titles->total() }} {{ Str::plural('title', $titles->total()) }}</span>
            </div>
        </form>

        @if($titles->count())
        <div class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2.5 sm:gap-4">
            @foreach($titles as $item)
            <div class="group/card relative">
                <a href="/watch/{{ $item->slug }}">
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
                                @if($item->first_air_date)
                                    <span class="text-white/40 text-[10px]">{{ $item->first_air_date->format('Y') }}</span>
                                @endif
                            </div>
                            <p class="text-white text-[11px] font-semibold leading-tight line-clamp-2">{{ $item->name }}</p>
                            @if($item->number_of_seasons)
                                <p class="text-white/30 text-[10px] mt-0.5">{{ $item->number_of_seasons }} {{ Str::plural('Season', $item->number_of_seasons) }}</p>
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
        @else
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto text-white/10 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
            <p class="text-white/30 text-[15px]">No series found matching your filters.</p>
            <a href="{{ route('series') }}" class="text-red-400/70 text-[13px] hover:text-red-400 transition mt-2 inline-block">Clear all filters</a>
        </div>
        @endif
    </div>
@endsection
