@php
    $isEdit = isset($user);
    $roleOptions = collect($roles ?? [])
        ->map(fn ($role) => ['value' => (string) $role->id, 'text' => ucfirst($role->name)])
        ->all();
@endphp

<div class="grid gap-6 lg:grid-cols-[1.4fr_0.6fr]">
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Name</span>
                <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white placeholder-white/20 focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Email</span>
                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white placeholder-white/20 focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Role</span>
                @include('admin.partials.select-dropdown', [
                    'name' => 'role_id',
                    'current' => (string) old('role_id', $user->role_id ?? ''),
                    'options' => $roleOptions,
                    'placeholder' => 'Choose a role',
                ])
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Password {{ $isEdit ? '(leave blank to keep)' : '' }}</span>
                <input type="password" name="password" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white placeholder-white/20 focus:border-red-500/40 focus:outline-none">
            </label>
            <label class="grid gap-2">
                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Confirm Password</span>
                <input type="password" name="password_confirmation" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white placeholder-white/20 focus:border-red-500/40 focus:outline-none">
            </label>
        </div>
    </div>

    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Status</p>
        <div class="mt-4 space-y-4">
            <label class="flex items-center gap-3 rounded-2xl border border-white/[0.06] bg-[#0b0f16] px-4 py-4">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true)) class="h-4 w-4 rounded border-white/20 bg-transparent text-red-600 focus:ring-red-500">
                <span>
                    <span class="block text-[14px] font-semibold text-white">Account active</span>
                    <span class="block text-[12px] text-white/35">Disabled accounts cannot sign in.</span>
                </span>
            </label>
            <label class="flex items-center gap-3 rounded-2xl border border-white/[0.06] bg-[#0b0f16] px-4 py-4">
                <input type="checkbox" name="email_verified" value="1" @checked(old('email_verified', filled($user->email_verified_at ?? null))) class="h-4 w-4 rounded border-white/20 bg-transparent text-red-600 focus:ring-red-500">
                <span>
                    <span class="block text-[14px] font-semibold text-white">Email verified</span>
                    <span class="block text-[12px] text-white/35">Use this to manually mark the account as verified.</span>
                </span>
            </label>
        </div>

        @if($isEdit)
            <div class="mt-5 rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4 text-[13px] text-white/45">
                <p class="font-semibold text-white/80">Account details</p>
                <p class="mt-2">Created {{ optional($user->created_at)->format('M d, Y h:i A') ?: 'just now' }}</p>
                <p class="mt-1">Updated {{ optional($user->updated_at)->format('M d, Y h:i A') ?: 'just now' }}</p>
            </div>
        @endif
    </div>
</div>
