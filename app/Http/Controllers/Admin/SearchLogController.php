<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchLog;
use Illuminate\Http\Request;

class SearchLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = SearchLog::query()
            ->with(['user.role', 'selectedTitle'])
            ->when($request->filled('q'), fn ($query) => $query->where('query', 'like', '%' . trim((string) $request->input('q')) . '%'))
            ->when($request->boolean('zero_results'), fn ($query) => $query->where('results_count', 0))
            ->latest('searched_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.search-logs.index', compact('logs'));
    }

    public function show(SearchLog $searchLog)
    {
        $searchLog->load(['user.role', 'selectedTitle']);

        return view('admin.search-logs.show', [
            'log' => $searchLog,
        ]);
    }

    public function destroy(SearchLog $searchLog)
    {
        $searchLog->delete();

        return redirect()
            ->route('admin.search-logs.index')
            ->with('message', 'Search log deleted successfully.');
    }
}
