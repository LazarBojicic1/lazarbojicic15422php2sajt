@extends('admin.layouts.app')

@section('title', 'Users - Admin')
@section('page-title', 'Users')

@section('content')
    <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">User management</p>
                <h2 class="mt-2 text-2xl font-bold tracking-[-0.02em]">Accounts</h2>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">Create user</a>
        </div>

        <div class="mt-6 overflow-x-auto rounded-2xl border border-white/[0.06]">
            <table class="min-w-full divide-y divide-white/[0.06]">
                <thead class="bg-white/[0.03] text-left text-[11px] uppercase tracking-[0.18em] text-white/35">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Verified</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.05] bg-[#0b0f16]">
                    @forelse($users ?? [] as $user)
                        <tr class="align-top">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-white">{{ $user->name }}</p>
                                <p class="mt-1 text-[12px] text-white/35">{{ $user->email }}</p>
                            </td>
                            <td class="px-4 py-4 text-[13px] text-white/70">{{ $user->role?->name ?? 'user' }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-[12px] font-semibold {{ $user->is_active ? 'bg-emerald-500/15 text-emerald-200' : 'bg-red-500/15 text-red-200' }}">
                                    {{ $user->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-[13px] text-white/70">{{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'Unverified' }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg border border-white/[0.08] bg-white/[0.03] px-3 py-2 text-[12px] text-white/75">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-[13px] text-white/35">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links('partials.pagination') ?? '' }}
        </div>
    </div>
@endsection
