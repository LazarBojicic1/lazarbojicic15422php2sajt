@extends('layouts.app')

@section('title', 'My List - ' . config('app.name'))

@section('content')
    <div class="pt-24 pb-6 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10">
        <div class="flex items-center gap-3 mb-1">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/></svg>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-[-0.02em]">My List</h1>
        </div>
        <p class="text-white/35 text-[14px]">Your saved movies and shows. {{ $favorites->total() }} {{ Str::plural('title', $favorites->total()) }} in your list.</p>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10 mt-4">
        @if($favorites->count())
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3 sm:gap-4">
            @foreach($favorites as $fav)
            @if($fav->title)
            <div class="group/card relative">
                <a href="/watch/{{ $fav->title->slug }}">
                    <div class="relative aspect-[2/3] rounded-lg overflow-hidden bg-white/[0.03]">
                        @if($fav->title->poster_path)
                            <img src="https://image.tmdb.org/t/p/w342{{ $fav->title->poster_path }}" alt="{{ $fav->title->name }}" class="w-full h-full object-cover transition-all duration-300 group-hover/card:scale-[1.04] group-hover/card:brightness-75" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white/10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-3">
                            <div class="flex items-center gap-1.5 mb-1">
                                @if($fav->title->vote_average)
                                    <span class="text-yellow-400/90 text-[11px] font-bold flex items-center gap-0.5">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        {{ number_format($fav->title->vote_average, 1) }}
                                    </span>
                                @endif
                                <span class="text-white/40 text-[10px] capitalize">{{ $fav->title->tmdb_type === 'tv' ? 'Series' : 'Movie' }}</span>
                            </div>
                            <p class="text-white text-[11px] font-semibold leading-tight line-clamp-2">{{ $fav->title->name }}</p>
                        </div>
                    </div>
                </a>
                <button
                    type="button"
                    data-favorite-toggle
                    data-title-id="{{ $fav->title->id }}"
                    class="absolute top-2 right-2 z-10 w-8 h-8 rounded-full bg-black/60 backdrop-blur flex items-center justify-center opacity-0 group-hover/card:opacity-100 transition-all hover:bg-red-600/80"
                    title="Remove from list"
                >
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @endif
            @endforeach
        </div>

        <div class="mt-10 flex justify-center">
            {{ $favorites->links('partials.pagination') }}
        </div>
        @else
        <div class="text-center py-24">
            <svg class="w-20 h-20 mx-auto text-white/[0.06] mb-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="0.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/></svg>
            <p class="text-white/30 text-[16px] font-medium mb-2">Your list is empty</p>
            <p class="text-white/20 text-[13px] mb-6">Save movies and shows to watch later by clicking the bookmark icon.</p>
            <a href="{{ route('movies') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-500 text-white font-medium text-[13px] px-5 py-2.5 rounded transition">
                Browse Movies
            </a>
        </div>
        @endif
    </div>
@endsection

@push('vite')
    @vite('resources/js/pages/my-list.js')
@endpush
