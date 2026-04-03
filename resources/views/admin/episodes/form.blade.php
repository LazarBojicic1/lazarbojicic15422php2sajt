@php
    $seasonOptions = collect($seasons ?? [])
        ->map(fn ($season) => [
            'value' => (string) $season->id,
            'text' => $season->name . ' - ' . ($season->title?->name ?? 'Unknown title'),
        ])
        ->all();
@endphp

<div class="grid gap-6 xl:grid-cols-[1.5fr_0.5fr]">
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="grid gap-2 md:col-span-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Season</span>
                @include('admin.partials.select-dropdown', [
                    'name' => 'season_id',
                    'current' => (string) old('season_id', $episode->season_id ?? ''),
                    'options' => $seasonOptions,
                    'placeholder' => 'Choose a season',
                ])
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">TMDb ID</span>
                <input type="number" name="tmdb_id" value="{{ old('tmdb_id', $episode->tmdb_id ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Episode Number</span>
                <input type="number" name="episode_number" value="{{ old('episode_number', $episode->episode_number ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2 md:col-span-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Name</span>
                <input type="text" name="name" value="{{ old('name', $episode->name ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2 md:col-span-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Overview</span>
                <textarea name="overview" rows="5" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">{{ old('overview', $episode->overview ?? '') }}</textarea>
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Still Path</span>
                <input type="text" name="still_path" value="{{ old('still_path', $episode->still_path ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Air Date</span>
                <input type="date" name="air_date" value="{{ old('air_date', optional($episode->air_date ?? null)->format('Y-m-d')) }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Runtime</span>
                <input type="number" name="runtime" value="{{ old('runtime', $episode->runtime ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Vote Average</span>
                <input type="number" step="0.01" name="vote_average" value="{{ old('vote_average', $episode->vote_average ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Vote Count</span>
                <input type="number" name="vote_count" value="{{ old('vote_count', $episode->vote_count ?? 0) }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
        </div>
    </div>
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 text-[13px] text-white/45">
        <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Episode Rules</p>
        <p class="mt-4">Episode numbers are unique inside the selected season.</p>
        @if(isset($episode) && $episode->exists)
            <p class="mt-2">Last updated {{ optional($episode->updated_at)->format('M d, Y h:i A') ?: 'just now' }}</p>
        @endif
    </div>
</div>
