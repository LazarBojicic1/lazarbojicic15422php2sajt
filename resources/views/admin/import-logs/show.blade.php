@extends('admin.layouts.app')

@section('title', 'Import Log - Admin')
@section('page-title', 'Import Log')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
            <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Action</p>
            <h2 class="mt-3 text-3xl font-bold tracking-[-0.03em]">{{ $log->action }}</h2>
            <p class="mt-2 text-[13px] text-white/35">{{ $log->tmdb_type }} #{{ $log->tmdb_id }}</p>
            <div class="mt-6 rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4">
                <p class="text-[12px] uppercase tracking-[0.18em] text-white/30">Message</p>
                <p class="mt-3 text-[14px] leading-7 text-white/85">{{ $log->message ?? 'No message recorded.' }}</p>
            </div>
        </div>
        <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 text-[13px] text-white/45">
            <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Metadata</p>
            <div class="mt-4 space-y-3">
                <p>Status: {{ ucfirst($log->status) }}</p>
                <p>Admin: {{ $log->admin?->name ?? 'System' }}</p>
                <p>Created: {{ $log->created_at?->format('M d, Y h:i A') }}</p>
            </div>
            <form method="POST" action="{{ route('admin.import-logs.destroy', $log) }}" class="mt-6" onsubmit="return confirm('Delete this import log?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-[13px] font-semibold text-red-200 transition hover:bg-red-500/20">Delete log entry</button>
            </form>
        </div>
    </div>
@endsection
