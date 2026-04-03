@extends('admin.layouts.app')

@section('title', 'Edit Episode - Admin')
@section('page-title', 'Edit Episode')

@section('content')
    <form method="POST" action="{{ route('admin.episodes.update', $episode) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.episodes.form')
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.episodes.index') }}" class="rounded-xl border border-white/[0.08] bg-white/[0.03] px-4 py-3 text-[13px] text-white/70">Cancel</a>
            <button type="submit" class="rounded-xl bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">Update episode</button>
        </div>
    </form>
    <form method="POST" action="{{ route('admin.episodes.destroy', $episode) }}" class="mt-4" onsubmit="return confirm('Delete this episode?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-[13px] font-semibold text-red-200 transition hover:bg-red-500/20">Delete episode</button>
    </form>
@endsection
