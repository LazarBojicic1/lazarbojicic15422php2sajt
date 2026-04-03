@extends('admin.layouts.app')

@section('title', 'Review Report - Admin')
@section('page-title', 'Review Report')

@section('content')
    @php
        $selectedStatus = old('status', $report->status ?? 'pending');
        $reportedComment = $report->comment;
        $commentTitle = $reportedComment?->title?->name ?? $report->title_snapshot ?? 'Unknown title';
        $commentAuthor = $reportedComment?->user?->name ?? $report->comment_author_snapshot ?? 'Unknown author';
        $commentContent = $reportedComment?->content ?? $report->comment_snapshot ?? 'This comment is no longer available.';
        $parentContent = $reportedComment?->parent?->content ?? $report->parent_comment_snapshot;
        $replies = $reportedComment?->replies?->take(3) ?? collect();
        $commentState = $reportedComment
            ? ($reportedComment->is_approved ? 'Visible on the public site' : 'Hidden from the public site')
            : 'Comment no longer exists in the database';
        $statusOptions = [
            'pending' => ['label' => 'Keep Report Open', 'description' => 'Do not apply a moderation action yet.'],
            'kept' => ['label' => 'Keep Comment', 'description' => 'The comment is acceptable and should stay visible.'],
            'hidden' => ['label' => 'Hide Comment', 'description' => 'Remove the comment from the public thread but keep it in the database.'],
            'deleted' => ['label' => 'Delete Comment', 'description' => 'Remove the comment entirely and preserve only the report audit trail.'],
        ];
    @endphp

    <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid gap-6 lg:grid-cols-[1.4fr_0.6fr]">
            <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 space-y-4">
                <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4">
                    <p class="text-[12px] uppercase tracking-[0.18em] text-white/30">Report details</p>
                    <div class="mt-3 grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-[12px] text-white/30">Reason</p>
                            <p class="mt-1 text-[14px] leading-6 text-white/85">{{ $report->reason ?: 'No reason was provided.' }}</p>
                        </div>
                        <div>
                            <p class="text-[12px] text-white/30">Reporter</p>
                            <p class="mt-1 text-[14px] leading-6 text-white/85">
                                {{ $report->reportedBy?->name ?? 'Unknown user' }}<br>
                                <span class="text-white/35">{{ $report->created_at->format('d M Y, H:i') }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4">
                    <p class="text-[12px] uppercase tracking-[0.18em] text-white/30">Comment context</p>
                    <div class="mt-3 grid gap-4">
                        <div class="rounded-2xl border border-white/[0.06] bg-white/[0.03] p-4">
                            <p class="text-[12px] text-white/30">Title</p>
                            <p class="mt-1 text-[14px] font-medium text-white/90">{{ $commentTitle }}</p>
                            <p class="mt-3 text-[12px] text-white/30">Comment author</p>
                            <p class="mt-1 text-[14px] text-white/85">{{ $commentAuthor }}</p>
                            <p class="mt-3 text-[12px] text-white/30">Current state</p>
                            <p class="mt-1 text-[14px] text-white/85">{{ $commentState }}</p>
                        </div>

                        @if($parentContent)
                            <div class="rounded-2xl border border-white/[0.06] bg-white/[0.03] p-4">
                                <p class="text-[12px] uppercase tracking-[0.18em] text-white/30">Parent comment</p>
                                <p class="mt-3 text-[14px] leading-7 text-white/75">{{ $parentContent }}</p>
                            </div>
                        @endif

                        <div class="rounded-2xl border border-white/[0.06] bg-white/[0.03] p-4">
                            <p class="text-[12px] uppercase tracking-[0.18em] text-white/30">Reported comment</p>
                            <p class="mt-3 text-[14px] leading-7 text-white/85">{{ $commentContent }}</p>
                        </div>

                        @if($replies->count())
                            <div class="rounded-2xl border border-white/[0.06] bg-white/[0.03] p-4">
                                <p class="text-[12px] uppercase tracking-[0.18em] text-white/30">Replies</p>
                                <div class="mt-3 space-y-3">
                                    @foreach($replies as $reply)
                                        <div class="rounded-xl border border-white/[0.05] bg-[#0b0f16] px-4 py-3">
                                            <p class="text-[12px] text-white/30">{{ $reply->user?->name ?? 'Unknown user' }}</p>
                                            <p class="mt-1 text-[13px] leading-6 text-white/75">{{ $reply->content }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <label class="grid gap-2">
                    <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Review Note</span>
                    <textarea name="review_note" rows="5" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">{{ old('review_note', $report->review_note ?? '') }}</textarea>
                </label>
            </div>
            <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 space-y-4">
                <div class="grid gap-3">
                    <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Decision</p>
                    @foreach($statusOptions as $value => $option)
                        <label class="block cursor-pointer">
                            <input
                                type="radio"
                                name="status"
                                value="{{ $value }}"
                                class="peer sr-only"
                                @checked($selectedStatus === $value)
                            >
                            <span class="block rounded-2xl border border-white/[0.06] bg-[#0b0f16] px-4 py-4 transition peer-hover:border-white/[0.12] peer-checked:border-red-500/30 peer-checked:bg-red-500/10">
                                <span class="block text-[14px] font-semibold text-white">{{ $option['label'] }}</span>
                                <span class="mt-1 block text-[12px] leading-5 text-white/40">{{ $option['description'] }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4 text-[13px] text-white/45">
                    <p class="text-white/80 font-semibold">Moderator guidance</p>
                    <p class="mt-2">Use “Hide” when the comment should be removed from public view but still kept for audit. Use “Delete” only when the content should be removed entirely.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.reports.index') }}" class="rounded-xl border border-white/[0.08] bg-white/[0.03] px-4 py-3 text-[13px] text-white/70">Cancel</a>
            <button type="submit" class="rounded-xl bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">Apply decision</button>
        </div>
    </form>
    <form method="POST" action="{{ route('admin.reports.destroy', $report) }}" class="mt-4" onsubmit="return confirm('Delete this report?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-[13px] font-semibold text-red-200 transition hover:bg-red-500/20">Delete report</button>
    </form>
@endsection
