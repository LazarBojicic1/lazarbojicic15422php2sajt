@extends('admin.layouts.app')

@section('title', 'Create Role - Admin')
@section('page-title', 'Create Role')

@section('content')
    <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
        @csrf
        @include('admin.roles.form')
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.roles.index') }}" class="rounded-xl border border-white/[0.08] bg-white/[0.03] px-4 py-3 text-[13px] text-white/70">Cancel</a>
            <button type="submit" class="rounded-xl bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">Save role</button>
        </div>
    </form>
@endsection
