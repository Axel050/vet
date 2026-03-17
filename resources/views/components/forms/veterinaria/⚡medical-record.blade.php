<?php

use App\Livewire\Forms\MedicalRecordForm;
use App\Models\Customer;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\VeterinaryType;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
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
}; ?>

<form wire:submit="save" class="md:p-6 p-4">
    <div class="flex justify-between items-center md:mb-6 mb-4">
        <h2 class="text-lg font-medium text-white">
            {{ $form->record ? 'Editar Historia Clínica' : 'Nuevo Registro Clínico' }}
        </h2>
    </div>

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
                <option value="{{ $pet->id }}">{{ $pet->name }} - {{ $pet->species?->name }} -
                    {{ $pet->breed?->name }}</option>
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
        <x-textarea model="form.anamnesis" label="Anamnesis / Motivo de consulta" rows="3" />
    </div>

    <!-- Clinical Details -->
    <div class="mt-6 border-t border-gray-700 pt-6">
        <h3 class="text-sm font-semibold text-gray-400 mb-4 uppercase tracking-wider">Examen Físico y Diagnóstico</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-6 gap-3 mb-4">
            <x-textarea model="form.physical_exam_details" label="Detalles del Examen Físico" rows="4" />
            <div class="space-y-4">
                <x-textarea model="form.diagnosis" label="Diagnóstico Presuntivo/Definitivo" rows="2" />
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
            <x-textarea model="form.recommendations" label="Recomendaciones (Para el Dueño)" rows="3" />
            <x-input model="form.next_appointment_at" label="Próxima Cita" type="date" />
        </div>
    </div>

    <!-- General Notes -->
    <div class="mt-6 border-t border-gray-700 pt-6">
        <h3 class="text-sm font-semibold text-gray-400 mb-4 uppercase tracking-wider">Otras Observaciones</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-6 gap-3">
            <x-textarea model="form.notes" label="Observaciones Adicionales (Públicas)" rows="2" />
            <x-textarea model="form.notes_inside" label="Observaciones Adicionales Internas" rows="2" />
        </div>
    </div>

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
