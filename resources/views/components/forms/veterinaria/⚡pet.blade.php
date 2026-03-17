<?php

use App\Livewire\Forms\PetForm;
use Livewire\Component;
use App\Models\Customer;
use App\Models\Species;
use App\Models\Breed;
use App\Models\Pet;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public ?Customer $customerToEdit = null;
    public $brands;
    public $models = [];
    public PetForm $form;
    public bool $confirmingDeletion = false;
    public $deletionId;

    public function mount(?Customer $cliente = null): void
    {
        $this->customerToEdit = $cliente;
        $this->brands = Species::orderBy('name', 'asc')->get();
        if ($cliente) {
            $this->form->customer_id = $cliente->id;
        }
    }

    public function savePet(): void
    {
        if ($this->form->pet) {
            $this->form->update();
            $this->dispatch('notify', message: 'Mascota actualizada correctamente', type: 'success');
            $this->dispatch('pet-updated');
        } else {
            $this->form->store();
            $this->dispatch('notify', message: 'Mascota agregada correctamente', type: 'success');
            $this->dispatch('pet-saved');
        }
        $this->cancelEdit();
    }

    public function editPet($petId): void
    {
        $pet = Pet::findOrFail($petId);
        $this->form->setPet($pet);

        if ($this->form->species_id !== 'other') {
            $this->models = Breed::where('species_id', $this->form->species_id)->get();
        } else {
            $this->models = [];
        }
    }

    public function cancelEdit(): void
    {
        $this->form->reset();
        $this->form->customer_id = $this->customerToEdit->id;
        $this->models = [];
    }

    public function updatedFormSpeciesId($value)
    {
        if ($value === 'other') {
            $this->models = [];
            $this->form->breed_id = 'other';
        } else {
            $this->models = Breed::where('species_id', $value)->get();
            $this->form->breed_id = null;
        }
    }

    public function closePetForm()
    {
        $this->dispatch('form-cancelled');
    }

    public function confirmDeletion($id): void
    {
        $this->deletionId = $id;
        $this->confirmingDeletion = true;
    }

    public function delete(): void
    {
        $pet = Pet::where('id', $this->deletionId)
            ->where('veterinary_id', Auth::user()->veterinary_id)
            ->firstOrFail();

        $pet->delete();

        $this->confirmingDeletion = false;
        $this->deletionId = null;

        $this->dispatch('notify', message: 'Mascota eliminada correctamente', type: 'success');
        $this->dispatch('pet-deleted');
    }
};
?>

<div>
    <div class="p-2  bg-gray-800 border-b border-gray-700 flex justify-between items-center" id="pets-header">
        <h2 class="text-lg font-medium text-white">
            Mascotas de {{ $customerToEdit?->name }}
        </h2>
    </div>

    <div class="p-0 md:p-4 bg-gray-800 space-y-6 flex flex-col">
        <!-- Add Pet Form -->
        <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
            <h3 class="text-sm font-semibold text-gray-300 mb-3 uppercase tracking-wider">
                {{ $form->pet ? 'Editar Mascota' : 'Agregar Nueva Mascota' }}
            </h3>
            <form wire:submit="savePet">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-2 md:gap-4 items-end">
                    <div class="md:col-span-2">
                        <x-select model="form.species_id" label="Especie" live="true">
                            <option value="">Seleccionar...</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                            <option value="other">Otra</option>
                        </x-select>
                    </div>

                    @if ($form->species_id === 'other')
                        <div class="md:col-span-2">
                            <x-input label="Especie" model="form.specie_custom" />
                        </div>
                    @endif

                    <div class="md:col-span-2">
                        <x-select model="form.breed_id" label="Raza" live="true">
                            <option value="">Seleccionar...</option>
                            @foreach ($models as $model)
                                <option value="{{ $model->id }}">{{ $model->name }}</option>
                            @endforeach
                            <option value="other">Otra</option>
                        </x-select>
                    </div>

                    @if ($form->breed_id === 'other')
                        <div class="md:col-span-2">
                            <x-input label="Raza" model="form.breed_custom" />
                        </div>
                    @endif

                    <div class="md:col-span-2">
                        <x-input label="Nombre Mascota" model="form.name" class="text-red-500" />
                    </div>

                    <div class="md:col-span-1">
                        <x-input label="Año Nac." model="form.birth_year" type="number" />
                    </div>

                    <div class="md:col-span-1">
                        <x-input label="Fecha Nac." model="form.date_of_birth" type="date" />
                    </div>

                    <div class="md:col-span-1">
                        <x-select label="Sexo" model="form.gender">
                            <option value="unknown">Desconocido</option>
                            <option value="male">Macho</option>
                            <option value="female">Hembra</option>
                        </x-select>
                    </div>

                    <div class="md:col-span-1">
                        <x-input label="ID Microchip" model="form.microchip_id" />
                    </div>

                    <div class="md:col-span-1">
                        <x-input label="Color" model="form.color" />
                    </div>

                    <div class="md:col-span-1">
                        <x-input label="Peso (Kg)" model="form.weight" type="number" step="0.01" />
                    </div>

                    <div class="md:col-span-1">
                        <x-select label="Esterilizado" model="form.is_sterilized">
                            <option value="0">No</option>
                            <option value="1">Sí</option>
                        </x-select>
                    </div>

                    <div class="md:col-span-1">
                        <x-input label="Tipo Sangre" model="form.blood_type" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input label="Alergias" model="form.allergies" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input label="Med. Crónica" model="form.chronic_medications" />
                    </div>

                    <div class="md:col-span-3 flex items-center gap-4 md:justify-start justify-between">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1 uppercase tracking-wider">Foto de
                                la
                                Mascota</label>
                            <input type="file" wire:model="form.photo"
                                class="w-full text-sm text-gray-400 file:mr-4 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 transition-all cursor-pointer" />
                            @error('form.photo')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="size-16 rounded-xl overflow-hidden bg-gray-950 border border-gray-700 shrink-0">
                            @if ($form->photo)
                                <img src="{{ $form->photo->temporaryUrl() }}" class="size-full object-cover">
                            @elseif ($form->pet && $form->pet->photo_path)
                                <img src="{{ asset('storage/' . $form->pet->photo_path) }}"
                                    class="size-full object-cover">
                            @else
                                <div class="size-full flex items-center justify-center text-gray-700">
                                    <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="md:col-span-3 flex gap-3 md:justify-center justify-between md:mt-0 mt-2">
                        @if ($form->pet)
                            <button type="button" wire:click="cancelEdit"
                                class="w-1/2 md:w-fit bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-2 rounded-lg transition-colors text-sm">
                                Cancelar
                            </button>
                        @endif
                        <button type="submit"
                            class="w-fit bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                            {{ $form->pet ? 'Actualizar' : 'Agregar' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Pets List -->
        <div>
            <h3 class="text-sm font-semibold text-gray-300 mb-3 uppercase tracking-wider">Mascotas Registradas</h3>
            <div class="border border-gray-700 rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-950">
                        <tr
                            class="text-left text-xs font-medium text-gray-400 uppercase [&>th]:px-2 md:[&>th]:px-4 [&>th]:py-1 md:[&>th]:py-2">
                            <th>Especie / Raza</th>
                            <th>Nombre</th>
                            <th>Año/Sexo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        @if ($customerToEdit && $customerToEdit->pets->isNotEmpty())
                            @foreach ($customerToEdit->pets as $pet)
                                <tr wire:key="pet-{{ $pet->id }}" class="divide-x divide-gray-600">
                                    <td class="px-4 py-3 text-sm text-white">
                                        {{ $pet->species?->name }} {{ $pet->breed?->name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-300 font-medium">{{ $pet->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-300">
                                        {{ $pet->birth_year ?: '-' }} /
                                        {{ $pet->gender == 'male' ? 'M' : ($pet->gender == 'female' ? 'H' : 'D') }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex md:flex-row flex-col justify-end items-center gap-3 md:gap-5">
                                            @php
                                                $shareUrl = route('public.history', [
                                                    'veterinary' => Auth::user()->veterinary->slug,
                                                    'pet' => $pet->id, // Use ID for pet history
                                                    'token' => $pet->public_token,
                                                ]);
                                            @endphp
                                            <button
                                                @click="navigator.clipboard.writeText('{{ $shareUrl }}').then(() => { window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Enlace copiado correctamente', type: 'success' } })) })"
                                                class="text-gray-400 hover:text-white transition-colors">
                                                <svg class="size-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3">
                                                    </path>
                                                </svg>
                                            </button>

                                            @php
                                                $message = urlencode(
                                                    'Hola ' .
                                                        ($customerToEdit->name ?? '') .
                                                        '! Aquí puedes consultar la historia clínica de ' .
                                                        $pet->name .
                                                        ': ' .
                                                        $shareUrl,
                                                );
                                                $phone = preg_replace('/[^0-9]/', '', $customerToEdit->phone ?? '');
                                            @endphp
                                            <a href="https://wa.me/{{ $phone }}?text={{ $message }}"
                                                target="_blank"
                                                class="text-green-500 hover:text-green-400 transition-colors">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 0 5.414 0 12.05c0 2.123.553 4.197 1.603 6.023L0 24l6.135-1.61a11.782 11.782 0 005.91 1.586h.005c6.634 0 12.048-5.414 12.048-12.05a11.763 11.763 0 00-3.483-8.514z">
                                                    </path>
                                                </svg>
                                            </a>

                                            <button wire:click="editPet({{ $pet->id }})"
                                                x-on:click="$nextTick(() => { document.getElementById('pets-header').scrollIntoView({ behavior: 'smooth', block: 'start' }); })"
                                                class="text-blue-400 hover:text-blue-300 text-sm">Editar</button>
                                            <button wire:click="confirmDeletion({{ $pet->id }})"
                                                class="text-red-400 hover:text-red-300 text-sm">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 text-sm">Este cliente no
                                    tiene mascotas registradas.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <button wire:click="closePetForm"
            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm mx-auto self-center">Cerrar</button>

        <x-confirmation-modal wire:model="confirmingDeletion" action="delete">
            <x-slot name="title">Eliminar Mascota</x-slot>
            <x-slot name="content">¿Estás seguro de que deseas eliminar esta mascota?</x-slot>
        </x-confirmation-modal>
    </div>
</div>
