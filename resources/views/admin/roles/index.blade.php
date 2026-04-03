@extends('admin.layouts.app')

@section('title', 'Roles - Admin')
@section('page-title', 'Roles')

@section('content')
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Authorization</p>
                <h2 class="mt-2 text-2xl font-bold tracking-[-0.02em]">Roles</h2>
            </div>
            <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">Create role</a>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($roles ?? [] as $role)
                <div class="rounded-3xl border border-white/[0.06] bg-[#0b0f16] p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-bold capitalize">{{ $role->name }}</h3>
                            <p class="mt-2 text-[13px] text-white/35">{{ $role->description ?? 'No description provided.' }}</p>
                        </div>
                        <span class="rounded-full bg-white/[0.06] px-3 py-1 text-[12px] text-white/60">{{ $role->users_count ?? $role->users?->count() ?? 0 }} users</span>
                    </div>
                    <div class="mt-5 flex items-center gap-2">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="rounded-xl border border-white/[0.08] bg-white/[0.03] px-3 py-2 text-[12px] text-white/75">Edit</a>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-8 text-center text-[13px] text-white/35">No roles found.</div>
            @endforelse
        </div>
    </div>
@endsection
