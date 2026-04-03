@extends('admin.layouts.app')

@section('title', 'Create Title - Admin')
@section('page-title', 'Create Title')

@section('content')
    @php
        $typeOptions = collect([
            'multi' => 'All',
            'movie' => 'Movie',
            'tv' => 'Series',
        ])->map(fn ($label, $value) => ['value' => $value, 'text' => $label])->values()->all();
        $seasonLimitOptions = collect([0, 1, 2, 3, 4, 5])
            ->map(fn ($limit) => ['value' => (string) $limit, 'text' => (string) $limit])
            ->all();
    @endphp

    <div class="space-y-6">
        <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">TMDb Import</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-[-0.02em]">Search and import a title</h2>
                </div>
                <p class="max-w-md text-[13px] text-white/35">
                    Search TMDb, pick the correct result, and import it with genres plus seasons and episodes for TV series.
                </p>
            </div>

            @if($errors->has('tmdb_search'))
                <div class="mt-4 rounded-2xl border border-red-500/15 bg-red-500/10 px-4 py-3 text-[13px] text-red-200">
                    {{ $errors->first('tmdb_search') }}
                </div>
            @endif

            @if($searchError)
                <div class="mt-4 rounded-2xl border border-red-500/15 bg-red-500/10 px-4 py-3 text-[13px] text-red-200">
                    {{ $searchError }}
                </div>
            @endif

            <form method="GET" action="{{ route('admin.titles.create') }}" class="mt-6 grid gap-4 lg:grid-cols-[1fr_180px_180px_auto]">
                <label class="grid gap-2">
                    <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Search Query</span>
                    <input
                        type="text"
                        name="q"
                        value="{{ $query }}"
                        placeholder="Movie, series, original title..."
                        class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none"
                    >
                </label>

                <label class="grid gap-2">
                    <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Type</span>
                    @include('admin.partials.select-dropdown', [
                        'name' => 'type',
                        'current' => $searchType,
                        'options' => $typeOptions,
                        'placeholder' => 'Choose a type',
                    ])
                </label>

                <label class="grid gap-2">
                    <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">TV Season Limit</span>
                    @include('admin.partials.select-dropdown', [
                        'name' => 'season_limit',
                        'current' => (string) $seasonLimit,
                        'options' => $seasonLimitOptions,
                        'placeholder' => 'Choose a limit',
                    ])
                </label>

                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-xl bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">
                        Search TMDb
                    </button>
                </div>
            </form>
        </div>

        @if($query !== '' && mb_strlen($query) < 2)
            <div class="rounded-2xl border border-amber-500/15 bg-amber-500/10 px-4 py-3 text-[13px] text-amber-100/80">
                Enter at least 2 characters to search TMDb.
            </div>
        @endif

        @if($searchResults->count())
            <div class="grid gap-4 xl:grid-cols-2">
                @foreach($searchResults as $result)
                    <article class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
                        <div class="flex gap-4">
                            <div class="h-[150px] w-[102px] flex-none overflow-hidden rounded-2xl bg-[#0b0f16]">
                                @if($result['poster_path'])
                                    <img src="https://image.tmdb.org/t/p/w342{{ $result['poster_path'] }}" alt="" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-white/10">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-[20px] font-semibold text-white">{{ $result['name'] }}</h3>
                                    <span class="rounded-full bg-white/[0.06] px-3 py-1 text-[11px] uppercase tracking-[0.14em] text-white/45">
                                        {{ $result['tmdb_type'] === 'tv' ? 'Series' : 'Movie' }}
                                    </span>
                                    @if($result['year'])
                                        <span class="text-[12px] text-white/35">{{ $result['year'] }}</span>
                                    @endif
                                    @if($result['vote_average'])
                                        <span class="text-[12px] text-yellow-300/80">{{ number_format((float) $result['vote_average'], 1) }}/10</span>
                                    @endif
                                </div>

                                @if($result['original_name'] && $result['original_name'] !== $result['name'])
                                    <p class="mt-1 text-[13px] text-white/35">{{ $result['original_name'] }}</p>
                                @endif

                                <p class="mt-4 text-[13px] leading-6 text-white/60">
                                    {{ \Illuminate\Support\Str::limit($result['overview'] ?: 'No overview available from TMDb.', 220) }}
                                </p>

                                @if($result['existing_title'])
                                    <div class="mt-4 rounded-2xl border border-emerald-500/15 bg-emerald-500/10 px-4 py-3 text-[13px] text-emerald-100/80">
                                        Already in catalog.
                                        <a href="{{ route('admin.titles.edit', $result['existing_title']) }}" class="font-semibold text-white underline underline-offset-4">Open existing title</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.titles.store') }}" class="mt-5 flex flex-col gap-4 rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4 md:flex-row md:items-end md:justify-between">
                            @csrf
                            <input type="hidden" name="tmdb_id" value="{{ $result['tmdb_id'] }}">
                            <input type="hidden" name="tmdb_type" value="{{ $result['tmdb_type'] }}">
                            <input type="hidden" name="season_limit" value="{{ $seasonLimit }}">

                            <div class="flex flex-col gap-3 md:flex-row md:items-center">
                                <div class="rounded-xl border border-white/[0.06] bg-white/[0.03] px-4 py-3 text-[12px] text-white/50">
                                    TMDb ID: <span class="font-semibold text-white/85">{{ $result['tmdb_id'] }}</span>
                                </div>
                                <label class="flex items-center gap-3 rounded-xl border border-white/[0.06] bg-white/[0.03] px-4 py-3">
                                    <input type="checkbox" name="is_published" value="1" checked class="h-4 w-4 rounded border-white/20 bg-transparent text-red-600">
                                    <span class="text-[13px] text-white/75">Publish after import</span>
                                </label>
                            </div>

                            <button type="submit" class="rounded-xl bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">
                                {{ $result['existing_title'] ? 'Sync Existing Title' : 'Import Title' }}
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>
        @elseif($query !== '' && mb_strlen($query) >= 2 && ! $searchError)
            <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] px-6 py-16 text-center">
                <p class="text-[18px] font-semibold text-white">No TMDb results found for "{{ $query }}".</p>
                <p class="mt-2 text-[13px] text-white/35">Try a broader title, original name, or switch the type filter.</p>
            </div>
        @endif
    </div>
@endsection
