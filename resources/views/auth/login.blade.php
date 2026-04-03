@extends('layouts.app')

@section('title', 'Sign In - ' . config('app.name'))

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 pt-16">
    <div class="w-full max-w-[400px]">
        <div class="bg-white/[0.03] backdrop-blur-xl rounded-lg p-8 sm:p-10 border border-white/[0.04]">
            <h1 class="text-[28px] font-bold mb-1 tracking-[-0.02em]">Sign In</h1>
            <p class="text-white/30 text-[13px] mb-7">Welcome back to {{ config('app.name') }}</p>

            @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/15 text-red-400 rounded px-4 py-3 mb-5 text-[13px] space-y-0.5">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form id="login-form" method="POST" action="{{ route('login') }}" novalidate class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-[12px] font-semibold text-white/40 uppercase tracking-wider mb-1.5">Email</label>
                    <input
                        type="text"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        autofocus
                        class="w-full bg-white/[0.04] border border-white/[0.06] text-white rounded px-4 py-2.5 text-[14px] focus:outline-none transition placeholder-white/15"
                        placeholder="your@email.com"
                    >
                    <div id="email-feedback" class="mt-1.5 text-[12px] hidden"></div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-[12px] font-semibold text-white/40 uppercase tracking-wider">Password</label>
                        <a href="{{ route('password.request') }}" class="text-[12px] text-white/35 hover:text-white/70 transition">Forgot password?</a>
                    </div>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="w-full bg-white/[0.04] border border-white/[0.06] text-white rounded px-4 py-2.5 text-[14px] focus:outline-none transition placeholder-white/15"
                        placeholder="Your password"
                    >
                    <div id="password-feedback" class="mt-1.5 text-[12px] hidden"></div>
                </div>

                <div class="flex items-center pt-1">
                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        class="w-3.5 h-3.5 bg-white/5 border-white/10 rounded text-red-600 focus:ring-red-500/30 focus:ring-offset-0"
                    >
                    <label for="remember" class="ml-2 text-[13px] text-white/35">Remember me</label>
                </div>

                <button id="login-btn" type="submit" disabled class="w-full bg-red-600/40 text-white/40 font-bold py-2.5 rounded text-[14px] mt-2 cursor-not-allowed transition-all">
                    Sign In
                </button>
            </form>

            <p class="mt-7 text-[13px] text-white/25 text-center">
                Don't have an account? <a href="{{ route('register') }}" class="text-white/70 hover:text-white transition">Sign up</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('vite')
    @vite('resources/js/pages/auth-login.js')
@endpush
