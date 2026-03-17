<?php

use App\Livewire\Forms\CustomerForm;
use Livewire\Component;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public CustomerForm $form;
    public ?Customer $customer = null;

    public function mount(?Customer $customer = null): void
    {
        if ($customer?->exists) {
            $this->customer = $customer;
            $this->form->setCustomer($customer);
        }
    }

    public function cancel(): void
    {
        $this->dispatch('form-cancelled');
    }

    public function save(): void
    {
        if ($this->customer) {
            $this->form->update();
            $this->dispatch('notify', message: 'Cliente actualizado correctamente', type: 'success');
            $this->dispatch('customer-updated');
        } else {
            $this->form->store();
            $this->dispatch('notify', message: 'Cliente creado correctamente', type: 'success');
            $this->dispatch('customer-created');
            $this->reset();
        }
    }
};
?>

<div>
    <h2 class="text-xl font-semibold text-white md:mb-6 mb-4">
        {{ $customer ? 'Editar Cliente' : 'Crear Cliente' }}
    </h2>

    <form wire:submit="save" class="md:space-y-6 space-y-3" autocomplete="off">

        <div class="bg-gray-800/50 md:p-4 p-2 rounded-lg border border-gray-700">
            <div class="md:space-y-4 space-y-2">
                <x-input label="Nombre Completo" model="form.name" />
                <x-input label="Teléfono" model="form.phone" />
                <x-input label="Email" model="form.email" />
                <x-input label="Dirección" model="form.address" />
            </div>
        </div>

        <div class="flex gap-4 md:mt-8 mt-4 md:pt-6 pt-4 border-t border-gray-700">
            <button type="button" wire:click="cancel"
                class="flex-1 md:px-6 px-3 md:py-3 py-1.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors font-medium text-base">
                Cancelar
            </button>
            <button type="submit"
                class="flex-1 md:px-6 px-3 md:py-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-base shadow-lg">
                {{ $customer ? 'Actualizar' : 'Crear' }}
            </button>
        </div>
    </form>
</div>
