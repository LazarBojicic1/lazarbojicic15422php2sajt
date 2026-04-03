@extends('admin.layouts.app')

@section('title', 'Import Logs - Admin')
@section('page-title', 'Import Logs')

@section('content')
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Sync operations</p>
                <h2 class="mt-2 text-2xl font-bold tracking-[-0.02em]">Import logs</h2>
            </div>
            <p class="text-[13px] text-white/35">Track TMDb import and IMDb sync activity here.</p>
        </div>

        <div class="mt-6 overflow-x-auto rounded-2xl border border-white/[0.06]">
            <table class="min-w-full divide-y divide-white/[0.06]">
                <thead class="bg-white/[0.03] text-left text-[11px] uppercase tracking-[0.18em] text-white/35">
                    <tr>
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">When</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.05] bg-[#0b0f16]">
                    @forelse($logs ?? [] as $log)
                        <tr>
                            <td class="px-4 py-4 font-semibold text-white">{{ $log->action }}</td>
                            <td class="px-4 py-4 text-[13px] text-white/70">{{ $log->tmdb_type }} #{{ $log->tmdb_id }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-[12px] font-semibold {{ $log->status === 'success' ? 'bg-emerald-500/15 text-emerald-200' : ($log->status === 'warning' ? 'bg-amber-500/15 text-amber-200' : 'bg-red-500/15 text-red-200') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-[13px] text-white/60">{{ $log->created_at?->format('M d, Y h:i A') }}</td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.import-logs.show', $log) }}" class="rounded-lg border border-white/[0.08] bg-white/[0.03] px-3 py-2 text-[12px] text-white/75">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-[13px] text-white/35">No import logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
