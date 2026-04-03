@extends('layouts.app')

@section('title', 'Verify Email - ' . config('app.name'))

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 pt-16">
    <div class="w-full max-w-[420px] text-center">
        <div class="bg-white/[0.03] backdrop-blur-xl rounded-lg p-8 sm:p-10 border border-white/[0.04]">
            <div class="mx-auto w-14 h-14 bg-red-500/10 rounded-full flex items-center justify-center mb-5">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
            </div>

            <h1 class="text-[24px] font-bold mb-2 tracking-[-0.02em]">Check your email</h1>
            <p class="text-white/35 text-[13px] mb-6 leading-relaxed">
                We've sent a verification link to<br>
                <strong class="text-white/70">{{ Auth::user()->email }}</strong>
            </p>

            @if(session('message'))
                <div class="bg-emerald-500/10 border border-emerald-500/15 text-emerald-400 rounded px-4 py-3 mb-5 text-[13px]">
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-white/[0.06] hover:bg-white/[0.10] text-white/80 font-medium py-2.5 rounded transition text-[13px]">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-[13px] text-white/20 hover:text-white/50 transition">
                    Sign out
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-white/[0.04]">
                <p class="text-[11px] text-white/15">
                    Since mail is set to log, check <code class="text-white/30">storage/logs/laravel.log</code> for the verification link.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
