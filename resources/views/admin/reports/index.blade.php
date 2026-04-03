@extends('admin.layouts.app')

@section('title', 'Reports - Admin')
@section('page-title', 'Reports')

@section('content')
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Content moderation</p>
                <h2 class="mt-2 text-2xl font-bold tracking-[-0.02em]">Comment reports</h2>
            </div>
            <p class="text-[13px] text-white/35">Review the full context and decide whether the comment should stay, be hidden, or be deleted.</p>
        </div>

        <div class="mt-6 overflow-x-auto rounded-2xl border border-white/[0.06]">
            <table class="min-w-full divide-y divide-white/[0.06]">
                <thead class="bg-white/[0.03] text-left text-[11px] uppercase tracking-[0.18em] text-white/35">
                    <tr>
                        <th class="px-4 py-3">Report</th>
                        <th class="px-4 py-3">Context</th>
                        <th class="px-4 py-3">Outcome</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.05] bg-[#0b0f16]">
                    @forelse($reports ?? [] as $report)
                        @php
                            $statusClasses = match ($report->status) {
                                'kept' => 'bg-emerald-500/15 text-emerald-200',
                                'hidden' => 'bg-amber-500/15 text-amber-200',
                                'deleted' => 'bg-red-500/15 text-red-200',
                                default => 'bg-sky-500/15 text-sky-200',
                            };
                        @endphp
                        <tr>
                            <td class="px-4 py-4">
                                <p class="text-[13px] font-medium text-white/85">{{ \Illuminate\Support\Str::limit($report->reason ?? 'No reason provided.', 100) }}</p>
                                <p class="mt-1 text-[12px] text-white/35">
                                    Reported by {{ $report->reportedBy?->name ?? 'Unknown user' }} on {{ $report->created_at->format('d M Y, H:i') }}
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-[13px] text-white/75">{{ \Illuminate\Support\Str::limit($report->comment?->content ?? $report->comment_snapshot ?? 'Comment removed from the database.', 100) }}</p>
                                <p class="mt-1 text-[12px] text-white/35">
                                    {{ $report->comment?->title?->name ?? $report->title_snapshot ?? 'Unknown title' }}
                                    •
                                    {{ $report->comment?->user?->name ?? $report->comment_author_snapshot ?? 'Unknown author' }}
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-[12px] font-semibold {{ $statusClasses }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.reports.edit', $report) }}" class="rounded-lg border border-white/[0.08] bg-white/[0.03] px-3 py-2 text-[12px] text-white/75">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-12 text-center text-[13px] text-white/35">No reports found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
