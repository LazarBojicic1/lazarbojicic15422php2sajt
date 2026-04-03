@extends('layouts.app')

@section('title', 'My Requests - ' . config('app.name'))

@section('content')
    <div class="pt-24 pb-6 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10">
        <p class="text-red-500 text-[12px] font-bold uppercase tracking-[0.2em] mb-2">Account</p>
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-[-0.02em]">My Requests</h1>
                <p class="text-white/35 text-[14px] mt-2">
                    Track every movie or series request you sent and see whether it is still pending, approved, or rejected.
                </p>
            </div>
            <a href="{{ route('title-requests.create') }}" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">
                New Request
            </a>
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10 pb-12">
        @if($requests->count())
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach($requests as $request)
                    @php
                        $statusClasses = match ($request->status) {
                            'approved' => 'bg-emerald-500/15 text-emerald-200',
                            'rejected' => 'bg-red-500/15 text-red-200',
                            'reviewed' => 'bg-sky-500/15 text-sky-200',
                            default => 'bg-amber-500/15 text-amber-200',
                        };
                    @endphp
                    <article class="rounded-[24px] border border-white/[0.06] bg-white/[0.03] p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-[19px] font-semibold text-white">{{ $request->requested_title }}</p>
                                <p class="mt-1 text-[12px] uppercase tracking-[0.18em] text-white/30">
                                    {{ $request->requested_type === 'tv' ? 'Series' : 'Movie' }}
                                </p>
                            </div>
                            <span class="shrink-0 rounded-full px-3 py-1 text-[12px] font-semibold {{ $statusClasses }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>

                        <div class="mt-5 space-y-3 text-[13px] text-white/55">
                            <div>
                                <p class="text-white/30">Submitted</p>
                                <p class="mt-1 text-white/85">{{ $request->created_at->format('d M Y, H:i') }}</p>
                            </div>

                            <div>
                                <p class="text-white/30">Moderator</p>
                                <p class="mt-1 text-white/85">{{ $request->reviewedBy?->name ?? 'Waiting for review' }}</p>
                            </div>

                            <div>
                                <p class="text-white/30">Notes</p>
                                <p class="mt-1 leading-6 text-white/75">{{ $request->message ?: 'No extra notes were included.' }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10 flex justify-center">
                {{ $requests->links('partials.pagination') }}
            </div>
        @else
            <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] px-6 py-16 text-center">
                <p class="text-[20px] font-semibold text-white">You have not sent any requests yet.</p>
                <p class="mt-2 text-[14px] text-white/35">If something is missing from the catalog, send it here and the moderation team will review it.</p>
                <a href="{{ route('title-requests.create') }}" class="mt-6 inline-flex items-center rounded-xl bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">
                    Request a Title
                </a>
            </div>
        @endif
    </div>
@endsection
