@extends('admin.layouts.app')

@section('title', 'Edit Comment - Admin')
@section('page-title', 'Edit Comment')

@section('content')
    @php
        $userOptions = collect($users ?? [])
            ->map(fn ($user) => ['value' => (string) $user->id, 'text' => $user->name . ' (' . ($user->role?->name ?? 'user') . ')'])
            ->all();
        $titleOptions = collect($titles ?? [])
            ->map(fn ($title) => ['value' => (string) $title->id, 'text' => $title->name])
            ->all();
        $parentDropdownOptions = collect([['value' => '', 'text' => 'No parent comment']])
            ->merge(
                collect($parentOptions ?? [])->map(fn ($parent) => [
                    'value' => (string) $parent->id,
                    'text' => \Illuminate\Support\Str::limit($parent->content, 70),
                ])
            )
            ->all();
    @endphp

    <form method="POST" action="{{ route('admin.comments.update', $comment) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid gap-6 lg:grid-cols-[1.4fr_0.6fr]">
            <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
                <label class="grid gap-2">
                    <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Content</span>
                    <textarea name="content" rows="8" class="rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-[14px] text-white focus:border-red-500/40 focus:outline-none">{{ old('content', $comment->content ?? '') }}</textarea>
                </label>
            </div>
            <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 space-y-4">
                <label class="grid gap-2">
                    <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Author</span>
                    @include('admin.partials.select-dropdown', [
                        'name' => 'user_id',
                        'current' => (string) old('user_id', $comment->user_id ?? ''),
                        'options' => $userOptions,
                        'placeholder' => 'Choose an author',
                    ])
                </label>
                <label class="grid gap-2">
                    <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Title</span>
                    @include('admin.partials.select-dropdown', [
                        'name' => 'title_id',
                        'current' => (string) old('title_id', $comment->title_id ?? ''),
                        'options' => $titleOptions,
                        'placeholder' => 'Choose a title',
                    ])
                </label>
                <label class="grid gap-2">
                    <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Parent Comment</span>
                    @include('admin.partials.select-dropdown', [
                        'name' => 'parent_id',
                        'current' => (string) old('parent_id', $comment->parent_id ?? ''),
                        'options' => $parentDropdownOptions,
                        'placeholder' => 'No parent comment',
                    ])
                </label>
                <label class="flex items-center gap-3 rounded-2xl border border-white/[0.06] bg-[#0b0f16] px-4 py-4">
                    <input type="checkbox" name="is_approved" value="1" @checked(old('is_approved', $comment->is_approved ?? true)) class="h-4 w-4 rounded border-white/20 bg-transparent text-red-600">
                    <span>
                        <span class="block text-[14px] font-semibold text-white">Approved</span>
                        <span class="block text-[12px] text-white/35">Hide from public if unchecked.</span>
                    </span>
                </label>
                <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4 text-[13px] text-white/45">
                    <p class="text-white/80 font-semibold">Context</p>
                    <p class="mt-2">Created {{ optional($comment->created_at)->format('M d, Y h:i A') ?: 'just now' }}</p>
                    <p class="mt-1">Current title: {{ $comment->title?->name ?? 'Unknown title' }}</p>
                    <p class="mt-1">Replies: {{ $comment->replies->count() }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.comments.index') }}" class="rounded-xl border border-white/[0.08] bg-white/[0.03] px-4 py-3 text-[13px] text-white/70">Cancel</a>
            <button type="submit" class="rounded-xl bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">Update comment</button>
        </div>
    </form>
    <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" class="mt-4" onsubmit="return confirm('Delete this comment?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-[13px] font-semibold text-red-200 transition hover:bg-red-500/20">Delete comment</button>
    </form>
@endsection
