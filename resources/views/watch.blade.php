@extends('layouts.app')

@section('title', $title->name . ' - ' . config('app.name'))

@section('content')
@php
    $primarySource = $embedSources[0] ?? null;
    $isFavorited = auth()->check() ? \App\Models\Favorite::where('user_id', auth()->id())->where('title_id', $title->id)->exists() : false;
@endphp
<style>
    #player-shell:fullscreen #player-fullscreen-button,
    #player-shell:-webkit-full-screen #player-fullscreen-button {
        display: none !important;
    }
</style>
<div class="pt-16" data-watch-base-url="{{ route('watch', ['slug' => $title->slug]) }}">
    <div class="w-full bg-black">
        <div class="max-w-[1100px] mx-auto">
            <div class="relative aspect-video bg-black" id="player-shell">
                @if($primarySource)
                    <iframe
                        id="player-frame"
                        src="{{ $embedUrl }}"
                        class="absolute inset-0 w-full h-full"
                        frameborder="0"
                        sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-presentation"
                        referrerpolicy="origin"
                        allowfullscreen
                        webkitallowfullscreen
                        mozallowfullscreen
                        allow="autoplay *; encrypted-media *; fullscreen *; picture-in-picture *"
                    ></iframe>

                    <button
                        type="button"
                        id="player-fullscreen-button"
                        class="absolute right-4 bottom-4 z-10 inline-flex items-center gap-2 rounded-md border border-white/10 bg-black/65 px-3 py-2 text-[12px] font-semibold text-white/80 backdrop-blur transition hover:bg-black/80 hover:text-white"
                        aria-label="Enter fullscreen"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 3H5a2 2 0 00-2 2v3m16-5h-3a2 2 0 012 2v3M3 16v3a2 2 0 002 2h3m11-5v3a2 2 0 01-2 2h-3"/>
                        </svg>
                        <span>Fullscreen</span>
                    </button>
                @else
                    <div class="absolute inset-0 flex items-center justify-center px-6 text-center">
                        <div>
                            <p class="text-lg font-semibold text-white/85">No streaming sources are available right now.</p>
                            <p class="mt-2 text-sm text-white/45">Check the provider configuration and try again.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-[1100px] mx-auto px-4 sm:px-6 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <h1 class="text-2xl sm:text-[28px] font-bold tracking-[-0.02em]">{{ $title->name }}</h1>
                    @auth
                    <button
                        type="button"
                        data-favorite-toggle
                        data-title-id="{{ $title->id }}"
                        class="shrink-0 mt-1 w-10 h-10 rounded-full flex items-center justify-center transition-all {{ $isFavorited ? 'bg-red-600/20 text-red-400' : 'bg-white/[0.06] text-white/40 hover:text-white/70 hover:bg-white/[0.1]' }}"
                        title="{{ $isFavorited ? 'Remove from My List' : 'Add to My List' }}"
                    >
                        <svg class="w-5 h-5" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/></svg>
                    </button>
                    @endauth
                </div>

                <div class="flex items-center flex-wrap gap-3 mb-4 text-[13px]">
                    @if($title->vote_average)
                        <span class="flex items-center gap-1 text-yellow-400/90 font-bold">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            {{ number_format($title->vote_average, 1) }}
                        </span>
                    @endif
                    @if($title->release_date)
                        <span class="text-white/35">{{ $title->release_date->format('Y') }}</span>
                    @elseif($title->first_air_date)
                        <span class="text-white/35">{{ $title->first_air_date->format('Y') }}</span>
                    @endif
                    @if($title->runtime)
                        <span class="text-white/25">{{ floor($title->runtime / 60) }}h {{ $title->runtime % 60 }}m</span>
                    @endif
                    <span class="text-white/25 capitalize">{{ $title->tmdb_type === 'tv' ? 'Series' : 'Movie' }}</span>
                </div>

                @if($title->genres->count())
                <div class="flex flex-wrap items-center gap-1.5 mb-5">
                    @foreach($title->genres as $genre)
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-white/45 bg-white/[0.06] px-2.5 py-1 rounded">{{ $genre->name }}</span>
                    @endforeach
                </div>
                @endif

                <p class="text-white/40 text-[14px] leading-relaxed">{{ $title->overview }}</p>

                @if($primarySource)
                <div class="mt-6 rounded-xl border border-white/[0.06] bg-white/[0.03] px-4 py-4">
                    <div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-white/30">Playback</p>
                            <p class="mt-1 text-sm text-white/55">If the player does not work, choose another one.</p>
                        </div>
                    </div>

                    @if(count($embedSources) > 1)
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($embedSources as $source)
                            <button
                                type="button"
                                data-player-source
                                data-source-url="{{ $source['url'] }}"
                                class="rounded-md border px-3 py-2 text-[13px] font-semibold transition {{ $loop->first ? 'border-red-500/40 bg-red-500/12 text-red-300' : 'border-white/[0.08] bg-white/[0.03] text-white/55 hover:bg-white/[0.08] hover:text-white/80' }}"
                                aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                            >
                                {{ $source['label'] }}
                            </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif
            </div>

            @if($title->tmdb_type === 'tv' && $title->seasons->count())
            <div class="lg:w-72 shrink-0">
                <h3 class="text-[12px] font-bold text-white/30 uppercase tracking-widest mb-3">Episodes</h3>

                <div class="relative mb-3" id="season-dropdown">
                    <button type="button" id="season-toggle" class="w-full flex items-center justify-between bg-white/[0.04] border border-white/[0.06] text-white rounded px-3 py-2.5 text-[13px] font-medium hover:bg-white/[0.07] transition cursor-pointer">
                        <span id="season-label">{{ $title->seasons->sortBy('season_number')->firstWhere('season_number', $season)?->name ?? 'Season '.$season }}</span>
                        <svg class="w-4 h-4 text-white/40 transition-transform" id="season-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="season-menu" class="hidden absolute left-0 right-0 top-full mt-1 z-50 rounded-lg shadow-2xl max-h-60 overflow-y-auto custom-scrollbar py-1" style="background:#141418;border:1px solid rgba(255,255,255,0.08)">
                        @foreach($title->seasons->sortBy('season_number') as $s)
                            <button type="button" data-season="{{ $s->season_number }}"
                                class="w-full text-left px-3 py-2.5 text-[13px] transition cursor-pointer {{ $season == $s->season_number ? 'bg-red-500/15 text-red-400 font-semibold' : 'text-white/60 hover:bg-white/[0.06] hover:text-white/90' }}">
                                {{ $s->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="max-h-[320px] overflow-y-auto custom-scrollbar space-y-0.5">
                    @php
                        $currentSeason = $title->seasons->firstWhere('season_number', $season);
                    @endphp
                    @if($currentSeason && $currentSeason->episodes->count())
                        @foreach($currentSeason->episodes->sortBy('episode_number') as $ep)
                            <a href="{{ route('watch', ['slug' => $title->slug, 's' => $season, 'e' => $ep->episode_number]) }}"
                               class="block px-3 py-2.5 rounded text-[13px] transition {{ $episode == $ep->episode_number ? 'bg-red-500/15 text-red-400' : 'text-white/50 hover:bg-white/[0.04] hover:text-white/70' }}">
                                <span class="font-bold text-[12px]">E{{ $ep->episode_number }}</span>
                                <span class="ml-1.5">{{ Str::limit($ep->name, 28) }}</span>
                            </a>
                        @endforeach
                    @else
                        @for($e = 1; $e <= min($currentSeason->episode_count ?? 10, 20); $e++)
                            <a href="{{ route('watch', ['slug' => $title->slug, 's' => $season, 'e' => $e]) }}"
                               class="block px-3 py-2.5 rounded text-[13px] transition {{ $episode == $e ? 'bg-red-500/15 text-red-400' : 'text-white/50 hover:bg-white/[0.04] hover:text-white/70' }}">
                                <span class="font-bold text-[12px]">E{{ $e }}</span>
                                <span class="ml-1.5">Episode {{ $e }}</span>
                            </a>
                        @endfor
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($related->count())
    <div class="pb-8">
        @include('components.title-row', ['rowTitle' => 'You May Also Like', 'titles' => $related])
    </div>
    @endif

    {{-- Comments Section --}}
    <div class="max-w-[1100px] mx-auto px-4 sm:px-6 pb-12" id="comments-section" data-title-id="{{ $title->id }}">
        <div class="border-t border-white/[0.06] pt-8">
            <h3 class="text-[18px] font-bold mb-6">Comments</h3>

            @auth
            <div class="mb-8">
                <div class="flex gap-3">
                    <div class="shrink-0">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-8 h-8 rounded-full object-cover ring-1 ring-white/10" alt="">
                        @else
                            <div class="w-8 h-8 rounded-full bg-red-600/20 flex items-center justify-center text-[11px] font-bold text-red-400 ring-1 ring-white/5">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <textarea
                            id="comment-input"
                            placeholder="Share your thoughts..."
                            rows="3"
                            maxlength="2000"
                            class="w-full bg-white/[0.04] border border-white/[0.08] rounded-lg px-4 py-3 text-[13px] text-white/80 placeholder-white/25 resize-none focus:outline-none focus:border-red-500/30 transition"
                        ></textarea>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-[11px] text-white/20" id="comment-char-count">0 / 2000</span>
                            <button type="button" id="comment-submit" class="bg-red-600 hover:bg-red-500 disabled:opacity-30 disabled:cursor-not-allowed text-white text-[13px] font-semibold px-5 py-2 rounded transition" disabled>
                                Comment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="mb-8 rounded-lg border border-white/[0.06] bg-white/[0.02] p-6 text-center">
                <p class="text-white/35 text-[13px] mb-3">Sign in to join the conversation.</p>
                <a href="{{ route('login') }}" class="text-red-400 text-[13px] font-medium hover:text-red-300 transition">Sign In</a>
            </div>
            @endauth

            <div id="comments-list">
                {{-- Comments loaded via AJAX --}}
            </div>

            <div id="comments-loader" class="text-center py-6 hidden">
                <div class="inline-flex items-center gap-2 text-white/30 text-[13px]">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Loading...
                </div>
            </div>

            <button type="button" id="comments-load-more" class="hidden w-full py-3 text-center text-[13px] text-white/30 hover:text-white/50 transition border border-white/[0.06] rounded-lg mt-4">
                Load more comments
            </button>
        </div>
    </div>
</div>

{{-- Report Modal --}}
<div id="report-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-[#141418] border border-white/[0.08] rounded-xl shadow-2xl w-full max-w-md mx-4 p-6">
        <h4 class="text-[16px] font-bold mb-1">Report Comment</h4>
        <p class="text-white/35 text-[12px] mb-5">Help us understand what's wrong with this comment.</p>
        <input type="hidden" id="report-comment-id">
        <div class="space-y-2 mb-5">
            <label class="flex items-center gap-3 p-3 rounded-lg border border-white/[0.06] hover:border-white/[0.12] cursor-pointer transition">
                <input type="radio" name="report-reason" value="Spam or misleading" class="accent-red-500">
                <span class="text-[13px] text-white/70">Spam or misleading</span>
            </label>
            <label class="flex items-center gap-3 p-3 rounded-lg border border-white/[0.06] hover:border-white/[0.12] cursor-pointer transition">
                <input type="radio" name="report-reason" value="Harassment or hate speech" class="accent-red-500">
                <span class="text-[13px] text-white/70">Harassment or hate speech</span>
            </label>
            <label class="flex items-center gap-3 p-3 rounded-lg border border-white/[0.06] hover:border-white/[0.12] cursor-pointer transition">
                <input type="radio" name="report-reason" value="Spoilers without warning" class="accent-red-500">
                <span class="text-[13px] text-white/70">Spoilers without warning</span>
            </label>
            <label class="flex items-center gap-3 p-3 rounded-lg border border-white/[0.06] hover:border-white/[0.12] cursor-pointer transition">
                <input type="radio" name="report-reason" value="Other" class="accent-red-500">
                <span class="text-[13px] text-white/70">Other</span>
            </label>
        </div>
        <div class="flex items-center gap-3 justify-end">
            <button type="button" id="report-cancel" class="text-[13px] text-white/40 hover:text-white/70 transition px-4 py-2">Cancel</button>
            <button type="button" id="report-submit" class="bg-red-600 hover:bg-red-500 disabled:opacity-30 text-white text-[13px] font-semibold px-5 py-2 rounded transition" disabled>Report</button>
        </div>
    </div>
</div>

@endsection

@push('vite')
    @vite('resources/js/pages/watch.js')
@endpush
