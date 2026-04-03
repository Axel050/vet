<?php

namespace App\Livewire\Forms;

use App\Models\VeterinaryType;
use Livewire\Form;

class TypeForm extends Form
{
    public ?VeterinaryType $type = null;

    public string $name = '';

    public string $description = '';

    public string $icon = '';

    public bool $is_active = true;

    public bool $show_in_landing = false;

    public function setType(VeterinaryType $type): void
    {
        $this->type = $type;
        $this->name = $type->name;
        $this->description = $type->description ?? '';
        $this->is_active = (bool) $type->is_active;
        $this->show_in_landing = (bool) $type->show_in_landing;
        $this->icon = $type->icon ?? '';
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100', 'unique:veterinary_types,name,'.$this->type?->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'show_in_landing' => ['boolean'],
            'icon' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.unique' => 'El nombre ya existe.',
        ];
    }

    public function store(): VeterinaryType
    {

        $type = VeterinaryType::create([
            'veterinary_id' => auth()->user()->veterinary_id,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'show_in_landing' => $this->show_in_landing,
            'icon' => $this->icon,
        ]);

        $this->reset();

        return $type;
    }

    public function update(): void
    {

        $this->type->update([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'show_in_landing' => $this->show_in_landing,
            'icon' => $this->icon,
        ]);

        $this->reset();
    }
}
