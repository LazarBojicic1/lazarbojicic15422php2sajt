@extends('admin.layouts.app')

@section('title', 'Title Views - Admin')
@section('page-title', 'Title Views')

@section('content')
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Watch analytics</p>
                <h2 class="mt-2 text-2xl font-bold tracking-[-0.02em]">Title views</h2>
            </div>
            <p class="text-[13px] text-white/35">A log entry is created when a watch page is viewed.</p>
        </div>

        <div class="mt-6 overflow-x-auto rounded-2xl border border-white/[0.06]">
            <table class="min-w-full divide-y divide-white/[0.06]">
                <thead class="bg-white/[0.03] text-left text-[11px] uppercase tracking-[0.18em] text-white/35">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">When</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.05] bg-[#0b0f16]">
                    @forelse($views ?? [] as $view)
                        <tr>
                            <td class="px-4 py-4 font-semibold text-white">{{ $view->title?->name }}</td>
                            <td class="px-4 py-4 text-[13px] text-white/70">{{ $view->user?->name ?? 'Guest' }}</td>
                            <td class="px-4 py-4 text-[13px] text-white/60">{{ $view->viewed_at?->format('M d, Y h:i A') }}</td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.title-views.show', $view) }}" class="rounded-lg border border-white/[0.08] bg-white/[0.03] px-3 py-2 text-[12px] text-white/75">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-12 text-center text-[13px] text-white/35">No views found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
