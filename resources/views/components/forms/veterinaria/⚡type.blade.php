<?php

use App\Models\VeterinaryType;
use App\Livewire\Forms\TypeForm;
use Livewire\Component;

new class extends Component {
    public ?VeterinaryType $type = null;

    public TypeForm $form;

    public bool $is_active = true;

    public bool $show_in_landing = false;

    public function mount(?VeterinaryType $type = null): void
    {
        if ($type?->exists) {
            $this->type = $type;
            $this->form->setType($type);
        }
    }

    public function cancel(): void
    {
        $this->dispatch('form-cancelled');
    }

    public function save(): void
    {
        $this->form->validate();

        if ($this->type) {
            $this->form->update();
        } else {
            $this->type = $this->form->store();
        }

        if ($this->type->wasRecentlyCreated) {
            $this->dispatch('notify', message: 'Tipo creado correctamente', type: 'success');
            $this->dispatch('type-created');
            $this->reset();
        } else {
            $this->dispatch('notify', message: 'Tipo actualizado correctamente', type: 'success');
            $this->dispatch('type-updated');
        }
    }
};
?>

<div>
    <h2 class="text-2xl font-semibold text-white mb-3">
        {{ $type ? 'Editar Tipo' : 'Crear Tipo' }}
    </h2>

    <form wire:submit="save" class="space-y-6" autocomplete="off">

        <div class="bg-gray-800/50 p-4 rounded-lg border border-gray-700">

            <div class="grid grid-cols-1 ">

                <div class="space-y-3">
                    <x-input label="Nombre " model="form.name" />

                    <x-input label="Descripción" model="form.description" />

                    <x-radio label="Activo" model="form.is_active" :options="[['label' => 'Sí', 'value' => 1], ['label' => 'No', 'value' => 0]]" live="true" />


                    @can('pro-veterinaria')
                        <div class="pt-4 border-t border-gray-700">
                            <x-radio label="Mostrar en mi Landing Page" model="form.show_in_landing" :options="[['label' => 'Sí', 'value' => 1], ['label' => 'No', 'value' => 0]]"
                                live="true" />
                            <p class="text-xs text-gray-400 mt-1 italic">Este tipo de consulta aparecerá destacado en tu
                                página
                                pública personalizada.</p>
                        </div>

                        <div class="pt-3 border-t border-gray-700">
                            <label class="block text-sm font-medium text-gray-400 mb-3">Ícono del Tipo de Consulta</label>
                            <div class="grid md:grid-cols-5 grid-cols-3 gap-2">
                                @php
                                    $availableIcons = config('service-icons', []);
                                    $defaultPath =
                                        'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z';
                                @endphp

                                <button type="button" wire:click="$set('form.icon', '')"
                                    class="p-2.5 rounded-xl border-2 transition-all flex flex-col items-center gap-1 {{ $form->icon === '' ? 'border-indigo-500 bg-indigo-500/10' : 'border-gray-700 hover:border-gray-600 bg-gray-900/50' }}">
                                    <svg class="w-6 h-6 {{ $form->icon === '' ? 'text-indigo-400' : 'text-gray-400' }}"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $defaultPath }}" />
                                    </svg>
                                    <span class="text-[10px] uppercase font-bold text-gray-500">Default</span>
                                </button>

                                @foreach ($availableIcons as $key => $path)
                                    <button type="button" wire:click="$set('form.icon', '{{ $key }}')"
                                        class="p-3 rounded-xl border-2 transition-all flex flex-col items-center gap-1 {{ $form->icon === $key ? 'border-indigo-500 bg-indigo-500/10' : 'border-gray-700 hover:border-gray-600 bg-gray-900/50' }}">
                                        <svg class="w-6 h-6 {{ $form->icon === $key ? 'text-indigo-400' : 'text-gray-400' }}"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $path }}" />
                                        </svg>
                                        <span
                                            class="text-[10px] uppercase font-bold text-gray-500">{{ str_replace('_', ' ', $key) }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endcan

                </div>

                <div class="flex gap-4 mt-5 pt-4 border-t border-gray-700">
                    <button type="button" wire:click="cancel"
                        class="flex-1 md:px-6 px-3 md:py-3 py-1.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors font-medium text-base">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 md:px-6 px-3 md:py-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-base shadow-lg">
                        {{ $type ? 'Actualizar' : 'Crear' }}
                    </button>
                </div>
    </form>
</div>
