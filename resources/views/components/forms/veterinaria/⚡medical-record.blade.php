<?php

use App\Livewire\Forms\MedicalRecordForm;
use App\Models\Customer;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\VeterinaryType;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public MedicalRecordForm $form;

    public ?MedicalRecord $record = null;

    public ?int $customerId = null;

    public ?int $petId = null; //

    public function mount(?MedicalRecord $record = null, ?int $customerId = null, ?int $petId = null)
    {
        info($customerId);

        $this->form->mount();
        info($customerId);

        if ($record && $record->exists) {
            $this->form->setRecord($record);
        } else {
            if ($customerId) {
                $this->form->customer_id = $customerId;
            }
            if ($petId) {
                $this->form->pet_id = $petId;
            }
        }
    }

    #[Computed]
    public function customers()
    {
        return Customer::where('veterinary_id', auth()->user()->veterinary_id)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function pets()
    {
        if (!$this->form->customer_id) {
            return collect();
        }

        return Pet::where('veterinary_id', auth()->user()->veterinary_id)
            ->where('customer_id', $this->form->customer_id)
            ->get();
    }

    #[Computed]
    public function catalogServices()
    {
        return VeterinaryType::where('veterinary_id', auth()->user()->veterinary_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function save()
    {
        if ($this->form->record) {
            $this->form->update();
            $this->dispatch('notify', message: 'Registro actualizado correctamente', type: 'success');
            $this->dispatch('record-updated');
        } else {
            $this->form->store();
            $this->dispatch('notify', message: 'Registro guardado correctamente', type: 'success');
            $this->dispatch('record-created');
        }
    }

    public function cancel()
    {
        $this->dispatch('form-cancelled');
    }

    public function deleteAttachment($fileId)
    {
        $file = App\Models\MedicalRecordFile::findOrFail($fileId);

        // Ensure the file belongs to the current medical record
        if ($this->form->record && $file->medical_record_id === $this->form->record->id) {
            if (Illuminate\Support\Facades\Storage::disk('public')->exists($file->file_path)) {
                Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();

            // Reload attachments
            $this->form->existing_attachments = $this->form->record->files()->get();
            $this->dispatch('notify', message: 'Archivo eliminado correctamente', type: 'success');
        }
    }

    public function toggleAttachmentVisibility($fileId)
    {
        $file = App\Models\MedicalRecordFile::findOrFail($fileId);

        if ($this->form->record && $file->medical_record_id === $this->form->record->id) {
            $file->update(['is_visible_to_owner' => !$file->is_visible_to_owner]);

            $this->form->existing_attachments = $this->form->record->files()->get();
            $this->dispatch('notify', message: $file->is_visible_to_owner ? 'Archivo ahora es público' : 'Archivo ahora es privado', type: 'success');
        }
    }

    public function removeAttachment($index)
    {
        $this->form->removeAttachment($index);
    }
}; ?>

<form wire:submit="save" class="md:p-6 p-4">
    <div class="flex justify-between items-center md:mb-6 mb-4">
        <h2 class="text-lg font-medium text-white">
            {{ $form->record ? 'Editar Historia Clínica' : 'Nuevo Registro Clínico' }}
        </h2>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-900/50 border border-red-500 rounded-lg" id="form-errors" x-data="{ show: true }"
            x-init="$nextTick(() => { document.getElementById('form-errors').scrollIntoView({ behavior: 'smooth', block: 'start' }); })" x-show="show">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 w-full">
                    <h3 class="text-sm font-medium text-red-300">Hay {{ $errors->count() }}
                        {{ $errors->count() === 1 ? 'error' : 'errores' }} que requieren tu atención:</h3>
                    <div class="mt-2 text-sm text-red-200">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" @click="show = false"
                            class="inline-flex bg-red-900/50 rounded-md p-1.5 text-red-400 hover:bg-red-900/80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-900 focus:ring-red-500 transition-colors">
                            <span class="sr-only">Descartar</span>
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 md:gap-6 gap-3">
        <!-- Customer Selection -->
        <x-select model="form.customer_id" label="Cliente" live="true">
            <option value="">Seleccionar</option>
            @foreach ($this->customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
        </x-select>

        @if ($form->previous_customer_name)
            <div class="col-span-1 md:col-span-1">
                <x-input model="form.previous_customer_name" label="Dueño anterior (Histórico)" disabled="true" />
                <p class="mt-1 text-xs text-blue-400">
                    * La mascota ha sido transferida.
                </p>
            </div>
        @endif

        <!-- Pet Selection -->
        <x-select model="form.pet_id" label="Mascota" live="true">
            <option value="">Seleccionar</option>
            @foreach ($this->pets as $pet)
                <option value="{{ $pet->id }}">{{ $pet->name }} - {{ $pet->specie_name }} -
                    {{ $pet->breed_name }}</option>
            @endforeach
        </x-select>

        <!-- Service Catalog Selection -->
        <x-select model="form.veterinary_type_id" label="Consulta (del Catálogo)" live="true">
            <option value="">Seleccionar</option>
            @foreach ($this->catalogServices as $service)
                <option value="{{ $service->id }}">{{ $service->name }}</option>
            @endforeach
            <option value="other">Otro (personalizado)</option>
        </x-select>

        @if ($form->veterinary_type_id === 'other')
            <x-input model="form.custom_type_name" label="Consulta Personalizada" />
        @endif

        <!-- Price and Weight -->
        <div class="grid grid-cols-2 gap-4">
            <x-input model="form.price" label="Precio ($)" type="number" step="1" min="0" />
            <x-input model="form.weight" label="Peso (Kg)" type="number" step="0.1" min="0" />
        </div>

        <!-- Date and Visibility -->
        <div class="grid grid-cols-2 gap-4">
            <x-input model="form.performed_at" label="Fecha Consulta" type="date" />
            <x-select model="form.is_visible_to_owner" label="Visible al Dueño">
                <option value="1">Sí</option>
                <option value="0">No</option>
            </x-select>
        </div>
    </div>

    <!-- Vitals and Pre-exam -->
    <div class="mt-6 border-t border-gray-700 pt-6">
        <h3 class="text-sm font-semibold text-gray-400 mb-4 uppercase tracking-wider">Constantes Vitales y Anamnesis
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 md:gap-6 gap-3 mb-4">
            <x-input model="form.temperature" label="Temperatura (°C)" type="number" step="0.1" />
            <x-input model="form.heart_rate" label="Frec. Cardíaca (bpm)" type="number" />
            <x-input model="form.respiratory_rate" label="Frec. Resp. (rpm)" type="number" />
        </div>
        <x-textarea model="form.anamnesis" label="Anamnesis / Motivo de consulta"
            class="field-sizing-content min-h-8" />
    </div>

    <!-- Clinical Details -->
    <div class="mt-6 border-t border-gray-700 pt-6">
        <h3 class="text-sm font-semibold text-gray-400 mb-4 uppercase tracking-wider">Examen Físico y Diagnóstico</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-6 gap-3 mb-4">
            <x-textarea model="form.physical_exam_details" label="Detalles del Examen Físico" />
            <div class="space-y-4">
                <x-textarea model="form.diagnosis" label="Diagnóstico Presuntivo/Definitivo" />
                <x-select model="form.prognosis" label="Pronóstico">
                    <option value="">Seleccionar...</option>
                    <option value="Bueno">Bueno</option>
                    <option value="Reservado">Reservado</option>
                    <option value="Malo">Malo</option>
                </x-select>
            </div>
        </div>
    </div>

    <!-- Treatment and Plan -->
    <div class="mt-6 border-t border-gray-700 pt-6">
        <h3 class="text-sm font-semibold text-gray-400 mb-4 uppercase tracking-wider">Tratamiento y Plan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-6 gap-3 mb-4">
            <x-textarea model="form.treatment_plan" label="Plan de Tratamiento (Interno)" rows="4" />
            <x-textarea model="form.prescriptions" label="Receta / Prescripciones" rows="4" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-6 gap-3">
            <x-textarea model="form.recommendations" label="Recomendaciones (Para el Dueño)"
                class="field-sizing-content min-h-8" />
            <x-input model="form.next_appointment_at" label="Próxima Cita" type="date" />
        </div>
    </div>

    <!-- General Notes -->
    <div class="mt-6 border-t border-gray-700 pt-6">
        <h3 class="text-sm font-semibold text-gray-400 mb-4 uppercase tracking-wider">Otras Observaciones</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-6 gap-3">
            <x-textarea model="form.notes" label="Observaciones Adicionales (Públicas)"
                class="field-sizing-content min-h-8" />
            <x-textarea model="form.notes_inside" label="Observaciones Adicionales Internas"
                class="field-sizing-content min-h-8" />
        </div>
    </div>

    <!-- Attachments -->
    @include('components.forms.veterinaria.partials.medical-record-attachments')

    <div class="mt-6 flex justify-center md:gap-5 gap-4">
        <button type="button" wire:click="cancel"
            class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
            Cancelar
        </button>
        <button type="submit"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            {{ $form->record ? 'Actualizar' : 'Guardar' }}
        </button>
    </div>
</form>
