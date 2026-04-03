@extends('admin.layouts.app')

@section('title', 'Title View - Admin')
@section('page-title', 'Title View')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
            <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Title</p>
            <h2 class="mt-3 text-3xl font-bold tracking-[-0.03em]">{{ $view->title?->name }}</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4">
                    <p class="text-[12px] text-white/30">User</p>
                    <p class="mt-2 text-[14px] font-semibold">{{ $view->user?->name ?? 'Guest' }}</p>
                </div>
                <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4">
                    <p class="text-[12px] text-white/30">Viewed at</p>
                    <p class="mt-2 text-[14px] font-semibold">{{ $view->viewed_at?->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 text-[13px] text-white/45">
            <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Meta</p>
            <div class="mt-4 space-y-3">
                <p>Title ID: {{ $view->title_id }}</p>
                <p>IP Hash: {{ $view->ip_hash ?? 'None' }}</p>
                <p>User ID: {{ $view->user_id ?? 'Guest' }}</p>
            </div>
            <form method="POST" action="{{ route('admin.title-views.destroy', $view) }}" class="mt-6" onsubmit="return confirm('Delete this title view?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-[13px] font-semibold text-red-200 transition hover:bg-red-500/20">Delete view entry</button>
            </form>
        </div>
    </div>
@endsection
