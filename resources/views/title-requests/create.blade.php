@extends('layouts.app')

@section('title', 'Request a Title - ' . config('app.name'))

@section('content')
    <div class="pt-24 pb-6 max-w-[1100px] mx-auto px-4 sm:px-6">
        <p class="text-red-500 text-[12px] font-bold uppercase tracking-[0.2em] mb-2">Catalog Request</p>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-[-0.02em]">Request a Movie or Series</h1>
        <p class="text-white/35 text-[14px] mt-2 max-w-2xl">
            Can't find something in the catalog? Send it here and a moderator can review the request from the admin panel.
        </p>
    </div>

    <div class="max-w-[1100px] mx-auto px-4 sm:px-6 pb-12">
        @php
            $requestedType = old('requested_type', $suggestedType ?? 'movie');
            $typeOptions = [
                ['value' => 'movie', 'text' => 'Movie'],
                ['value' => 'tv', 'text' => 'Series'],
            ];
            $requestedTypeText = collect($typeOptions)->firstWhere('value', $requestedType)['text'] ?? 'Movie';
        @endphp

        <div class="max-w-2xl mx-auto">
            <div class="mb-5 rounded-[24px] border border-white/[0.06] bg-white/[0.03] p-4 sm:p-5">
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Signed In Account</p>
                <div class="mt-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="truncate text-[16px] font-semibold text-white">{{ $currentUser->name }}</p>
                        <p class="truncate text-[13px] text-white/35">{{ $currentUser->email }}</p>
                    </div>
                    <a href="{{ route('my-requests') }}" class="shrink-0 rounded-xl border border-white/[0.08] bg-white/[0.04] px-4 py-2.5 text-[13px] font-medium text-white/75 transition hover:bg-white/[0.08] hover:text-white">
                        View My Requests
                    </a>
                </div>
            </div>

            <form id="title-request-form" method="POST" action="{{ route('title-requests.store') }}" novalidate class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 sm:p-6">
                @csrf

                <div class="grid gap-5">
                    <label class="grid gap-2">
                        <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Requested Title</span>
                        <input
                            type="text"
                            id="requested_title"
                            name="requested_title"
                            value="{{ old('requested_title', $suggestedTitle) }}"
                            minlength="2"
                            maxlength="150"
                            class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none"
                            placeholder="Movie or series name..."
                        >
                        <div id="requested-title-feedback" class="text-[12px] hidden"></div>
                        @error('requested_title')
                            <span class="text-[12px] text-red-300">{{ $message }}</span>
                        @enderror
                    </label>

                    <div class="grid gap-2">
                        <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Type</span>
                        <div class="relative" data-dropdown data-dropdown-submit="false">
                            <input type="hidden" name="requested_type" value="{{ $requestedType }}">
                            <button
                                type="button"
                                data-dropdown-toggle
                                class="w-full flex items-center justify-between rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] font-medium text-white transition hover:bg-white/[0.07]"
                            >
                                <span data-dropdown-label>{{ $requestedTypeText }}</span>
                                <svg class="w-4 h-4 text-white/40 transition-transform" data-dropdown-chevron fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div
                                data-dropdown-menu
                                class="hidden absolute left-0 right-0 top-full mt-1 z-50 rounded-lg shadow-2xl max-h-60 overflow-y-auto custom-scrollbar py-1"
                                style="background:#141418;border:1px solid rgba(255,255,255,0.08)"
                            >
                                @foreach($typeOptions as $option)
                                    <button
                                        type="button"
                                        data-dropdown-option
                                        data-value="{{ $option['value'] }}"
                                        class="w-full text-left px-3 py-2.5 text-[13px] transition cursor-pointer {{ $requestedType === $option['value'] ? 'bg-red-500/15 text-red-400 font-semibold' : 'text-white/60 hover:bg-white/[0.06] hover:text-white/90' }}"
                                    >
                                        {{ $option['text'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @error('requested_type')
                            <span class="text-[12px] text-red-300">{{ $message }}</span>
                        @enderror
                    </div>

                    <label class="grid gap-2">
                        <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Notes for the Moderator</span>
                        <textarea
                            id="request_message"
                            name="message"
                            rows="6"
                            maxlength="1000"
                            class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none"
                            placeholder="Optional: release year, original title, franchise name, or anything else that helps us find it."
                        >{{ old('message') }}</textarea>
                        <div id="request-message-feedback" class="text-[12px] hidden"></div>
                        @error('message')
                            <span class="text-[12px] text-red-300">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <div class="mt-6 flex justify-end">
                    <button id="title-request-submit" type="submit" class="rounded-xl bg-red-600/40 px-5 py-3 text-[13px] font-semibold text-white/40 transition">
                        Send Request
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('vite')
    @vite('resources/js/pages/title-requests.js')
@endpush
