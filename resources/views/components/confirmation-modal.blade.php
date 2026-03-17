@props(['show' => null, 'maxWidth' => 'max-w-2xl', 'action' => null])

@php
    $show = $show ?? $attributes->wire('model')->value();
@endphp

<x-modal :show="$show" :maxWidth="$maxWidth" {{ $attributes }}>

    <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-700">
        <div class="sm:flex sm:items-start">
            <div
                class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-900/50 sm:mx-0 sm:h-10 sm:w-10 ring-1 ring-red-500/50">
                <svg class="h-6 w-6 text-red-500" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-white" id="modal-title">
                    {{ $title }}
                </h3>
                <div class="mt-2 text-wrap">
                    <p class="text-sm text-gray-300">
                        {{ $content }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-row justify-center px-6 py-4 bg-gray-900/50 text-right md:gap-6 gap-4">
        @if (isset($footer) && $footer->isNotEmpty())
            {{ $footer }}
        @else
            <button x-on:click="show = false"
                class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 mr-2 transition-colors">
                Cancelar
            </button>

            @if ($action)
                <button wire:click="{{ $action }}"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Eliminar
                </button>
            @endif
        @endif
    </div>
</x-modal>
