@extends('admin.layouts.app')

@section('title', 'Titles - Admin')
@section('page-title', 'Titles')

@section('content')
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Catalog</p>
                <h2 class="mt-2 text-2xl font-bold tracking-[-0.02em]">Titles</h2>
            </div>
            <a href="{{ route('admin.titles.create') }}" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">Create title</a>
        </div>

        <div class="mt-6 overflow-x-auto rounded-2xl border border-white/[0.06]">
            <table class="min-w-full divide-y divide-white/[0.06]">
                <thead class="bg-white/[0.03] text-left text-[11px] uppercase tracking-[0.18em] text-white/35">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Published</th>
                        <th class="px-4 py-3">Rating</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.05] bg-[#0b0f16]">
                    @forelse($titles ?? [] as $title)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-white">{{ $title->name }}</p>
                                <p class="mt-1 text-[12px] text-white/35">{{ $title->slug }}</p>
                            </td>
                            <td class="px-4 py-4 text-[13px] text-white/70 capitalize">{{ $title->tmdb_type }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-[12px] font-semibold {{ $title->is_published ? 'bg-emerald-500/15 text-emerald-200' : 'bg-white/10 text-white/50' }}">
                                    {{ $title->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-[13px] text-white/70">{{ $title->vote_average ? number_format($title->vote_average, 1) : 'N/A' }}</td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.titles.edit', $title) }}" class="rounded-lg border border-white/[0.08] bg-white/[0.03] px-3 py-2 text-[12px] text-white/75">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-[13px] text-white/35">No titles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $titles->links('partials.pagination') ?? '' }}</div>
    </div>
@endsection
