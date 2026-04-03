@extends('admin.layouts.app')

@section('title', 'Comments - Admin')
@section('page-title', 'Comments')

@section('content')
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Moderation</p>
                <h2 class="mt-2 text-2xl font-bold tracking-[-0.02em]">Comments</h2>
            </div>
            <p class="text-[13px] text-white/35">Review, edit, or delete user-generated comments.</p>
        </div>

        <div class="mt-6 overflow-x-auto rounded-2xl border border-white/[0.06]">
            <table class="min-w-full divide-y divide-white/[0.06]">
                <thead class="bg-white/[0.03] text-left text-[11px] uppercase tracking-[0.18em] text-white/35">
                    <tr>
                        <th class="px-4 py-3">Comment</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.05] bg-[#0b0f16]">
                    @forelse($comments ?? [] as $comment)
                        <tr class="align-top">
                            <td class="px-4 py-4">
                                <p class="text-[13px] leading-6 text-white/85">{{ \Illuminate\Support\Str::limit($comment->content, 120) }}</p>
                                <p class="mt-2 text-[12px] text-white/35">{{ $comment->created_at?->format('M d, Y h:i A') }}</p>
                            </td>
                            <td class="px-4 py-4 text-[13px] text-white/70">{{ $comment->user?->name }}</td>
                            <td class="px-4 py-4 text-[13px] text-white/70">{{ $comment->title?->name }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-[12px] font-semibold {{ $comment->is_approved ? 'bg-emerald-500/15 text-emerald-200' : 'bg-amber-500/15 text-amber-200' }}">
                                    {{ $comment->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.comments.edit', $comment) }}" class="rounded-lg border border-white/[0.08] bg-white/[0.03] px-3 py-2 text-[12px] text-white/75">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-[13px] text-white/35">No comments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
