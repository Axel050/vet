<?php

namespace App\Livewire\Forms;

use App\Models\Customer;
use Livewire\Form;

class CustomerForm extends Form
{
    public ?Customer $customer = null;

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
        $this->name = $customer->name;
        $this->phone = $customer->phone ?? '';
        $this->email = $customer->email ?? '';
        $this->address = $customer->address ?? '';
    }

    protected function rules(): array
    {
        return [
            'phone' => 'required|string|min:8|max:15|unique:customers,phone,'.$this->customer?->id,
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:customers,email,'.$this->customer?->id,
            'address' => 'required|string|min:3|max:255',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.min' => 'El teléfono debe tener al menos 8 caracteres.',
            'phone.max' => 'El teléfono debe tener al menos 15 caracteres.',
            'phone.unique' => 'El teléfono ya existe.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser un email válido.',
            'email.unique' => 'El email ya existe.',
            'address.required' => 'La dirección es obligatoria.',
            'address.min' => 'La dirección debe tener al menos 3 caracteres.',
            'address.max' => 'La dirección debe tener al menos 255 caracteres.',
        ];
    }

    public function store(): void
    {
        $this->validate();

        Customer::create([
            'veterinary_id' => auth()->user()->veterinary_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
        ]);

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $this->customer->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
        ]);

        $this->reset();
    }
}
