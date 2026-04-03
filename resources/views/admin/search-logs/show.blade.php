@extends('admin.layouts.app')

@section('title', 'Search Log - Admin')
@section('page-title', 'Search Log')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
            <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Query</p>
            <h2 class="mt-3 text-3xl font-bold tracking-[-0.03em]">{{ $log->query }}</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4">
                    <p class="text-[12px] text-white/30">Results count</p>
                    <p class="mt-2 text-2xl font-bold">{{ $log->results_count }}</p>
                </div>
                <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4">
                    <p class="text-[12px] text-white/30">Selected title</p>
                    <p class="mt-2 text-2xl font-bold">{{ $log->selectedTitle?->name ?? 'None' }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 text-[13px] text-white/45">
            <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Metadata</p>
            <div class="mt-4 space-y-3">
                <p>User: {{ $log->user?->name ?? 'Guest' }}</p>
                <p>Searched at: {{ $log->searched_at?->format('M d, Y h:i A') }}</p>
                <p>Selected ID: {{ $log->selected_title_id ?? 'None' }}</p>
            </div>
            <form method="POST" action="{{ route('admin.search-logs.destroy', $log) }}" class="mt-6" onsubmit="return confirm('Delete this search log?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-[13px] font-semibold text-red-200 transition hover:bg-red-500/20">Delete log entry</button>
            </form>
        </div>
    </div>
@endsection
