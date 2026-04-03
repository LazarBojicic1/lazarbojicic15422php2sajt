@php
    $titleOptions = collect($titles ?? [])
        ->map(fn ($title) => ['value' => (string) $title->id, 'text' => $title->name])
        ->all();
@endphp

<div class="grid gap-6 xl:grid-cols-[1.5fr_0.5fr]">
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="grid gap-2 md:col-span-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Title</span>
                @include('admin.partials.select-dropdown', [
                    'name' => 'title_id',
                    'current' => (string) old('title_id', $season->title_id ?? ''),
                    'options' => $titleOptions,
                    'placeholder' => 'Choose a title',
                ])
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">TMDb ID</span>
                <input type="number" name="tmdb_id" value="{{ old('tmdb_id', $season->tmdb_id ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Season Number</span>
                <input type="number" name="season_number" value="{{ old('season_number', $season->season_number ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2 md:col-span-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Name</span>
                <input type="text" name="name" value="{{ old('name', $season->name ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2 md:col-span-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Overview</span>
                <textarea name="overview" rows="5" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">{{ old('overview', $season->overview ?? '') }}</textarea>
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Poster Path</span>
                <input type="text" name="poster_path" value="{{ old('poster_path', $season->poster_path ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Air Date</span>
                <input type="date" name="air_date" value="{{ old('air_date', optional($season->air_date ?? null)->format('Y-m-d')) }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Episode Count</span>
                <input type="number" name="episode_count" value="{{ old('episode_count', $season->episode_count ?? 0) }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
        </div>
    </div>
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 text-[13px] text-white/45">
        <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Season Rules</p>
        <p class="mt-4">Each series can only have one record for a given season number.</p>
        @if(isset($season) && $season->exists)
            <p class="mt-2">Stored episodes: {{ $season->episodes_count ?? $season->episode_count ?? 0 }}</p>
        @endif
    </div>
</div>
