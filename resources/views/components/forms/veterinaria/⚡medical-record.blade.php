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
    <div class="mt-6 border-t border-gray-700 pt-6">
        <h3 class="text-sm font-semibold text-gray-400 mb-4 uppercase tracking-wider">Archivos Adjuntos</h3>

        <div class="space-y-4 md:flex md:gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300">Subir Archivos (Imágenes o PDF)</label>
                <input type="file" wire:model="form.attachments" multiple accept=".pdf,.jpg,.jpeg,.png,.webp"
                    class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 transition-all cursor-pointer">
                <p class="mt-1 text-xs text-gray-500">Puedes seleccionar hasta 2 archivos a la vez. Peso máximo 10MB
                    por archivo.</p>
                <div wire:loading wire:target="form.attachments">
                    <div class="flex items-center gap-2">
                        <div class="animate-spin rounded-full size-4 border-b-2 border-indigo-600"></div>
                        <span class="text-xs text-gray-400">Subiendo archivos...</span>
                    </div>
                </div>
                @error('form.attachments')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('form.attachments.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Previews for new uploads -->
            @if ($form->attachments && count(array_filter($form->attachments)) > 0)
                <div>
                    <h4 class="text-sm font-medium text-gray-400 mb-2">Archivos listos para subir:</h4>
                    <div class="flex flex-wrap gap-4">
                        @foreach ($form->attachments as $index => $attachment)
                            @if ($attachment)
                                <div class="flex flex-col items-center gap-2">
                                    <div
                                        class="relative group size-24 bg-gray-800 rounded-lg border border-gray-600 flex flex-col items-center justify-center p-2 ">
                                        @if (in_array(strtolower($attachment->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'webp']))
                                            <img src="{{ $attachment->temporaryUrl() }}"
                                                class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-100 transition-opacity">
                                        @else
                                            <svg class="size-8 text-red-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            <span
                                                class="text-[10px] text-white bg-black bg-opacity-50 px-1 rounded truncate mt-1 break-all w-full text-center relative z-10">{{ Str::limit($attachment->getClientOriginalName(), 15) }}</span>
                                        @endif

                                        <button type="button" wire:click="removeAttachment({{ $index }})"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600 z-20">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <label class="flex items-center space-x-2 text-xs text-gray-400 cursor-pointer">
                                        <input type="checkbox"
                                            wire:model="form.attachments_visibility.{{ $index }}"
                                            class="rounded bg-gray-900 border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span>Público</span>
                                    </label>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Existing files -->
            @if ($form->record && count($form->existing_attachments) > 0)
                <div>
                    <h4 class="text-sm font-medium text-gray-400 mb-2">Archivos guardados:</h4>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach ($form->existing_attachments as $file)
                            <div
                                class="group relative bg-gray-800 rounded-xl border border-gray-700 flex flex-col items-center justify-center p-3 h-32 hover:border-indigo-500 transition-colors">
                                @if ($file->type === 'image')
                                    <div class="absolute inset-0 rounded-xl overflow-hidden">
                                        <img src="{{ asset('storage/' . $file->file_path) }}"
                                            class="w-full h-full object-cover">
                                        <div
                                            class="absolute inset-0 bg-black/40 md:opacity-0 opacity-75 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                class="p-1.5 bg-gray-900/80 rounded-lg text-white hover:text-indigo-400"
                                                title="Ver archivo">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <button type="button"
                                                wire:click="toggleAttachmentVisibility({{ $file->id }})"
                                                class="p-1.5 {{ $file->is_visible_to_owner ? 'bg-green-600/80 hover:bg-green-700' : 'bg-gray-600/80 hover:bg-gray-700' }} rounded-lg text-white"
                                                title="{{ $file->is_visible_to_owner ? 'Hacer Privado' : 'Hacer Público' }}">
                                                @if ($file->is_visible_to_owner)
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                    </svg>
                                                @endif
                                            </button>
                                            <button type="button"
                                                wire:confirm="¿Estás seguro de eliminar este archivo?"
                                                wire:click="deleteAttachment({{ $file->id }})"
                                                class="p-1.5 bg-red-600/80 rounded-lg text-white hover:bg-red-700"
                                                title="Eliminar archivo">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <svg class="h-10 w-10 text-red-500 mb-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs text-gray-300 font-medium truncate w-full px-2 text-center"
                                        title="{{ $file->original_name }}">{{ $file->original_name }}</span>

                                    <div
                                        class="absolute inset-0 bg-gray-900/80 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                            class="p-1.5 bg-gray-800 rounded-lg text-white hover:text-indigo-400"
                                            title="Ver archivo">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <button type="button"
                                            wire:click="toggleAttachmentVisibility({{ $file->id }})"
                                            class="p-1.5 {{ $file->is_visible_to_owner ? 'bg-green-600/80 hover:bg-green-700' : 'bg-gray-600/80 hover:bg-gray-700' }} rounded-lg text-white"
                                            title="{{ $file->is_visible_to_owner ? 'Hacer Privado' : 'Hacer Público' }}">
                                            @if ($file->is_visible_to_owner)
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            @endif
                                        </button>
                                        <button type="button" wire:confirm="¿Estás seguro de eliminar este archivo?"
                                            wire:click="deleteAttachment({{ $file->id }})"
                                            class="p-1.5 bg-red-600 rounded-lg text-white hover:bg-red-700"
                                            title="Eliminar archivo">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
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
