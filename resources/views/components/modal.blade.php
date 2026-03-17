<div>
    @props([
        'show' => false,
        'maxWidth' => 'max-w4xl',
        'title' => null,
    ])

    <div x-data="{ show: @entangle($show).live }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true" x-transition.opacity.duration.200ms>

        <!-- Overlay con blur -->
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-lg transition-opacity" x-on:click="show = false"
            {{ $attributes->only('wire:click') }} x-transition.opacity>
        </div>

        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-lg bg-gray-800 shadow-2xl w-full {{ $maxWidth }} border border-gray-700"
                @click.stop x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                <!-- Botón Cerrar (X) -->
                <button x-on:click="show = false" {{ $attributes->only('wire:click') }}
                    class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Contenido del Modal -->
                <div class="md:p-6 p-4">
                    @if ($title)
                        <h2 class="text-2xl font-semibold text-white mb-6">
                            {{ $title }}
                        </h2>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
