<div class="grid gap-6 lg:grid-cols-[1.4fr_0.6fr]">
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">TMDb ID</span>
                <input type="number" name="tmdb_id" value="{{ old('tmdb_id', $genre->tmdb_id ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Name</span>
                <input type="text" name="name" value="{{ old('name', $genre->name ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
        </div>
    </div>
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 text-[13px] text-white/45">
        <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Catalog Link</p>
        <p class="mt-4">Genres are shared across movies and series, so edits here affect the whole catalog.</p>
    </div>
</div>
