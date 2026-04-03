<div class="grid gap-6 lg:grid-cols-[1.4fr_0.6fr]">
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="grid gap-5">
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Role name</span>
                <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Description</span>
                <textarea name="description" rows="5" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">{{ old('description', $role->description ?? '') }}</textarea>
            </label>
        </div>
    </div>
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Role Rules</p>
        <div class="mt-4 space-y-3 text-[13px] text-white/45">
            <p>Core roles like <span class="text-white/70">admin</span>, <span class="text-white/70">moderator</span>, and <span class="text-white/70">user</span> stay protected by the system.</p>
            <p>Custom roles are best used only when you also plan dedicated access rules for them.</p>
        </div>
    </div>
</div>
