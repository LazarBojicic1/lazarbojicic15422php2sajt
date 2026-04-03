{{-- Custom filter dropdown (replaces native <select>)
     Props: $name, $label, $options (array of ['value'=>..., 'text'=>...]), $current --}}
@php
    $current = $current ?? '';
    $currentText = $label;
    foreach ($options as $opt) {
        if ((string) $opt['value'] === (string) $current) {
            $currentText = $opt['text'];
            break;
        }
    }
@endphp
<div class="relative" data-dropdown>
    <input type="hidden" name="{{ $name }}" value="{{ $current }}">
    <button
        type="button"
        data-dropdown-toggle
        class="flex items-center gap-2 bg-white/[0.04] border border-white/[0.06] text-white rounded-lg px-3 py-2 sm:py-2.5 text-[13px] font-medium hover:bg-white/[0.07] transition cursor-pointer min-w-0"
    >
        <span data-dropdown-label class="truncate">{{ $currentText }}</span>
        <svg class="w-3.5 h-3.5 text-white/40 shrink-0 transition-transform duration-200" data-dropdown-chevron fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div
        data-dropdown-menu
        class="hidden absolute left-0 top-full mt-1 z-50 rounded-lg shadow-2xl max-h-64 overflow-y-auto custom-scrollbar py-1 min-w-[160px]"
        style="background:#141418;border:1px solid rgba(255,255,255,0.08)"
    >
        @foreach($options as $opt)
            <button
                type="button"
                data-dropdown-option
                data-value="{{ $opt['value'] }}"
                class="w-full text-left px-3 py-2.5 text-[13px] transition cursor-pointer {{ (string) $opt['value'] === (string) $current ? 'bg-red-500/15 text-red-400 font-semibold' : 'text-white/60 hover:bg-white/[0.06] hover:text-white/90' }}"
            >
                {{ $opt['text'] }}
            </button>
        @endforeach
    </div>
</div>
