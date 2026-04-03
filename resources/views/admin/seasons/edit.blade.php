@extends('admin.layouts.app')

@section('title', 'Edit Season - Admin')
@section('page-title', 'Edit Season')

@section('content')
    <form method="POST" action="{{ route('admin.seasons.update', $season) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.seasons.form')
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.seasons.index') }}" class="rounded-xl border border-white/[0.08] bg-white/[0.03] px-4 py-3 text-[13px] text-white/70">Cancel</a>
            <button type="submit" class="rounded-xl bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">Update season</button>
        </div>
    </form>
    <form method="POST" action="{{ route('admin.seasons.destroy', $season) }}" class="mt-4" onsubmit="return confirm('Delete this season and all related episodes?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-[13px] font-semibold text-red-200 transition hover:bg-red-500/20">Delete season</button>
    </form>
@endsection
