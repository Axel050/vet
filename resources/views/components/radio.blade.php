@props(['label', 'model', 'live' => false, 'options' => []])

<div>
    <label class="block text-sm font-medium text-gray-300 mb-2">
        {{ $label }}
    </label>

    @foreach ($options as $option)
        <label class="inline-flex items-center gap-2 text-gray-200 mr-6">
            <input type="radio" wire:model.{{ $live ? 'live' : 'defer' }} ="{{ $model }}"
                value="{{ $option['value'] }}">
            {{ $option['label'] }}
        </label>
    @endforeach

    @error($model)
        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
