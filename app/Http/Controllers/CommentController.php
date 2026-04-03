<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Request $request, int $titleId)
    {
        $comments = Comment::where('title_id', $titleId)
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->with([
                'user',
                'replies' => fn ($q) => $q->where('is_approved', true)->with('user')->oldest(),
            ])
            ->latest()
            ->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.comments-list', compact('comments'))->render(),
                'next_page' => $comments->nextPageUrl(),
            ]);
        }

        return response()->json(['comments' => $comments]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_id' => 'required|exists:titles,id',
            'parent_id' => 'nullable|exists:comments,id',
            'content' => 'required|string|min:1|max:2000',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'title_id' => $request->title_id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'is_approved' => true,
        ]);

        $comment->load('user');

        return response()->json([
            'html' => view('partials.comment-single', ['comment' => $comment, 'depth' => $request->parent_id ? 1 : 0])->render(),
            'comment' => $comment,
        ]);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['status' => 'deleted']);
    }

    public function report(Request $request, Comment $comment)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $comment->loadMissing(['user', 'title', 'parent.user']);

        $existing = CommentReport::where('comment_id', $comment->id)
            ->where('reported_by_user_id', Auth::id())
            ->first();

        if ($existing) {
            return response()->json(['error' => 'You have already reported this comment.'], 422);
        }

        CommentReport::create([
            'comment_id' => $comment->id,
            'reported_by_user_id' => Auth::id(),
            'reason' => trim((string) $request->reason),
            'comment_snapshot' => $comment->content,
            'parent_comment_snapshot' => $comment->parent?->content,
            'comment_author_snapshot' => $comment->user?->name,
            'title_snapshot' => $comment->title?->name,
            'status' => 'pending',
        ]);

        return response()->json(['status' => 'reported']);
    }
}
