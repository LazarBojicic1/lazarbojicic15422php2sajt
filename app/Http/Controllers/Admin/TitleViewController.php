<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TitleView;
use Illuminate\Http\Request;

class TitleViewController extends Controller
{
    public function index(Request $request)
    {
        $views = TitleView::query()
            ->with(['user.role', 'title'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->input('q'));

                $query->where(function ($builder) use ($search) {
                    $builder
                        ->whereHas('title', fn ($titleQuery) => $titleQuery->where('name', 'like', '%' . $search . '%'))
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'))
                        ->orWhere('ip_hash', 'like', '%' . $search . '%');
                });
            })
            ->latest('viewed_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.title-views.index', compact('views'));
    }

    public function show(TitleView $titleView)
    {
        $titleView->load(['user.role', 'title']);

        return view('admin.title-views.show', [
            'view' => $titleView,
        ]);
    }

    public function destroy(TitleView $titleView)
    {
        $titleView->delete();

        return redirect()
            ->route('admin.title-views.index')
            ->with('message', 'Title view deleted successfully.');
    }
}
