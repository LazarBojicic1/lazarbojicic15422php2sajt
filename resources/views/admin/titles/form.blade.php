@php
    $typeOptions = collect([
        'movie' => 'Movie',
        'tv' => 'Series',
    ])->map(fn ($label, $value) => ['value' => $value, 'text' => $label])->values()->all();
    $selectedGenres = collect(old('genres', isset($title) ? $title->genres->pluck('id')->all() : []))
        ->map(fn ($id) => (string) $id)
        ->all();
@endphp

<div class="grid gap-6 xl:grid-cols-[1.5fr_0.5fr]">
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="grid gap-2 md:col-span-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Name</span>
                <input type="text" name="name" value="{{ old('name', $title->name ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Slug</span>
                <input type="text" name="slug" value="{{ old('slug', $title->slug ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Type</span>
                @include('admin.partials.select-dropdown', [
                    'name' => 'tmdb_type',
                    'current' => old('tmdb_type', $title->tmdb_type ?? 'movie'),
                    'options' => $typeOptions,
                    'placeholder' => 'Choose a type',
                ])
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">TMDb ID</span>
                <input type="number" name="tmdb_id" value="{{ old('tmdb_id', $title->tmdb_id ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">IMDb ID</span>
                <input type="text" name="imdb_id" value="{{ old('imdb_id', $title->imdb_id ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2 md:col-span-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Original Name</span>
                <input type="text" name="original_name" value="{{ old('original_name', $title->original_name ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2 md:col-span-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Overview</span>
                <textarea name="overview" rows="6" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">{{ old('overview', $title->overview ?? '') }}</textarea>
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Poster Path</span>
                <input type="text" name="poster_path" value="{{ old('poster_path', $title->poster_path ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Backdrop Path</span>
                <input type="text" name="backdrop_path" value="{{ old('backdrop_path', $title->backdrop_path ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Release Date</span>
                <input type="date" name="release_date" value="{{ old('release_date', optional($title->release_date ?? null)->format('Y-m-d')) }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">First Air Date</span>
                <input type="date" name="first_air_date" value="{{ old('first_air_date', optional($title->first_air_date ?? null)->format('Y-m-d')) }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Last Air Date</span>
                <input type="date" name="last_air_date" value="{{ old('last_air_date', optional($title->last_air_date ?? null)->format('Y-m-d')) }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Runtime</span>
                <input type="number" name="runtime" value="{{ old('runtime', $title->runtime ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Number of Seasons</span>
                <input type="number" name="number_of_seasons" value="{{ old('number_of_seasons', $title->number_of_seasons ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Number of Episodes</span>
                <input type="number" name="number_of_episodes" value="{{ old('number_of_episodes', $title->number_of_episodes ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Status</span>
                <input type="text" name="status" value="{{ old('status', $title->status ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Original Language</span>
                <input type="text" name="original_language" value="{{ old('original_language', $title->original_language ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Country</span>
                <input type="text" name="country" value="{{ old('country', $title->country ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Vote Average</span>
                <input type="number" step="0.01" name="vote_average" value="{{ old('vote_average', $title->vote_average ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Vote Count</span>
                <input type="number" name="vote_count" value="{{ old('vote_count', $title->vote_count ?? 0) }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Popularity</span>
                <input type="number" step="0.01" name="popularity" value="{{ old('popularity', $title->popularity ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Synced At</span>
                <input type="datetime-local" name="synced_at" value="{{ old('synced_at', optional($title->synced_at ?? null)->format('Y-m-d\TH:i')) }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2 md:col-span-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Genres</span>
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($genres ?? [] as $genre)
                        @php
                            $isSelected = in_array((string) $genre->id, $selectedGenres, true);
                        @endphp
                        <label class="flex items-center gap-3 rounded-2xl border border-white/[0.06] bg-[#0b0f16] px-4 py-3 transition hover:border-white/[0.12]">
                            <input
                                type="checkbox"
                                name="genres[]"
                                value="{{ $genre->id }}"
                                @checked($isSelected)
                                class="h-4 w-4 rounded border-white/20 bg-transparent text-red-600 focus:ring-red-500"
                            >
                            <span class="min-w-0">
                                <span class="block truncate text-[14px] font-medium text-white">{{ $genre->name }}</span>
                                <span class="block text-[12px] text-white/35">TMDb #{{ $genre->tmdb_id }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                <span class="text-[12px] text-white/30">Select every genre that applies to this title.</span>
            </label>
        </div>
    </div>

    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="space-y-4">
            <label class="flex items-center gap-3 rounded-2xl border border-white/[0.06] bg-[#0b0f16] px-4 py-4">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $title->is_published ?? true)) class="h-4 w-4 rounded border-white/20 bg-transparent text-red-600">
                <span>
                    <span class="block text-[14px] font-semibold text-white">Published</span>
                    <span class="block text-[12px] text-white/35">Visible on the public site.</span>
                </span>
            </label>
            <label class="flex items-center gap-3 rounded-2xl border border-white/[0.06] bg-[#0b0f16] px-4 py-4">
                <input type="checkbox" name="adult" value="1" @checked(old('adult', $title->adult ?? false)) class="h-4 w-4 rounded border-white/20 bg-transparent text-red-600">
                <span>
                    <span class="block text-[14px] font-semibold text-white">Adult</span>
                    <span class="block text-[12px] text-white/35">Mark adult content if needed.</span>
                </span>
            </label>
        </div>
    </div>
</div>
