@extends('layouts.app')

@section('title', config('app.name') . ' - Watch Movies & TV Shows')

@section('content')
    @if($featured)
    <section class="relative h-[70vh] sm:h-[80vh] lg:h-[90vh] min-h-[480px] sm:min-h-[600px] flex items-end overflow-hidden">
        <div class="absolute inset-0">
            <img
                src="https://image.tmdb.org/t/p/original{{ $featured->backdrop_path }}"
                alt="{{ $featured->name }}"
                class="w-full h-full object-cover object-top"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0f] via-[#0a0a0f]/60 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#0a0a0f]/90 via-[#0a0a0f]/30 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-[#0a0a0f] to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10 pb-[10%] w-full">
            <div class="max-w-lg">
                @if($featured->genres->count())
                <div class="flex items-center gap-1.5 mb-4">
                    @foreach($featured->genres->take(3) as $genre)
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-white/60 bg-white/[0.08] px-2.5 py-1 rounded">
                            {{ $genre->name }}
                        </span>
                    @endforeach
                </div>
                @endif

                <h1 class="text-4xl sm:text-5xl lg:text-[56px] font-extrabold leading-[1.05] mb-4 tracking-[-0.02em]">
                    {{ $featured->name }}
                </h1>

                <div class="flex items-center gap-3 mb-5 text-[13px]">
                    @if($featured->vote_average)
                        <span class="flex items-center gap-1 text-yellow-400/90 font-bold">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            {{ number_format($featured->vote_average, 1) }}
                        </span>
                    @endif
                    @if($featured->release_date)
                        <span class="text-white/40">{{ $featured->release_date->format('Y') }}</span>
                    @elseif($featured->first_air_date)
                        <span class="text-white/40">{{ $featured->first_air_date->format('Y') }}</span>
                    @endif
                    <span class="text-white/30 capitalize">{{ $featured->tmdb_type === 'tv' ? 'Series' : 'Movie' }}</span>
                    @if($featured->runtime)
                        <span class="text-white/30">{{ floor($featured->runtime / 60) }}h {{ $featured->runtime % 60 }}m</span>
                    @endif
                </div>

                <p class="text-white/50 text-[14px] leading-relaxed mb-7 line-clamp-3">
                    {{ $featured->overview }}
                </p>

                <div class="flex items-center gap-3">
                    <a href="/watch/{{ $featured->slug }}" class="inline-flex items-center gap-2.5 bg-white text-black font-bold px-7 py-3 rounded-[4px] hover:bg-white/85 transition-all text-[14px] group">
                        <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg>
                        Play
                    </a>
                    <a href="/watch/{{ $featured->slug }}" class="inline-flex items-center gap-2 bg-white/[0.08] text-white/90 font-medium px-6 py-3 rounded-[4px] hover:bg-white/[0.15] transition-all text-[14px] backdrop-blur-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                        More Info
                    </a>
                </div>
            </div>
        </div>
    </section>
    @else
    <div class="h-[40vh] flex items-center justify-center">
        <p class="text-white/30 text-lg">No titles available yet. Run <code class="text-red-400">php artisan tmdb:import</code> to populate the database.</p>
    </div>
    @endif

    <div class="relative z-10 {{ $featured ? 'mt-6 sm:-mt-12' : '' }} space-y-10 pb-12">
        @include('components.title-row', ['rowTitle' => 'Trending Now', 'titles' => $trending])
        @include('components.title-row', ['rowTitle' => 'Popular Movies', 'titles' => $popularMovies])
        @include('components.title-row', ['rowTitle' => 'Popular Series', 'titles' => $popularSeries])
        @include('components.title-row', ['rowTitle' => 'Top Rated', 'titles' => $topRated])
        @include('components.title-row', ['rowTitle' => 'New Releases', 'titles' => $recentMovies])
    </div>
@endsection
