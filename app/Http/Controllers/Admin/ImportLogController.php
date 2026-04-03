<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportLog;
use Illuminate\Http\Request;

class ImportLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ImportLog::query()
            ->with('admin.role')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('action'), fn ($query) => $query->where('action', $request->input('action')))
            ->when($request->filled('type'), fn ($query) => $query->where('tmdb_type', $request->input('type')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.import-logs.index', compact('logs'));
    }

    public function show(ImportLog $importLog)
    {
        $importLog->load('admin.role');

        return view('admin.import-logs.show', [
            'log' => $importLog,
        ]);
    }

    public function destroy(ImportLog $importLog)
    {
        $importLog->delete();

        return redirect()
            ->route('admin.import-logs.index')
            ->with('message', 'Import log deleted successfully.');
    }
}
