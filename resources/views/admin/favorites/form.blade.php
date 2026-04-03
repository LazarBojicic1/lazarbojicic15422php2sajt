@php
    $userOptions = collect($users ?? [])
        ->map(fn ($user) => ['value' => (string) $user->id, 'text' => $user->name . ' (' . $user->email . ')'])
        ->all();
    $titleOptions = collect($titles ?? [])
        ->map(fn ($title) => ['value' => (string) $title->id, 'text' => $title->name])
        ->all();
@endphp

<div class="grid gap-6 lg:grid-cols-[1.4fr_0.6fr]">
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">User</span>
                @include('admin.partials.select-dropdown', [
                    'name' => 'user_id',
                    'current' => (string) old('user_id', $favorite->user_id ?? ''),
                    'options' => $userOptions,
                    'placeholder' => 'Choose a user',
                ])
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Title</span>
                @include('admin.partials.select-dropdown', [
                    'name' => 'title_id',
                    'current' => (string) old('title_id', $favorite->title_id ?? ''),
                    'options' => $titleOptions,
                    'placeholder' => 'Choose a title',
                ])
            </label>
        </div>
    </div>
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 text-[13px] text-white/45">
        <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">List Entry</p>
        <p class="mt-4">Use this when you need to manually correct a user’s saved list.</p>
    </div>
</div>
