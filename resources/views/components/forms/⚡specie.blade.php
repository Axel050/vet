<?php

use Livewire\Component;
use App\Models\Species;
use Illuminate\Validation\Rule;

new class extends Component {
    public ?Species $especie = null;

    public string $name = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255', Rule::unique('species', 'name')->ignore($this->especie?->id)],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre de la especie es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.unique' => 'La especie ya existe.',
        ];
    }

    public function mount(?Species $especie = null): void
    {
        if ($especie?->exists) {
            $this->especie = $especie;
            $this->name = $especie->name ?? '';
        }
    }

    public function cancel(): void
    {
        $this->dispatch('form-cancelled');
    }

    public function save(): void
    {
        $this->validate();

        if ($this->especie) {
            $this->especie->update([
                'name' => $this->name,
            ]);
        } else {
            $this->especie = Species::create([
                'name' => $this->name,
            ]);
        }

        if ($this->especie->wasRecentlyCreated) {
            $this->dispatch('especie-created');
            $this->dispatch('notify', message: 'Especie creada correctamente', type: 'success');
            $this->reset();
        } else {
            $this->dispatch('especie-updated');
            $this->dispatch('notify', message: 'Especie actualizada correctamente', type: 'success');
        }
    }
};
?>

<div>
    <h2 class="md:text-2xl text-xl font-semibold text-white md:mb-6 mb-4">
        {{ $especie ? 'Editar Especie' : 'Crear Especie' }}
    </h2>

    <form wire:submit="save" class="md:space-y-6 space-y-3" autocomplete="off">

        <div class="bg-gray-800/50 p-4 rounded-lg border border-gray-700">
            <div class="grid grid-cols-1 gap-4">
                <x-input label="Nombre de la especie" model="name" />
            </div>
        </div>

        <div class="flex gap-4 mt-8 pt-6 border-t border-gray-700">
            <button type="button" wire:click="cancel"
                class="flex-1 md:px-6 px-3 md:py-3 py-1.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors font-medium text-base">
                Cancelar
            </button>
            <button type="submit"
                class="flex-1 md:px-6 px-3 md:py-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-base shadow-lg">
                {{ $especie ? 'Actualizar' : 'Crear' }}
            </button>
        </div>
    </form>
</div>
