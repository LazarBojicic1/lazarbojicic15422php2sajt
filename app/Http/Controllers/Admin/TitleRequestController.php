<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TitleRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TitleRequestController extends Controller
{
    private const STATUSES = ['pending', 'reviewed', 'approved', 'rejected'];

    public function index(Request $request)
    {
        $requests = TitleRequest::query()
            ->with(['user', 'reviewedBy'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('type'), fn ($query) => $query->where('requested_type', $request->input('type')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->input('q'));

                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('requested_title', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('message', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statuses = self::STATUSES;

        return view('admin.title-requests.index', compact('requests', 'statuses'));
    }

    public function edit(TitleRequest $titleRequest)
    {
        $titleRequest->load(['user', 'reviewedBy']);
        $statuses = self::STATUSES;

        return view('admin.title-requests.edit', [
            'request' => $titleRequest,
            'statuses' => $statuses,
        ]);
    }

    public function update(Request $request, TitleRequest $titleRequest)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $data['reviewed_by_user_id'] = $data['status'] === 'pending'
            ? null
            : $request->user()->id;

        $titleRequest->update($data);

        return redirect()
            ->route('admin.title-requests.edit', $titleRequest)
            ->with('message', 'Title request updated successfully.');
    }

    public function destroy(TitleRequest $titleRequest)
    {
        $titleRequest->delete();

        return redirect()
            ->route('admin.title-requests.index')
            ->with('message', 'Title request deleted successfully.');
    }
}
