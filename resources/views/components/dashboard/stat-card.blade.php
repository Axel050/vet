@props(['title', 'value', 'color' => 'indigo', 'icon' => null, 'leftIcon' => false])

@php
    $colors = [
        'indigo' => [
            'border' => 'border-indigo-500/20',
            'bg_icon' => 'bg-indigo-500/10',
            'text_icon' => 'text-indigo-400',
        ],
        'purple' => [
            'border' => 'border-purple-500/20',
            'bg_icon' => 'bg-purple-500/10',
            'text_icon' => 'text-purple-400',
        ],
        'green' => [
            'border' => 'border-green-500/20',
            'bg_icon' => 'bg-green-500/10',
            'text_icon' => 'text-green-400',
        ],
        'blue' => [
            'border' => 'border-blue-500/20',
            'bg_icon' => 'bg-blue-500/10',
            'text_icon' => 'text-blue-400',
        ],
        'gray' => [
            'border' => 'border-gray-700',
            'bg_icon' => 'bg-gray-500/10',
            'text_icon' => 'text-gray-400',
        ],
    ];

    $selectedColor = $colors[$color] ?? $colors['indigo'];
@endphp

<div class="bg-gray-800/50 md:p-6 py-2 px-3 rounded-xl border {{ $selectedColor['border'] }} shadow-lg backdrop-blur-sm">
    <div class="flex  items-center justify-between {{ $leftIcon ?: 'flex-row-reverse' }} ">
        <div>
            <p class="text-sm text-gray-400 font-medium">{{ $title }}</p>
            <p class="md:text-2xl text-lg font-bold text-white md:mt-1 mt-0">
                {{ $value }}
            </p>
        </div>
        @if ($icon)
            <div class="md:p-3 p-2 {{ $selectedColor['bg_icon'] }} rounded-lg">
                <svg class="h-6 w-6 {{ $selectedColor['text_icon'] }}" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    {!! $icon !!}
                </svg>
            </div>
        @endif
    </div>
</div>
