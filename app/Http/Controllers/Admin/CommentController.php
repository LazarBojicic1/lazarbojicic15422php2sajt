<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Title;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::query()
            ->with(['user.role', 'title', 'parent'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->input('q'));

                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('content', 'like', '%' . $search . '%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'))
                        ->orWhereHas('title', fn ($titleQuery) => $titleQuery->where('name', 'like', '%' . $search . '%'));
                });
            })
            ->when($request->filled('approval'), function ($query) use ($request) {
                if ($request->input('approval') === 'approved') {
                    $query->where('is_approved', true);
                }

                if ($request->input('approval') === 'pending') {
                    $query->where('is_approved', false);
                }
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    public function edit(Comment $comment)
    {
        $comment->load(['user', 'title', 'parent', 'replies']);
        $users = User::query()->with('role')->orderBy('name')->limit(200)->get();
        $titles = Title::query()->orderBy('name')->limit(200)->get();
        $parentOptions = Comment::query()
            ->where('title_id', $comment->title_id)
            ->where('id', '!=', $comment->id)
            ->latest()
            ->limit(100)
            ->get();

        return view('admin.comments.edit', compact('comment', 'users', 'titles', 'parentOptions'));
    }

    public function update(Request $request, Comment $comment)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title_id' => ['required', 'exists:titles,id'],
            'parent_id' => ['nullable', 'exists:comments,id'],
            'content' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        if ((int) ($data['parent_id'] ?? 0) === $comment->id) {
            return back()->withErrors([
                'parent_id' => 'A comment cannot be its own parent.',
            ])->withInput();
        }

        if (filled($data['parent_id'] ?? null)) {
            $parent = Comment::query()->find($data['parent_id']);

            if (! $parent || (int) $parent->title_id !== (int) $data['title_id']) {
                return back()->withErrors([
                    'parent_id' => 'Parent comment must belong to the same title.',
                ])->withInput();
            }
        }

        $data['is_approved'] = $request->boolean('is_approved');
        $comment->update($data);

        return redirect()
            ->route('admin.comments.edit', $comment)
            ->with('message', 'Comment updated successfully.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()
            ->route('admin.comments.index')
            ->with('message', 'Comment deleted successfully.');
    }

    public function toggleApproval(Comment $comment)
    {
        $comment->update([
            'is_approved' => ! $comment->is_approved,
        ]);

        return back()->with('message', 'Comment approval status updated.');
    }
}
