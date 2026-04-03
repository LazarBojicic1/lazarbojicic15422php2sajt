@if($titles->count())
<section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10">
    <h2 class="text-[16px] sm:text-[18px] font-bold text-white/90 mb-3 tracking-[-0.01em]">{{ $rowTitle }}</h2>

    <div class="title-row-wrapper relative group/row">
        <button class="row-scroll-left absolute left-0 top-0 bottom-0 z-20 w-10 flex items-center justify-center opacity-0 group-hover/row:opacity-100 transition-opacity duration-300 cursor-pointer">
            <div class="w-8 h-8 rounded-full bg-black/60 backdrop-blur flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </div>
        </button>
        <button class="row-scroll-right absolute right-0 top-0 bottom-0 z-20 w-10 flex items-center justify-center opacity-0 group-hover/row:opacity-100 transition-opacity duration-300 cursor-pointer">
            <div class="w-8 h-8 rounded-full bg-black/60 backdrop-blur flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </div>
        </button>

        <div class="title-row flex gap-[6px] overflow-x-auto scrollbar-hide scroll-smooth pb-1">
            @foreach($titles as $item)
            <div class="title-card flex-none w-[130px] sm:w-[150px] lg:w-[170px] group/card relative">
                <a href="/watch/{{ $item->slug }}">
                    <div class="relative aspect-[2/3] rounded overflow-hidden bg-white/[0.03]">
                        @if($item->poster_path)
                            <img
                                src="https://image.tmdb.org/t/p/w342{{ $item->poster_path }}"
                                alt="{{ $item->name }}"
                                class="w-full h-full object-cover transition-all duration-300 group-hover/card:scale-[1.04] group-hover/card:brightness-75"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white/10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2.5">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                @if($item->vote_average)
                                    <span class="text-yellow-400/90 text-[11px] font-bold flex items-center gap-0.5">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        {{ number_format($item->vote_average, 1) }}
                                    </span>
                                @endif
                                <span class="text-white/40 text-[10px] capitalize">{{ $item->tmdb_type === 'tv' ? 'Series' : 'Movie' }}</span>
                            </div>
                            <p class="text-white text-[11px] font-semibold leading-tight line-clamp-2">{{ $item->name }}</p>
                        </div>
                    </div>
                </a>
                @auth
                @php $isFav = in_array($item->id, $userFavoriteIds ?? []); @endphp
                <button
                    type="button"
                    data-favorite-toggle
                    data-title-id="{{ $item->id }}"
                    class="favorite-btn absolute top-1.5 right-1.5 z-10 w-7 h-7 rounded-full flex items-center justify-center transition-all opacity-0 group-hover/card:opacity-100 {{ $isFav ? 'bg-red-600/90 text-white fav-active' : 'bg-black/60 backdrop-blur text-white/70 hover:bg-black/80 hover:text-white' }}"
                    title="{{ $isFav ? 'Remove from My List' : 'Add to My List' }}"
                >
                    <svg class="w-3.5 h-3.5" fill="{{ $isFav ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/></svg>
                </button>
                @endauth
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
