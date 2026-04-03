@extends('admin.layouts.app')

@section('title', 'Review Request - Admin')
@section('page-title', 'Review Request')

@section('content')
    @php
        $statusOptions = collect([
            'pending' => 'Pending',
            'reviewed' => 'Reviewed',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ])->map(fn ($label, $value) => ['value' => $value, 'text' => $label])->values()->all();
    @endphp

    <form method="POST" action="{{ route('admin.title-requests.update', $request) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid gap-6 lg:grid-cols-[1.4fr_0.6fr]">
            <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 space-y-4">
                <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4">
                    <p class="text-[12px] uppercase tracking-[0.18em] text-white/30">Requested title</p>
                    <p class="mt-3 text-[18px] font-semibold text-white">{{ $request->requested_title }}</p>
                    <p class="mt-2 text-[13px] text-white/35">{{ $request->requested_type ? ucfirst($request->requested_type) : 'Any type' }}</p>
                    <a
                        href="{{ route('admin.titles.create', ['q' => $request->requested_title, 'type' => $request->requested_type ?: 'multi']) }}"
                        class="mt-4 inline-flex items-center rounded-xl bg-red-600 px-4 py-2.5 text-[13px] font-semibold text-white transition hover:bg-red-500"
                    >
                        Search TMDb For This Request
                    </a>
                </div>
                <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4">
                    <p class="text-[12px] uppercase tracking-[0.18em] text-white/30">Request message</p>
                    <p class="mt-3 text-[14px] leading-7 text-white/85">{{ $request->message ?: 'No extra details were provided.' }}</p>
                </div>
            </div>
            <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5 space-y-4">
                <label class="grid gap-2">
                    <span class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Status</span>
                    @include('admin.partials.select-dropdown', [
                        'name' => 'status',
                        'current' => old('status', $request->status ?? 'pending'),
                        'options' => $statusOptions,
                        'placeholder' => 'Choose a status',
                    ])
                </label>
                <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4 text-[13px] text-white/45">
                    <p class="text-white/80 font-semibold">Requester</p>
                    <p class="mt-2">{{ $request->name }}<br>{{ $request->email }}</p>
                    @if($request->user)
                        <p class="mt-3 text-white/30">Linked account: {{ $request->user->name }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.title-requests.index') }}" class="rounded-xl border border-white/[0.08] bg-white/[0.03] px-4 py-3 text-[13px] text-white/70">Cancel</a>
            <button type="submit" class="rounded-xl bg-red-600 px-5 py-3 text-[13px] font-semibold text-white transition hover:bg-red-500">Save request</button>
        </div>
    </form>
    <form method="POST" action="{{ route('admin.title-requests.destroy', $request) }}" class="mt-4" onsubmit="return confirm('Delete this request?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-[13px] font-semibold text-red-200 transition hover:bg-red-500/20">Delete request</button>
    </form>
@endsection
