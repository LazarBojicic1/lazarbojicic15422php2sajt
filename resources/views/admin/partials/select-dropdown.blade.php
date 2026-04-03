@php
    $current = (string) ($current ?? '');
    $placeholder = $placeholder ?? 'Select an option';
    $submit = $submit ?? false;
    $currentText = $placeholder;

    foreach ($options as $option) {
        if ((string) ($option['value'] ?? '') === $current) {
            $currentText = $option['text'] ?? $placeholder;
            break;
        }
    }
@endphp

<div class="relative min-w-0" data-dropdown data-dropdown-submit="{{ $submit ? 'true' : 'false' }}">
    <input type="hidden" name="{{ $name }}" value="{{ $current }}">

    <button
        type="button"
        data-dropdown-toggle
        class="flex w-full items-center justify-between gap-3 rounded-xl border border-white/[0.08] bg-[#0b0f16] px-4 py-3 text-left text-[14px] text-white transition hover:border-white/[0.14] focus:border-red-500/40 focus:outline-none"
    >
        <span data-dropdown-label class="min-w-0 truncate {{ $current === '' ? 'text-white/40' : 'text-white' }}">
            {{ $currentText }}
        </span>
        <svg class="h-4 w-4 shrink-0 text-white/40 transition-transform" data-dropdown-chevron fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        data-dropdown-menu
        class="hidden absolute left-0 right-0 top-full z-50 mt-2 max-h-64 overflow-y-auto rounded-2xl border border-white/[0.08] bg-[#11151d] p-2 shadow-2xl custom-scrollbar"
    >
        @foreach($options as $option)
            @php
                $isActive = (string) ($option['value'] ?? '') === $current;
            @endphp
            <button
                type="button"
                data-dropdown-option
                data-value="{{ $option['value'] }}"
                class="w-full rounded-xl px-3 py-2.5 text-left text-[13px] transition {{ $isActive ? 'bg-red-500/15 font-semibold text-red-400' : 'text-white/60 hover:bg-white/[0.06] hover:text-white/90' }}"
            >
                {{ $option['text'] }}
            </button>
        @endforeach
    </div>
</div>
