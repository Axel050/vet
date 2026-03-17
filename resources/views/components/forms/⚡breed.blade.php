<?php

use Livewire\Component;
use App\Models\Species;
use App\Models\Breed;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;

new class extends Component {
    public ?Breed $raza = null;

    public string $name = '';
    public string $species_id = '';

    protected function rules(): array
    {
        return [
            'species_id' => 'required|exists:species,id',
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('breeds', 'name')
                    ->where(function ($query) {
                        return $query->where('species_id', $this->species_id);
                    })
                    ->ignore($this->raza?->id),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre de la raza es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.unique' => 'La raza ya existe para esta especie.',
            'species_id.required' => 'La especie es obligatoria.',
        ];
    }

    #[Computed]
    public function species()
    {
        return Species::orderBy('name', 'asc')->get();
    }

    public function mount(?Breed $raza = null): void
    {
        if ($raza?->exists) {
            $this->raza = $raza;
            $this->name = $raza->name ?? '';
            $this->species_id = $raza->species_id ?? '';
        }
    }

    public function cancel(): void
    {
        $this->dispatch('form-cancelled');
    }

    public function save(): void
    {
        $this->validate();

        if ($this->raza) {
            $this->raza->update([
                'name' => $this->name,
                'species_id' => $this->species_id,
            ]);
        } else {
            $this->raza = Breed::create([
                'name' => $this->name,
                'species_id' => $this->species_id,
            ]);
        }

        if ($this->raza->wasRecentlyCreated) {
            $this->dispatch('raza-created');
            $this->dispatch('notify', message: 'Raza creada correctamente', type: 'success');
            $this->reset();
        } else {
            $this->dispatch('raza-updated');
            $this->dispatch('notify', message: 'Raza actualizada correctamente', type: 'success');
        }
    }
};
?>

<div>
    <h2 class="md:text-2xl text-xl font-semibold text-white md:mb-6 mb-4">
        {{ $raza ? 'Editar Raza' : 'Crear Raza' }}
    </h2>

    <form wire:submit="save" class="space-y-6" autocomplete="off">

        <div class="bg-gray-800/50 p-4 rounded-lg border border-gray-700">
            <div class="grid grid-cols-1 gap-4">
                <x-select label="Especie" model="species_id">
                    <option value="">Seleccione una especie</option>
                    @foreach ($this->species as $specie)
                        <option value="{{ $specie->id }}">{{ $specie->name }}</option>
                    @endforeach
                </x-select>

                <x-input label="Nombre de la raza" model="name" />
            </div>
        </div>

        <div class="flex gap-4 mt-8 pt-6 border-t border-gray-700">
            <button type="button" wire:click="cancel"
                class="flex-1 md:px-6 px-3 md:py-3 py-1.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors font-medium text-base">
                Cancelar
            </button>
            <button type="submit"
                class="flex-1 md:px-6 px-3 md:py-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-base shadow-lg">
                {{ $raza ? 'Actualizar' : 'Crear' }}
            </button>
        </div>
    </form>
</div>
