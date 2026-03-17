@props(['on', 'type' => 'success', 'message' => ''])

@php
    $colors = [
        'success' => 'bg-emerald-600 text-white shadow-emerald-500/20',
        'error' => 'bg-red-600 text-white shadow-red-500/20',
        'info' => 'bg-blue-600 text-white shadow-blue-500/20',
        'warning' => 'bg-amber-500 text-white shadow-amber-500/20',
    ];

    $icon = [
        'success' =>
            '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
        'error' =>
            '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
        'info' =>
            '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'warning' =>
            '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
    ];

    $classes = $colors[$type] ?? $colors['success'];
    $currentIcon = $icon[$type] ?? $icon['success'];
@endphp

<div x-data="{ shown: false, timeout: null }" x-init="@this.on('{{ $on }}', () => {
    clearTimeout(timeout);
    shown = true;
    timeout = setTimeout(() => { shown = false }, 3000);
})" x-show.transition.opacity.out.duration.1500ms="shown"
    style="display: none;"
    {{ $attributes->merge(['class' => "fixed top-5 right-5 z-50 rounded-xl shadow-lg pl-3 pr-5 py-3 flex items-center gap-3 backdrop-blur-sm transform transition-all duration-300 translate-y-0 $classes"]) }}>

    <div class="shrink-0">
        {!! $currentIcon !!}
    </div>

    <span class="font-medium text-sm tracking-wide">
        {{ $message ?: $slot }}
    </span>
</div>
