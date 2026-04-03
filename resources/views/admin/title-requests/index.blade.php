@extends('admin.layouts.app')

@section('title', 'Title Requests - Admin')
@section('page-title', 'Title Requests')

@section('content')
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Requests</p>
                <h2 class="mt-2 text-2xl font-bold tracking-[-0.02em]">Title requests</h2>
            </div>
            <p class="text-[13px] text-white/35">Requests are linked to registered member accounts.</p>
        </div>

        <div class="mt-6 overflow-x-auto rounded-2xl border border-white/[0.06]">
            <table class="min-w-full divide-y divide-white/[0.06]">
                <thead class="bg-white/[0.03] text-left text-[11px] uppercase tracking-[0.18em] text-white/35">
                    <tr>
                        <th class="px-4 py-3">Requested title</th>
                        <th class="px-4 py-3">Requester</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.05] bg-[#0b0f16]">
                    @forelse($requests ?? [] as $request)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-white">{{ $request->requested_title }}</p>
                                <p class="mt-1 text-[12px] text-white/35">{{ $request->requested_type ?? 'Any type' }}</p>
                            </td>
                            <td class="px-4 py-4 text-[13px] text-white/70">{{ $request->name }}<br><span class="text-white/35">{{ $request->email }}</span></td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-[12px] font-semibold {{ $request->status === 'pending' ? 'bg-amber-500/15 text-amber-200' : 'bg-emerald-500/15 text-emerald-200' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.title-requests.edit', $request) }}" class="rounded-lg border border-white/[0.08] bg-white/[0.03] px-3 py-2 text-[12px] text-white/75">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-12 text-center text-[13px] text-white/35">No title requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
