@props(['label', 'model', 'placeholder' => '', 'live' => false, 'disabled' => false])


<div>
    <label class="block text-sm font-medium text-gray-300 mb-1">
        {{ $label }}
    </label>

    <textarea wire:model{{ $live ? '.live' : '' }}="{{ $model }}" placeholder="{{ $placeholder }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' =>
                'w-full px-4 md:py-1.5 py-1 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 text-white ',
        ]) }}></textarea>

    @error($model)
        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
