@props([
    'title',
    'description' => null,
    'icon',
    'color' => [
        'bg' => 'bg-emerald-500',
        'text' => 'text-emerald-500',
        'border' => 'border-emerald-200',
        'shadow' => 'shadow-emerald-500/20',
        'gradient' => 'bg-linear-to-l from-white to-emerald-50/20',
    ],
])


<div
    class="group relative overflow-hidden md:p-8 p-5 rounded-2xl border {{ $color['border'] }} bg-white hover:shadow-lg {{ $color['shadow'] }} transition-all duration-500 hover:-translate-y-1">

    <div
        class="absolute inset-0 bg-linear-to-l {{ $color['gradient'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-800 ease-in-out ">
    </div>

    <div class="relative z-10">
        <!-- ICON -->

        <div
            class="md:size-12 size-10 flex items-center justify-center rounded-xl md:mb-5 mb-3
    {{ $color['bg'] }} text-white shadow-md z-20">

            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1.8" class="w-6 h-6">

                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />

            </svg>

        </div>

        <!-- TITLE -->

        <h3 class="text-xl font-bold text-gray-900 md:mb-3 mb-2 z-10">
            {{ $title }}
        </h3>

        <!-- DESCRIPTION -->

        @if ($description)
            <p class="text-gray-600 leading-relaxed">
                {{ $description }}
            </p>
        @endif

    </div>
</div>
