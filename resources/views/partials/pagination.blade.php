@if ($paginator->hasPages())
<nav class="flex items-center gap-1">
    @if ($paginator->onFirstPage())
        <span class="w-9 h-9 flex items-center justify-center rounded text-white/15 cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded text-white/50 hover:text-white hover:bg-white/[0.08] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="w-9 h-9 flex items-center justify-center rounded text-[13px] text-white/20">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="w-9 h-9 flex items-center justify-center rounded text-[13px] font-bold bg-red-600 text-white">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded text-[13px] text-white/50 hover:text-white hover:bg-white/[0.08] transition">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded text-white/50 hover:text-white hover:bg-white/[0.08] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    @else
        <span class="w-9 h-9 flex items-center justify-center rounded text-white/15 cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </span>
    @endif
</nav>
@endif
