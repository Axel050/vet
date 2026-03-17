<?php

namespace App\Livewire\Forms;

use App\Models\MedicalRecord;
use Illuminate\Support\Carbon;
use Livewire\Form;

class MedicalRecordForm extends Form
{
    public ?MedicalRecord $record = null;

    public $customer_id;

    public $previous_customer_name = null;

    public $pet_id;

    public $veterinary_type_id = null;

    public $custom_type_name = '';

    public $price = null;

    public $weight = null;

    public $performed_at;

    public $notes = '';

    public $notes_inside = '';

    public $temperature = null;

    public $heart_rate = null;

    public $respiratory_rate = null;

    public $anamnesis = '';

    public $physical_exam_details = '';

    public $diagnosis = '';

    public $prognosis = '';

    public $treatment_plan = '';

    public $prescriptions = '';

    public $recommendations = '';

    public $next_appointment_at = null;

    public $is_visible_to_owner = true;

    public function mount()
    {
        $this->performed_at = Carbon::now()->format('Y-m-d');
    }

    public function setRecord(MedicalRecord $record): void
    {
        $this->record = $record;

        // Original owner at the time of the service (for historical reference)
        if ($record->pet && $record->pet->customer_id !== $record->customer_id) {
            $this->previous_customer_name = $record->customer?->name;
        } else {
            $this->previous_customer_name = null;
        }

        // Use the pet's CURRENT owner for the main selection field
        $this->customer_id = $record->pet ? $record->pet->customer_id : $record->customer_id;

        $this->pet_id = $record->pet_id;
        $this->veterinary_type_id = $record->veterinary_type_id ?? ($record->custom_type_name ? 'other' : null);
        $this->custom_type_name = $record->custom_type_name ?? '';
        $this->price = $record->price;
        $this->weight = $record->weight;
        $this->performed_at = $record->performed_at->format('Y-m-d');
        $this->notes = $record->notes ?? '';
        $this->notes_inside = $record->notes_inside ?? '';
        $this->temperature = $record->temperature;
        $this->heart_rate = $record->heart_rate;
        $this->respiratory_rate = $record->respiratory_rate;
        $this->anamnesis = $record->anamnesis ?? '';
        $this->physical_exam_details = $record->physical_exam_details ?? '';
        $this->diagnosis = $record->diagnosis ?? '';
        $this->prognosis = $record->prognosis ?? '';
        $this->treatment_plan = $record->treatment_plan ?? '';
        $this->prescriptions = $record->prescriptions ?? '';
        $this->recommendations = $record->recommendations ?? '';
        $this->next_appointment_at = $record->next_appointment_at?->format('Y-m-d');
        $this->is_visible_to_owner = (bool) $record->is_visible_to_owner;
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'El cliente es obligatorio.',
            'pet_id.required' => 'La mascota es obligatoria.',
            'veterinary_type_id.required_without' => 'El tipo de consulta es obligatorio.',
            'custom_type_name.required_without' => 'El nombre del tipo de consulta es obligatorio.',
            'custom_type_name.required' => 'El nombre del tipo de consulta personalizado es obligatorio.',
            'price.required' => 'El precio es obligatorio.',
            'performed_at.required' => 'La fecha es obligatoria.',
        ];
    }

    public function store(): void
    {
        $this->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'pet_id' => ['required', 'exists:pets,id'],
            'veterinary_type_id' => [
                $this->veterinary_type_id === 'other' ? 'nullable' : 'required_without:custom_type_name',
                'nullable',
                $this->veterinary_type_id !== 'other' && $this->veterinary_type_id ? 'exists:veterinary_types,id' : '',
            ],
            'custom_type_name' => [
                $this->veterinary_type_id === 'other' ? 'required' : 'nullable',
                'string',
                'max:255',
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'performed_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'notes_inside' => ['nullable', 'string'],
            'temperature' => ['nullable', 'numeric', 'min:0'],
            'heart_rate' => ['nullable', 'integer', 'min:0'],
            'respiratory_rate' => ['nullable', 'integer', 'min:0'],
            'anamnesis' => ['nullable', 'string'],
            'physical_exam_details' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'prognosis' => ['nullable', 'string'],
            'treatment_plan' => ['nullable', 'string'],
            'prescriptions' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'next_appointment_at' => ['nullable', 'date'],
            'is_visible_to_owner' => ['boolean'],
        ]);

        MedicalRecord::create([
            'veterinary_id' => auth()->user()->veterinary_id,
            'customer_id' => $this->customer_id,
            'pet_id' => $this->pet_id,
            'veterinary_type_id' => $this->veterinary_type_id === 'other' ? null : $this->veterinary_type_id,
            'custom_type_name' => $this->custom_type_name,
            'price' => $this->price,
            'weight' => $this->weight,
            'performed_at' => $this->performed_at,
            'notes' => $this->notes,
            'notes_inside' => $this->notes_inside,
            'temperature' => $this->temperature,
            'heart_rate' => $this->heart_rate,
            'respiratory_rate' => $this->respiratory_rate,
            'anamnesis' => $this->anamnesis,
            'physical_exam_details' => $this->physical_exam_details,
            'diagnosis' => $this->diagnosis,
            'prognosis' => $this->prognosis,
            'treatment_plan' => $this->treatment_plan,
            'prescriptions' => $this->prescriptions,
            'recommendations' => $this->recommendations,
            'next_appointment_at' => $this->next_appointment_at,
            'is_visible_to_owner' => (bool) $this->is_visible_to_owner,
        ]);

        $this->reset(['veterinary_type_id', 'custom_type_name', 'price', 'weight', 'notes', 'notes_inside', 'temperature', 'heart_rate', 'respiratory_rate', 'anamnesis', 'physical_exam_details', 'diagnosis', 'prognosis', 'treatment_plan', 'prescriptions', 'recommendations', 'next_appointment_at', 'is_visible_to_owner']);
        $this->performed_at = Carbon::now()->format('Y-m-d');
    }

    public function update(): void
    {
        $this->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'pet_id' => ['required', 'exists:pets,id'],
            'veterinary_type_id' => [
                $this->veterinary_type_id === 'other' ? 'nullable' : 'required_without:custom_type_name',
                'nullable',
                $this->veterinary_type_id !== 'other' && $this->veterinary_type_id ? 'exists:veterinary_types,id' : '',
            ],
            'custom_type_name' => [
                $this->veterinary_type_id === 'other' ? 'required' : 'nullable',
                'string',
                'max:255',
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'performed_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'notes_inside' => ['nullable', 'string'],
            'temperature' => ['nullable', 'numeric', 'min:0'],
            'heart_rate' => ['nullable', 'integer', 'min:0'],
            'respiratory_rate' => ['nullable', 'integer', 'min:0'],
            'anamnesis' => ['nullable', 'string'],
            'physical_exam_details' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'prognosis' => ['nullable', 'string'],
            'treatment_plan' => ['nullable', 'string'],
            'prescriptions' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'next_appointment_at' => ['nullable', 'date'],
            'is_visible_to_owner' => ['boolean'],
        ]);

        $this->record->update([
            'customer_id' => $this->customer_id,
            'pet_id' => $this->pet_id,
            'veterinary_type_id' => $this->veterinary_type_id === 'other' ? null : $this->veterinary_type_id,
            'custom_type_name' => $this->custom_type_name,
            'price' => $this->price,
            'weight' => $this->weight,
            'performed_at' => $this->performed_at,
            'notes' => $this->notes,
            'notes_inside' => $this->notes_inside,
            'temperature' => $this->temperature,
            'heart_rate' => $this->heart_rate,
            'respiratory_rate' => $this->respiratory_rate,
            'anamnesis' => $this->anamnesis,
            'physical_exam_details' => $this->physical_exam_details,
            'diagnosis' => $this->diagnosis,
            'prognosis' => $this->prognosis,
            'treatment_plan' => $this->treatment_plan,
            'prescriptions' => $this->prescriptions,
            'recommendations' => $this->recommendations,
            'next_appointment_at' => $this->next_appointment_at,
            'is_visible_to_owner' => (bool) $this->is_visible_to_owner,
        ]);

        $this->reset();
    }
}
