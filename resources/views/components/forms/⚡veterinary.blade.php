<?php

use Livewire\Component;
use App\Models\Veterinary;
use App\Enums\SubscriptionStatus;
use Livewire\Attributes\Computed;
use App\Models\PlanPrice;
use App\Livewire\Forms\VeterinaryForm;

new class extends Component {
    public ?Veterinary $veterinary = null;

    public VeterinaryForm $form;

    public function mount(?Veterinary $veterinary = null): void
    {
        if ($veterinary?->exists) {
            $this->veterinary = $veterinary;
            $this->form->setVeterinary($veterinary);
        } else {
            $this->form->trialEndsAt = now()->addDays(14)->format('Y-m-d');
        }
    }

    #[Computed]
    public function planPrices()
    {
        return PlanPrice::all();
    }

    public function updatedFormBusinessName(): void
    {
        if (!$this->veterinary) {
            $this->form->slug = str($this->form->businessName)->slug();
        }
    }

    public function cancel(): void
    {
        $this->dispatch('form-cancelled');
    }

    public function save(): void
    {
        if ($this->veterinary) {
            $this->form->update();
            $this->dispatch('veterinary-updated');
            $this->dispatch('notify', message: 'Veterinaria actualizada correctamente', type: 'success');
        } else {
            $this->form->store();
            $this->dispatch('veterinary-created');
            $this->dispatch('notify', message: 'Veterinaria creada correctamente', type: 'success');
        }
    }

    public function updatedFormSubscriptionStatus($value)
    {
        if (!$this->veterinary) {
            if ($value === SubscriptionStatus::TRIAL->value) {
                $this->form->trialEndsAt = now()->addDays(14)->format('Y-m-d');
            }
        }
    }
};
?>

<div>
    <h2 class="md:text-2xl text-xl font-semibold text-white md:mb-6 mb-4">
        {{ $veterinary ? 'Editar Veterinaria' : 'Crear Veterinaria' }}
    </h2>

    <form wire:submit="save" class="md:space-y-6 space-y-3" autocomplete="off">

        <!-- Sección Datos del Negocio -->
        <div class="bg-gray-800/50 p-4 rounded-lg border border-gray-700">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <x-input label="Nombre de la Veterinaria" model="form.businessName" live="true" />

                <x-input label="Slug" model="form.slug" live="true" disabled />

                <x-select label="Plan" model="form.plan">
                    @foreach ($this->planPrices as $planPrice)
                        <option value="{{ $planPrice->plan }}">{{ $planPrice->plan }}</option>
                    @endforeach
                </x-select>

                <x-select label="Estado Suscripción" model="form.subscriptionStatus" live="true">
                    @foreach (SubscriptionStatus::cases() as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </x-select>

                @if ($form->subscriptionStatus === SubscriptionStatus::TRIAL->value)
                    <x-input label="Fin de Prueba (Trial)" model="form.trialEndsAt" type="date" />
                @endif

                @if ($form->subscriptionStatus !== SubscriptionStatus::TRIAL->value)
                    <x-input label="Fin de Suscripción" model="form.subscriptionEndsAt" type="date" />
                @endif

                <x-input label="Dirección" model="form.address" />

                <x-input label="Teléfono" model="form.phone" />

            </div>
        </div>

        <!-- Sección Datos del Dueño -->
        <div class="bg-gray-800/50 p-4 rounded-lg border border-gray-700">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Nombre del Dueño -->
                <x-input label="Nombre del Dueño" model="form.name" />

                <!-- Email -->
                <x-input label="Email" model="form.email" />

                <!-- Password -->
                <x-input label="Contraseña" model="form.password" type="password" />

            </div>
        </div>


        <div class="flex gap-4 mt-8 pt-6 border-t border-gray-700">
            <button type="button" wire:click="cancel"
                class="flex-1 md:px-6 px-2 md:py-3 py-1.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors font-medium text-base">
                Cancelar
            </button>
            <button type="submit"
                class="flex-1 md:px-6 px-2 md:py-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-base shadow-lg">
                {{ $veterinary ? 'Actualizar' : 'Crear' }}
            </button>
        </div>
    </form>
</div>
