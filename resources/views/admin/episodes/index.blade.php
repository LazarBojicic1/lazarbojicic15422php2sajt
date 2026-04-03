@extends('admin.layouts.app')

@section('title', 'Episodes - Admin')
@section('page-title', 'Episodes')

@section('content')
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Episode library</p>
                <h2 class="mt-2 text-2xl font-bold tracking-[-0.02em]">Episodes</h2>
            </div>
            <a href="{{ route('admin.episodes.create') }}" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">Create episode</a>
        </div>

        <div class="mt-6 overflow-x-auto rounded-2xl border border-white/[0.06]">
            <table class="min-w-full divide-y divide-white/[0.06]">
                <thead class="bg-white/[0.03] text-left text-[11px] uppercase tracking-[0.18em] text-white/35">
                    <tr>
                        <th class="px-4 py-3">Episode</th>
                        <th class="px-4 py-3">Season</th>
                        <th class="px-4 py-3">Runtime</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.05] bg-[#0b0f16]">
                    @forelse($episodes ?? [] as $episode)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-white">{{ $episode->name }}</p>
                                <p class="mt-1 text-[12px] text-white/35">Episode {{ $episode->episode_number }}</p>
                            </td>
                            <td class="px-4 py-4 text-[13px] text-white/70">{{ $episode->season?->name }}</td>
                            <td class="px-4 py-4 text-[13px] text-white/60">{{ $episode->runtime ?? 'N/A' }}</td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.episodes.edit', $episode) }}" class="rounded-lg border border-white/[0.08] bg-white/[0.03] px-3 py-2 text-[12px] text-white/75">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-12 text-center text-[13px] text-white/35">No episodes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
