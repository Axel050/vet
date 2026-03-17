@props(['label', 'model', 'placeholder' => '', 'type' => 'text', 'live' => false, 'disabled' => false])

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1 ">
        {{ $label }}
    </label>

    @if ($type === 'date')
        <div class="relative w-full">
            <input type="date" id="{{ $model }}" wire:model{{ $live ? '.live' : '' }}="{{ $model }}"
                placeholder="{{ $placeholder }}" {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->merge([
                    'class' =>
                        'w-full px-2 md:px-4 py-1 md:py-1.5 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 text-white pr-10 no-calendar-icon',
                ]) }} />

            <button type="button" onclick="document.getElementById('{{ $model }}').showPicker()"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-white/70 hover:text-white">
                <svg class="w-4 h-4 pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </button>
        </div>
    @else
        <input type="{{ $type }}" wire:model{{ $live ? '.live' : '' }}="{{ $model }}"
            placeholder="{{ $placeholder }}" {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge([
                'class' =>
                    'w-full px-2 md:px-4 py-1 md:py-1.5 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 text-white',
            ]) }} />
    @endif

    @error($model)
        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
