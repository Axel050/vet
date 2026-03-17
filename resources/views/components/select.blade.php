@props(['label', 'model', 'live' => false])

<div>
    <label class="block text-sm font-medium text-gray-300 mb-1 md:-2">
        {{ $label }}
    </label>

    <select wire:model.{{ $live ? 'live' : 'defer' }} ="{{ $model }}"
        {{ $attributes->merge([
            'class' =>
                'w-full px-2 md:px-4 py-1 md:py-1.5 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 text-white',
        ]) }}>
        {{ $slot }}
    </select>

    @error($model)
        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
