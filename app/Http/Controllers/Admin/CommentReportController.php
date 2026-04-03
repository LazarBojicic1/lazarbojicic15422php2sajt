<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CommentReportController extends Controller
{
    private const STATUSES = ['pending', 'kept', 'hidden', 'deleted'];

    public function index(Request $request)
    {
        $reports = CommentReport::query()
            ->with(['comment.user', 'comment.title', 'reportedBy', 'reviewedBy'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->input('q'));

                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('reason', 'like', '%' . $search . '%')
                        ->orWhere('comment_snapshot', 'like', '%' . $search . '%')
                        ->orWhere('comment_author_snapshot', 'like', '%' . $search . '%')
                        ->orWhere('title_snapshot', 'like', '%' . $search . '%')
                        ->orWhereHas('reportedBy', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'))
                        ->orWhereHas('comment', fn ($commentQuery) => $commentQuery->where('content', 'like', '%' . $search . '%'));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statuses = self::STATUSES;

        return view('admin.reports.index', compact('reports', 'statuses'));
    }

    public function edit(CommentReport $report)
    {
        $report->load(['comment.user', 'comment.title', 'comment.parent.user', 'comment.replies.user', 'reportedBy', 'reviewedBy']);
        $statuses = self::STATUSES;

        return view('admin.reports.edit', compact('report', 'statuses'));
    }

    public function update(Request $request, CommentReport $report)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'review_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $comment = $report->comment;

        if (in_array($data['status'], ['hidden', 'deleted'], true) && ! $comment) {
            return back()
                ->withErrors([
                    'status' => 'This comment no longer exists, so it cannot be hidden or deleted again.',
                ])
                ->withInput();
        }

        DB::transaction(function () use ($request, $report, $data, $comment) {
            if ($data['status'] === 'pending') {
                $report->update([
                    'status' => 'pending',
                    'review_note' => $data['review_note'] ?? null,
                    'reviewed_by_user_id' => null,
                ]);

                return;
            }

            $reviewedByUserId = $request->user()->id;
            $commentId = $report->comment_id;

            $report->update([
                'status' => $data['status'],
                'review_note' => $data['review_note'] ?? null,
                'reviewed_by_user_id' => $reviewedByUserId,
            ]);

            if ($commentId) {
                CommentReport::query()
                    ->where('comment_id', $commentId)
                    ->where('id', '!=', $report->id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => $data['status'],
                        'review_note' => $data['review_note']
                            ? 'Synced with report #' . $report->id . ': ' . $data['review_note']
                            : 'Synced with report #' . $report->id . '.',
                        'reviewed_by_user_id' => $reviewedByUserId,
                        'updated_at' => now(),
                    ]);
            }

            if (! $comment) {
                return;
            }

            if ($data['status'] === 'kept') {
                $comment->update(['is_approved' => true]);
            }

            if ($data['status'] === 'hidden') {
                $comment->update(['is_approved' => false]);
            }

            if ($data['status'] === 'deleted') {
                $comment->delete();
            }
        });

        return redirect()
            ->route('admin.reports.edit', $report)
            ->with('message', 'Moderation decision saved successfully.');
    }

    public function destroy(CommentReport $report)
    {
        $report->delete();

        return redirect()
            ->route('admin.reports.index')
            ->with('message', 'Report deleted successfully.');
    }
}
