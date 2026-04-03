@extends('layouts.app')

@section('title', 'Forgot Password - ' . config('app.name'))

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 pt-16">
    <div class="w-full max-w-[400px]">
        <div class="bg-white/[0.03] backdrop-blur-xl rounded-lg p-8 sm:p-10 border border-white/[0.04]">
            <h1 class="text-[28px] font-bold mb-1 tracking-[-0.02em]">Forgot Password</h1>
            <p class="text-white/30 text-[13px] mb-7">Enter your email and we&apos;ll send you a reset link.</p>

            @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/15 text-red-400 rounded px-4 py-3 mb-5 text-[13px] space-y-0.5">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" novalidate class="space-y-4">
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
                </div>

                <button type="submit" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-2.5 rounded text-[14px] mt-2 transition-all">
                    Send Reset Link
                </button>
            </form>

            <p class="mt-7 text-[13px] text-white/25 text-center">
                Remembered your password? <a href="{{ route('login') }}" class="text-white/70 hover:text-white transition">Back to sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
