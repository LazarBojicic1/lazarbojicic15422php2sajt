@extends('layouts.app')

@section('title', 'Sign Up - ' . config('app.name'))

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-20">
    <div class="w-full max-w-[440px]">
        <div class="bg-white/[0.03] backdrop-blur-xl rounded-lg p-8 sm:p-10 border border-white/[0.04]">
            <h1 class="text-[28px] font-bold mb-1 tracking-[-0.02em]">Create Account</h1>
            <p class="text-white/30 text-[13px] mb-7">Start streaming movies and series</p>

            @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/15 text-red-400 rounded px-4 py-3 mb-5 text-[13px] space-y-0.5">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form id="register-form" method="POST" action="{{ route('register') }}" enctype="multipart/form-data" novalidate class="space-y-5">
                @csrf

                {{-- Avatar --}}
                <div class="flex justify-center mb-2">
                    <label for="avatar" class="relative cursor-pointer group">
                        <div id="avatar-preview" class="w-20 h-20 rounded-full bg-white/[0.05] border-2 border-dashed border-white/[0.08] flex items-center justify-center overflow-hidden transition group-hover:border-white/20">
                            <svg class="w-7 h-7 text-white/20 group-hover:text-white/40 transition" id="avatar-placeholder" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
                            <img id="avatar-img" class="w-full h-full object-cover hidden" alt="Avatar preview">
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-red-600 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </div>
                        <input type="file" name="avatar" id="avatar" accept=".jpg,.jpeg,.png,.gif,.webp" class="hidden">
                    </label>
                </div>
                <div id="avatar-feedback" class="text-center text-[12px] hidden"></div>

                {{-- Nickname --}}
                <div>
                    <label for="name" class="block text-[12px] font-semibold text-white/40 uppercase tracking-wider mb-1.5">Nickname</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        maxlength="16"
                        autofocus
                        class="w-full bg-white/[0.04] border border-white/[0.06] text-white rounded px-4 py-2.5 text-[14px] focus:outline-none transition placeholder-white/15"
                        placeholder="e.g. cool_user42"
                    >
                    <div id="name-feedback" class="mt-1.5 text-[12px] hidden"></div>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-[12px] font-semibold text-white/40 uppercase tracking-wider mb-1.5">Email</label>
                    <input
                        type="text"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        class="w-full bg-white/[0.04] border border-white/[0.06] text-white rounded px-4 py-2.5 text-[14px] focus:outline-none transition placeholder-white/15"
                        placeholder="your@email.com"
                    >
                    <div id="email-feedback" class="mt-1.5 text-[12px] hidden"></div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-[12px] font-semibold text-white/40 uppercase tracking-wider mb-1.5">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="w-full bg-white/[0.04] border border-white/[0.06] text-white rounded px-4 py-2.5 text-[14px] focus:outline-none transition placeholder-white/15"
                        placeholder="Strong password"
                    >
                    <div id="pw-requirements" class="mt-2.5 grid grid-cols-2 gap-x-3 gap-y-1.5 hidden">
                        <div id="pw-length" class="flex items-center gap-1.5 text-[11px] text-white/25 transition-colors">
                            <span class="pw-dot w-1 h-1 rounded-full bg-white/20 transition-colors shrink-0"></span>
                            6 – 30 characters
                        </div>
                        <div id="pw-upper" class="flex items-center gap-1.5 text-[11px] text-white/25 transition-colors">
                            <span class="pw-dot w-1 h-1 rounded-full bg-white/20 transition-colors shrink-0"></span>
                            One uppercase
                        </div>
                        <div id="pw-lower" class="flex items-center gap-1.5 text-[11px] text-white/25 transition-colors">
                            <span class="pw-dot w-1 h-1 rounded-full bg-white/20 transition-colors shrink-0"></span>
                            One lowercase
                        </div>
                        <div id="pw-number" class="flex items-center gap-1.5 text-[11px] text-white/25 transition-colors">
                            <span class="pw-dot w-1 h-1 rounded-full bg-white/20 transition-colors shrink-0"></span>
                            One number
                        </div>
                        <div id="pw-special" class="flex items-center gap-1.5 text-[11px] text-white/25 transition-colors">
                            <span class="pw-dot w-1 h-1 rounded-full bg-white/20 transition-colors shrink-0"></span>
                            One special character
                        </div>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-[12px] font-semibold text-white/40 uppercase tracking-wider mb-1.5">Confirm Password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="w-full bg-white/[0.04] border border-white/[0.06] text-white rounded px-4 py-2.5 text-[14px] focus:outline-none transition placeholder-white/15"
                        placeholder="Repeat password"
                    >
                    <div id="confirm-feedback" class="mt-1.5 text-[12px] hidden"></div>
                </div>

                <button id="register-btn" type="submit" disabled class="w-full bg-red-600/40 text-white/40 font-bold py-2.5 rounded text-[14px] mt-3 cursor-not-allowed transition-all">
                    Create Account
                </button>
            </form>

            <p class="mt-7 text-[13px] text-white/25 text-center">
                Already have an account? <a href="{{ route('login') }}" class="text-white/70 hover:text-white transition">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('vite')
    @vite('resources/js/pages/auth-register.js')
@endpush
