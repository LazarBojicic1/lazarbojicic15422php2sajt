<div class="comment-item {{ $depth > 0 ? 'ml-8 sm:ml-12' : '' }}" data-comment-id="{{ $comment->id }}">
    <div class="flex gap-3 py-4 {{ $depth === 0 ? 'border-b border-white/[0.04]' : '' }}">
        <div class="shrink-0">
            @if($comment->user && $comment->user->avatar)
                <img src="{{ asset('storage/' . $comment->user->avatar) }}" class="w-7 h-7 rounded-full object-cover ring-1 ring-white/10" alt="">
            @else
                <div class="w-7 h-7 rounded-full bg-white/[0.08] flex items-center justify-center text-[10px] font-bold text-white/40 ring-1 ring-white/5">
                    {{ $comment->user ? strtoupper(substr($comment->user->name, 0, 1)) : '?' }}
                </div>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[12px] font-semibold text-white/70">{{ $comment->user->name ?? 'Deleted User' }}</span>
                <span class="text-[11px] text-white/20">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-[13px] text-white/55 leading-relaxed whitespace-pre-line break-words">{{ $comment->content }}</p>
            <div class="flex items-center gap-4 mt-2">
                @auth
                <button type="button" class="comment-reply-btn text-[11px] text-white/25 hover:text-white/50 transition font-medium" data-comment-id="{{ $comment->id }}">Reply</button>
                @if($comment->user_id === auth()->id())
                    <button type="button" class="comment-delete-btn text-[11px] text-white/25 hover:text-red-400 transition font-medium" data-comment-id="{{ $comment->id }}">Delete</button>
                @else
                    <button type="button" class="comment-report-btn text-[11px] text-white/25 hover:text-red-400 transition font-medium" data-comment-id="{{ $comment->id }}">Report</button>
                @endif
                @endauth
            </div>

            {{-- Reply form (hidden by default) --}}
            @auth
            <div class="reply-form hidden mt-3" data-reply-to="{{ $comment->id }}">
                <div class="flex gap-2">
                    <textarea
                        placeholder="Write a reply..."
                        rows="2"
                        maxlength="2000"
                        class="reply-input flex-1 bg-white/[0.04] border border-white/[0.08] rounded-lg px-3 py-2 text-[12px] text-white/80 placeholder-white/25 resize-none focus:outline-none focus:border-red-500/30 transition"
                    ></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 mt-2">
                    <button type="button" class="reply-cancel text-[12px] text-white/30 hover:text-white/50 transition px-3 py-1.5">Cancel</button>
                    <button type="button" class="reply-submit bg-red-600 hover:bg-red-500 disabled:opacity-30 text-white text-[12px] font-semibold px-4 py-1.5 rounded transition" disabled>Reply</button>
                </div>
            </div>
            @endauth
        </div>
    </div>

    {{-- Nested replies --}}
    @if($comment->replies && $comment->replies->count())
        @foreach($comment->replies as $reply)
            @include('partials.comment-single', ['comment' => $reply, 'depth' => $depth + 1])
        @endforeach
    @endif
</div>
