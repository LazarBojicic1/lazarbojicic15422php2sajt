@extends('admin.layouts.app')

@section('title', 'Edit Genre - Admin')
@section('page-title', 'Edit Genre')

@section('content')
    <form method="POST" action="{{ route('admin.genres.update', $genre) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.genres.form')
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.genres.index') }}" class="rounded-xl border border-white/[0.08] bg-white/[0.03] px-4 py-3 text-[13px] text-white/70">Cancel</a>
            <button type="submit" class="rounded-xl bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">Update genre</button>
        </div>
    </form>
    <form method="POST" action="{{ route('admin.genres.destroy', $genre) }}" class="mt-4" onsubmit="return confirm('Delete this genre?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-[13px] font-semibold text-red-200 transition hover:bg-red-500/20">Delete genre</button>
    </form>
@endsection
