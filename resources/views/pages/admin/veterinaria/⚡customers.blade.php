<?php

use App\Livewire\Forms\CustomerForm;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

new #[Title('Gestión de Clientes')] class extends Component {
    use WithPagination;

    public string $search = '';

    public bool $showCreateForm = false;
    public bool $showEditForm = false;
    public bool $showModal = false;

    public bool $confirmingDeletion = false;

    public $customerIdDeletion = null;
    public ?Customer $customerToEdit = null;

    public bool $showPetModal = false;
    public bool $showMedicalRecordModal = false;
    public ?int $selectedCustomerId = null;

    #[Computed]
    public function customers()
    {
        return Customer::with('pets')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')->orWhereHas('pets', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->where('veterinary_id', Auth::user()->veterinary_id)
            ->orderByDesc('id')
            ->get();
    }

    public function toggleCreateForm(): void
    {
        if ($this->showCreateForm || $this->showEditForm) {
            $this->handleFormCancelled();
            return;
        }

        $this->customerIdDeletion = null;
        $this->showCreateForm = true;
        $this->showModal = true;
    }

    public function togglePetModal(int $customerId): void
    {
        if ($this->showPetModal) {
            $this->closePetModal();
            return;
        }

        $this->customerToEdit = Customer::find($customerId);
        $this->showPetModal = true;
    }

    public function toggleMedicalRecordModal(int $customerId): void
    {
        if ($this->showMedicalRecordModal) {
            $this->closeMedicalRecordModal();
            return;
        }

        $this->customerToEdit = Customer::find($customerId);
        $this->showMedicalRecordModal = true;
    }

    public function closeMedicalRecordModal(): void
    {
        $this->showMedicalRecordModal = false;
    }

    public function closePetModal(): void
    {
        $this->showPetModal = false;
    }

    public function confirmDeletion($id): void
    {
        $this->customerIdDeletion = $id;
        $this->confirmingDeletion = true;
    }

    public function editCustomer(int $id): void
    {
        $this->customerToEdit = Customer::findOrFail($id);
        $this->showEditForm = true;
        $this->showCreateForm = false;
        $this->showModal = true;
    }

    public function delete(): void
    {
        $customer = Customer::where('id', $this->customerIdDeletion)
            ->where('veterinary_id', Auth::user()->veterinary_id)
            ->firstOrFail();

        $customer->delete();

        $this->confirmingDeletion = false;
        $this->customerIdDeletion = null;
        $this->customerToEdit = null;

        $this->dispatch('notify', message: 'Cliente eliminado correctamente', type: 'success');
        $this->dispatch('customer-deleted');
    }

    #[On('customer-created')]
    #[On('customer-updated')]
    #[On('form-cancelled')]
    #[On('customer-deleted')]
    #[On('record-created')]
    public function handleFormCancelled(): void
    {
        $this->reset(['showCreateForm', 'showEditForm', 'showModal', 'customerIdDeletion', 'showPetModal', 'showMedicalRecordModal', 'selectedCustomerId', 'customerToEdit', 'confirmingDeletion']);
    }

    #[On('open-service-modal')]
    public function openServiceModal(int $customerId): void
    {
        $this->selectedCustomerId = $customerId;
        $this->showMedicalRecordModal = true;
    }
};

?>


<div class="space-y-6">
    <!-- Header -->

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="w-full">
            <h1 class="md:text-2xl text-xl font-semibold text-white">Gestión de Clientes</h1>
            <p class="text-gray-400 mt-1">Administra tus clientes y sus mascotas</p>
        </div>

        <div class="flex gap-4 w-full md:justify-end">

            <div class="relative ">
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="bg-gray-800 text-white rounded-lg md:pl-10 pl-8 md:pr-4 pr-1 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full "
                    placeholder="Buscar cliente">
                <div class="absolute md:left-3 left-2 top-2.5 text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <button wire:click="toggleCreateForm"
                class="md:px-6 px-2 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 cursor-pointer transition-colors text-nowrap w-fit ml-auto md:ml-0">
                + Nuevo
            </button>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-gray-800 rounded-lg overflow-x-auto  ">
        <table class="divide-y divide-gray-700 md:text-base text-sm min-w-full">
            <thead class="bg-gray-950 text-gray-300">
                <tr class="[&>th]:px-3 [&>th]:md:px-6 [&>th]:py-3 [&>th]:md:py-4 [&>th]:text-left">
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y  divide-gray-500">
                @forelse ($this->customers as $customer)
                    <tr wire:key="customer-{{ $customer->id }}"
                        class="[&>td]:px-2 [&>td]:md:px-6 [&>td]:py-2 [&>td]:md:py-4 divide-x divide-gray-600">
                        <td>
                            <p class="text-white  font-medium">{{ $customer->id }}</p>
                        </td>
                        <td>
                            <p class="text-white font-medium">{{ $customer->name }}</p>
                        </td>
                        <td>
                            <div class="text-sm text-gray-300">
                                @if ($customer->email)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{ $customer->email }}
                                    </div>
                                @endif
                                @if ($customer->phone)
                                    <div class="flex items-center gap-2 mt-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                            </path>
                                        </svg>
                                        {{ $customer->phone }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class=" text-gray-300">
                            {{ $customer->address ?: '-' }}
                        </td>
                        <td>
                            <div class=" flex flex-wrap gap-2 items-center">
                                <button wire:click="togglePetModal({{ $customer->id }})"
                                    class="text-green-400 px-3 py-1 border border-green-400 rounded-full hover:bg-green-400/10 transition-colors text-xs font-medium">
                                    Mascotas
                                </button>


                                <button wire:click="toggleMedicalRecordModal({{ $customer->id }})"
                                    class="text-indigo-400 px-3 py-1 border border-indigo-400 rounded-full hover:bg-indigo-400/10 transition-colors text-xs font-medium">
                                    Consulta
                                </button>

                                <button wire:click="editCustomer({{ $customer->id }})"
                                    class="text-blue-400 px-3 py-1 border border-blue-400 rounded-full hover:bg-blue-400/10 transition-colors text-xs font-medium">
                                    Editar
                                </button>

                                <button wire:click="confirmDeletion({{ $customer->id }})"
                                    class="text-red-400 px-3 py-1 border border-red-400 rounded-full hover:bg-red-400/10 transition-colors text-xs font-medium">
                                    Eliminar
                                </button>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            No se encontraron clientes.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-700">
        </div>
    </div>


    <div wire:key="form-modal">
        <x-modal show="showModal" wire:click="handleFormCancelled" maxWidth="max-w-xl">
            @if ($showEditForm && $customerToEdit)
                <livewire:forms.veterinaria.customer :customer="$customerToEdit" :key="'edit-' . $customerToEdit->id" />
            @elseif ($showCreateForm)
                <livewire:forms.veterinaria.customer />
            @endif
        </x-modal>
    </div>


    <!-- Pet Management Modal -->
    <div wire:key="pet-modal">
        <x-modal show="showPetModal" wire:model="showPetModal" maxWidth="max-w-6xl">
            @if ($showPetModal)
                <livewire:forms.veterinaria.pet :cliente="$customerToEdit" :key="'edit-' . $customerToEdit->id" />
            @endif
        </x-modal>
    </div>

    <!-- Service Record Modal -->
    <x-modal show="showMedicalRecordModal" wire:model="showMedicalRecordModal" maxWidth="max-w-4xl">
        @if ($showMedicalRecordModal)
            <livewire:forms.veterinaria.medical-record :customerId="$customerToEdit->id" :key="'new-medical-record-' . $selectedCustomerId" />
        @endif
    </x-modal>

    <!-- Confirmation Modal -->
    <div key="confirmingDeletion">
        <x-confirmation-modal wire:model="confirmingDeletion" action="delete">
            <x-slot name="title">
                Eliminar Cliente
            </x-slot>

            <x-slot name="content">
                ¿Estás seguro de que deseas eliminar este cliente? Se eliminarán también todos sus mascotas y registros
                clínicos. Esta acción no se puede deshacer.
            </x-slot>
        </x-confirmation-modal>
    </div>
</div>
