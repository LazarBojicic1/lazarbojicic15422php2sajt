@extends('admin.layouts.app')

@section('title', 'Favorites - Admin')
@section('page-title', 'Favorites')

@section('content')
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">User lists</p>
                <h2 class="mt-2 text-2xl font-bold tracking-[-0.02em]">Favorites</h2>
            </div>
            <a href="{{ route('admin.favorites.create') }}" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">Add favorite</a>
        </div>

        <div class="mt-6 overflow-x-auto rounded-2xl border border-white/[0.06]">
            <table class="min-w-full divide-y divide-white/[0.06]">
                <thead class="bg-white/[0.03] text-left text-[11px] uppercase tracking-[0.18em] text-white/35">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.05] bg-[#0b0f16]">
                    @forelse($favorites ?? [] as $favorite)
                        <tr>
                            <td class="px-4 py-4 text-[13px] text-white/70">{{ $favorite->user?->name }}</td>
                            <td class="px-4 py-4 text-[13px] text-white/70">{{ $favorite->title?->name }}</td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.favorites.edit', $favorite) }}" class="rounded-lg border border-white/[0.08] bg-white/[0.03] px-3 py-2 text-[12px] text-white/75">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-12 text-center text-[13px] text-white/35">No favorites found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
