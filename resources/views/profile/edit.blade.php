@extends('layouts.app')

@section('title', 'Edit Profile - ' . config('app.name'))

@section('content')
    <div class="pt-22 sm:pt-24 pb-5 sm:pb-6 max-w-[980px] mx-auto px-3 sm:px-6">
        <p class="text-red-500 text-[12px] font-bold uppercase tracking-[0.2em] mb-2">Account</p>
        <h1 class="text-[28px] sm:text-4xl font-extrabold tracking-[-0.02em]">Edit Profile</h1>
        <p class="text-white/35 text-[14px] mt-2 max-w-2xl leading-6">
            Update your public account details, avatar, and password from one place.
        </p>
    </div>

    <div class="max-w-[980px] mx-auto px-3 sm:px-6 pb-10 sm:pb-12">
        @if($errors->any())
            <div class="mb-5 rounded-2xl border border-red-500/15 bg-red-500/10 px-4 py-3 text-[13px] text-red-300">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="overflow-hidden rounded-[24px] sm:rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-3 sm:p-6">
            @csrf
            @method('PUT')

            <div class="grid gap-4 sm:gap-6 lg:grid-cols-[0.72fr_1.28fr]">
                <div class="min-w-0 rounded-[22px] sm:rounded-[24px] border border-white/[0.06] bg-[#0b0f16] p-4 sm:p-5">
                    <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Avatar</p>
                    <div class="mt-4 sm:mt-5 flex flex-col items-center text-center">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="" class="h-24 w-24 sm:h-28 sm:w-28 rounded-full object-cover ring-1 ring-white/10">
                        @else
                            <div class="flex h-24 w-24 sm:h-28 sm:w-28 items-center justify-center rounded-full bg-red-600/15 text-3xl font-bold text-red-300 ring-1 ring-white/10">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif

                        <label class="mt-4 sm:mt-5 inline-flex w-full max-w-[220px] cursor-pointer items-center justify-center rounded-xl border border-white/[0.08] bg-white/[0.04] px-4 py-2.5 text-[13px] font-medium text-white/80 transition hover:bg-white/[0.08] hover:text-white">
                            Upload new avatar
                            <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.gif,.webp" class="hidden">
                        </label>

                        @if($user->avatar)
                            <label class="mt-4 inline-flex flex-wrap items-center justify-center gap-2 text-center text-[13px] text-white/45">
                                <input type="checkbox" name="remove_avatar" value="1" class="rounded border-white/10 bg-white/5 text-red-600 focus:ring-red-500/30 focus:ring-offset-0">
                                Remove current avatar
                            </label>
                        @endif

                        <p class="mt-4 text-[12px] leading-5 text-white/30">JPG, PNG, GIF, or WebP up to 2MB.</p>
                    </div>
                </div>

                <div class="min-w-0 space-y-4 sm:space-y-6">
                    <div class="min-w-0 rounded-[22px] sm:rounded-[24px] border border-white/[0.06] bg-[#0b0f16] p-4 sm:p-5">
                        <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Profile Details</p>
                        <div class="mt-4 sm:mt-5 grid gap-4 sm:gap-5 md:grid-cols-2">
                            <label class="grid gap-2">
                                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Nickname</span>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    maxlength="16"
                                    class="w-full rounded-xl border border-white/[0.08] bg-[#111723] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none"
                                >
                            </label>

                            <label class="grid gap-2">
                                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Email</span>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email', $user->email) }}"
                                    class="w-full rounded-xl border border-white/[0.08] bg-[#111723] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none"
                                >
                            </label>
                        </div>

                        <div class="mt-4 rounded-2xl border border-white/[0.06] bg-white/[0.03] px-4 py-3 text-[12px] leading-5 text-white/40">
                            @if($user->hasVerifiedEmail())
                                Your email is currently verified. If you change it, we will send a new verification link.
                            @else
                                Your email is not verified yet. Check your inbox or the mail log for the verification link.
                            @endif
                        </div>
                    </div>

                    <div class="min-w-0 rounded-[22px] sm:rounded-[24px] border border-white/[0.06] bg-[#0b0f16] p-4 sm:p-5">
                        <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Password</p>
                        <div class="mt-4 sm:mt-5 grid gap-4 sm:gap-5">
                            <label class="grid gap-2">
                                <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Current Password</span>
                                <input
                                    type="password"
                                    name="current_password"
                                    class="w-full rounded-xl border border-white/[0.08] bg-[#111723] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none"
                                    placeholder="Required"
                                >
                            </label>

                            <div class="grid gap-4 sm:gap-5 md:grid-cols-2">
                                <label class="grid gap-2">
                                    <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">New Password</span>
                                    <input
                                        type="password"
                                        name="password"
                                        class="w-full rounded-xl border border-white/[0.08] bg-[#111723] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none"
                                        placeholder=""
                                    >
                                </label>

                                <label class="grid gap-2">
                                    <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Confirm Password</span>
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        class="w-full rounded-xl border border-white/[0.08] bg-[#111723] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none"
                                        placeholder="Repeat new password"
                                    >
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 sm:mt-6 flex justify-stretch sm:justify-end">
                <button type="submit" class="w-full sm:w-auto rounded-xl bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection
