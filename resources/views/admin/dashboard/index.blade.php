@extends('admin.layouts.app')

@section('title', 'Dashboard - Admin')
@section('page-title', 'Dashboard')

@section('content')
    <div class="grid gap-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach([
                ['label' => 'Total Users', 'value' => $stats['users'] ?? null, 'tone' => 'from-red-500/20 to-red-500/5', 'accent' => 'text-red-200'],
                ['label' => 'Published Titles', 'value' => $stats['published_titles'] ?? null, 'tone' => 'from-white/10 to-white/5', 'accent' => 'text-white'],
                ['label' => 'Pending Reports', 'value' => $stats['pending_reports'] ?? null, 'tone' => 'from-amber-500/20 to-amber-500/5', 'accent' => 'text-amber-200'],
                ['label' => 'Searches Today', 'value' => $stats['searches_today'] ?? null, 'tone' => 'from-emerald-500/20 to-emerald-500/5', 'accent' => 'text-emerald-200'],
            ] as $card)
                <div class="rounded-3xl border border-white/[0.06] bg-gradient-to-br {{ $card['tone'] }} p-5 shadow-[0_20px_80px_rgba(0,0,0,0.18)]">
                    <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">{{ $card['label'] }}</p>
                    <p class="mt-4 text-3xl font-extrabold tracking-[-0.03em] {{ $card['accent'] }}">
                        {{ $card['value'] ?? 0 }}
                    </p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.65fr_1fr]">
            <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Overview</p>
                        <h2 class="mt-2 text-2xl font-bold tracking-[-0.02em]">Platform health</h2>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 rounded-full border border-white/[0.06] bg-white/[0.03] px-3 py-2 text-[12px] text-white/45">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        Live data
                    </div>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @foreach([
                        ['label' => 'Pending moderation', 'value' => $stats['pending_comments'] ?? 0, 'note' => 'Comments awaiting review'],
                        ['label' => 'Pending title requests', 'value' => $stats['pending_requests'] ?? 0, 'note' => 'User-submitted catalog requests'],
                        ['label' => 'Favorites saved', 'value' => $stats['favorites'] ?? 0, 'note' => 'User collections across the app'],
                        ['label' => 'Views tracked', 'value' => $stats['views'] ?? 0, 'note' => 'Watch events stored in the database'],
                    ] as $item)
                        <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] p-4">
                            <p class="text-[12px] text-white/30">{{ $item['label'] }}</p>
                            <p class="mt-3 text-2xl font-bold tracking-[-0.03em]">{{ $item['value'] }}</p>
                            <p class="mt-2 text-[13px] text-white/35">{{ $item['note'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[28px] border border-white/[0.06] bg-white/[0.03] p-5">
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-white/30">Roles</p>
                <div class="mt-4 space-y-3">
                    @foreach($roleBreakdown ?? [] as $role)
                        <div class="rounded-2xl border border-white/[0.06] bg-[#0b0f16] px-4 py-3">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-[14px] font-semibold">{{ $role['name'] ?? '' }}</p>
                                    <p class="text-[12px] text-white/30">{{ $role['description'] ?? '' }}</p>
                                </div>
                                <span class="rounded-full bg-white/[0.06] px-3 py-1 text-[12px] font-semibold text-white/70">{{ $role['count'] ?? 0 }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
