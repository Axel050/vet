<?php

use App\Models\Veterinary;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Gestión de Veterinarias')] class extends Component {
    public bool $showCreateForm = false;

    public bool $showEditForm = false;

    public bool $showModal = false;

    public bool $showPaymentsModal = false;

    public string $search = '';
    public string $statusFilter = '';

    public bool $confirmingVeterinariaDeletion = false;

    public $veterinariaIdDeletion = null;

    public ?Veterinary $veterinariaToEdit = null;

    public ?Veterinary $veterinariaForPayments = null;

    public function updatedStatusFilter() {}

    #[Computed]
    public function veterinarias()
    {
        $veterinarias = Veterinary::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')->orWhere('slug', 'like', '%' . $this->search . '%');
        })
            ->when($this->statusFilter, function ($query) {
                $query->where('subscription_status', $this->statusFilter);
            })
            ->latest()
            ->paginate(20);

        $veterinarias->getCollection()->each->syncSubscriptionStatus();

        return $veterinarias;
    }

    public function toggleCreateForm(): void
    {
        Gate::authorize('manage-veterinarias');

        if ($this->showCreateForm || $this->showEditForm) {
            $this->handleFormCancelled();

            return;
        }

        $this->veterinariaToEdit = null;
        $this->showCreateForm = true;
        $this->showModal = true;
    }

    public function editVeterinaria(int $id): void
    {
        Gate::authorize('manage-veterinarias');

        $this->veterinariaToEdit = Veterinary::findOrFail($id);
        $this->showEditForm = true;
        $this->showCreateForm = false;
        $this->showModal = true;
    }

    public function confirmVeterinariaDeletion(int $id): void
    {
        Gate::authorize('manage-veterinarias');

        $this->veterinariaIdDeletion = $id;
        $this->confirmingVeterinariaDeletion = true;
    }

    public function deleteVeterinaria(): void
    {
        Gate::authorize('manage-veterinarias');

        Veterinary::findOrFail($this->veterinariaIdDeletion)->delete();

        $this->confirmingVeterinariaDeletion = false;
        $this->veterinariaIdDeletion = null;

        $this->dispatch('veterinary-deleted');
        $this->dispatch('notify', message: 'Veterinaria eliminada correctamente', type: 'success');
    }

    #[On('veterinary-created')]
    #[On('veterinary-updated')]
    #[On('form-cancelled')]
    #[On('payment-recorded')]
    public function handleFormCancelled(): void
    {
        $this->reset(['showCreateForm', 'showEditForm', 'showModal', 'showPaymentsModal', 'veterinariaToEdit', 'veterinariaForPayments']);
    }

    public function openPayments(int $id): void
    {
        Gate::authorize('manage-veterinarias');
        $this->veterinariaForPayments = Veterinary::findOrFail($id);
        $this->showPaymentsModal = true;
    }

    public function canManage(): bool
    {
        return auth()->user()->can('manage-veterinarias');
    }
};

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="w-full">
            <h1 class="md:text-2xl text-xl font-semibold text-white">Gestión de Veterinarias</h1>
            <p class="text-gray-400 mt-1">Administración de clínicas veterinarias</p>
        </div>

        <div class="flex gap-4 w-full md:justify-end justify-between">

            <div class="flex flex-col md:flex-row md:gap-4 md:items-center items-start gap-2">

                <div class="relative w-full md:w-64">
                    <input wire:model.live="search" type="text"
                        class="w-full py-1 bg-gray-700 border-gray-600 text-white rounded-lg pl-10 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Buscar veterinaria...">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <select wire:model.live="statusFilter"
                    class="bg-gray-700 py-1.5 px-2 border-gray-600 text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Todos los estados</option>
                    <option value="active">Activos</option>
                    <option value="trial">Prueba (Trial)</option>
                    <option value="past_due">Vencidos</option>
                    <option value="suspended">Suspendidos</option>
                </select>

            </div>

            @can('manage-veterinarias')
                <button wire:click="toggleCreateForm"
                    class="md:px-6 px-4 md:py-2 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 cursor-pointer transition-colors text-nowrap h-fit">
                    + Crear
                </button>
            @endcan
        </div>

    </div>


    <!-- Modals -->
    <x-modal show="showModal" wire:click="handleFormCancelled" maxWidth="max-w-4xl">
        @if ($showEditForm && $veterinariaToEdit)
            <livewire:forms.veterinary :veterinary="$veterinariaToEdit" :key="'edit-' . $veterinariaToEdit->id" />
        @elseif ($showCreateForm)
            <livewire:forms.veterinary />
        @endif
    </x-modal>

    <x-modal show="showPaymentsModal" wire:click="handleFormCancelled" maxWidth="max-w-4xl">
        @if ($showPaymentsModal && $veterinariaForPayments)
            <livewire:forms.veterinary-payments :veterinary="$veterinariaForPayments" :key="'payments-' . $veterinariaForPayments->id" />
        @endif
    </x-modal>

    <!-- Tabla -->
    <div class="bg-gray-800 rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-950 text-gray-300">
                <tr class="[&>th]:px-3 [&>th]:md:px-6 [&>th]:py-3 [&>th]:md:py-4 [&>th]:text-left">
                    <th>Veterinaria</th>
                    <th>Plan</th>
                    <th>Estado</th>
                    <th>Vence</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y  divide-gray-500">
                @foreach ($this->veterinarias as $veterinaria)
                    <tr wire:key="veterinaria-{{ $veterinaria->id }}"
                        class="[&>td]:px-2 [&>td]:md:px-6 [&>td]:py-2 [&>td]:md:py-4 divide-x divide-gray-600">
                        <td>
                            <p class="text-white font-medium">{{ $veterinaria->name }}</p>
                            <p class="text-sm text-gray-400">{{ $veterinaria->slug }}</p>
                        </td>

                        <td class="text-gray-300">
                            {{ ucfirst($veterinaria->plan) }}
                        </td>

                        <td>
                            <div class="flex flex-col gap-1">
                                <span
                                    class="px-2 py-1 rounded text-[10px] font-bold uppercase w-fit
                                    {{ $veterinaria->subscription_status->color() }}">
                                    {{ $veterinaria->subscription_status->label() }}
                                </span>

                            </div>
                        </td>

                        <td>
                            @if ($veterinaria->effective_end_date)
                                <span class="{{ $veterinaria->days_left_color }}">
                                    {{ $veterinaria->effective_end_date->format('d/m/y') }}
                                    -- {{ $veterinaria->days_left }}
                                </span>
                            @else
                                <span class="text-gray-500 text-xs">Sin fecha</span>
                            @endif
                        </td>

                        <td>
                            <div class="flex md:gap-4 gap-3 items-center">
                                @can('manage-veterinarias')
                                    <button wire:click="openPayments({{ $veterinaria->id }})"
                                        class="text-green-400 px-2 py-1 hover:text-green-300 transition-colors"
                                        title="Gestionar Pagos">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>

                                    <button wire:click="editVeterinaria({{ $veterinaria->id }})"
                                        class="text-blue-400 px-2  py-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>

                                    <button wire:click="confirmVeterinariaDeletion({{ $veterinaria->id }})"
                                        class="text-red-400 px-2  py-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $this->veterinarias->links() }}
        </div>
    </div>
    <x-confirmation-modal wire:model="confirmingVeterinariaDeletion" action="deleteVeterinaria">
        <x-slot name="title">
            Eliminar Veterinaria
        </x-slot>

        <x-slot name="content">
            ¿Estás seguro de que deseas eliminar esta veterinaria? Esta acción no se puede deshacer.
        </x-slot>
    </x-confirmation-modal>
</div>
