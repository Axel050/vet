<?php

use App\Models\VeterinaryType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

new #[Title('Catálogo de Servicios')] class extends Component {
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;
    public bool $showEditForm = false;
    public bool $showCreateForm = false;

    public bool $confirmingDeletion = false;
    public $typeIdDeletion = null;
    public ?VeterinaryType $typeToEdit = null;

    #[Computed]
    public function types()
    {
        return VeterinaryType::where('veterinary_id', Auth::user()->veterinary_id)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);
    }

    public function toggleCreateForm(): void
    {
        if ($this->showCreateForm || $this->showEditForm) {
            $this->handleFormCancelled();
            return;
        }

        $this->showCreateForm = true;
        $this->showModal = true;
    }

    public function editType(int $id): void
    {
        $this->typeToEdit = VeterinaryType::where('veterinary_id', Auth::user()->veterinary_id)->findOrFail($id);
        $this->showEditForm = true;
        $this->showModal = true;
    }

    public function confirmDeletion($id): void
    {
        $this->typeIdDeletion = $id;
        $this->confirmingDeletion = true;
    }

    public function delete(): void
    {
        $type = VeterinaryType::where('id', $this->typeIdDeletion)
            ->where('veterinary_id', Auth::user()->veterinary_id)
            ->firstOrFail();

        $hasRecords = $type->medicalRecords()->withTrashed()->exists();

        if ($hasRecords) {
            $type->update([
                'is_active' => false,
            ]);

            $message = 'El tipo fue desactivado porque tiene historial asociado';
            $event = 'type-deactivated';
        } else {
            $type->delete();

            $message = 'Tipo eliminado correctamente';
            $event = 'type-deleted';
        }

        $this->confirmingDeletion = false;
        $this->typeIdDeletion = null;

        $this->dispatch('notify', message: $message, type: 'success');
        $this->dispatch($event);
    }

    public function delete2(): void
    {
        $type = VeterinaryType::where('id', $this->typeIdDeletion)
            ->where('veterinary_id', Auth::user()->veterinary_id)
            ->firstOrFail();

        $type->delete();

        $this->confirmingDeletion = false;
        $this->typeIdDeletion = null;

        $this->dispatch('notify', message: 'Tipo eliminado correctamente', type: 'success');
        $this->dispatch('type-deleted');
    }

    #[On('type-created')]
    #[On('type-updated')]
    #[On('form-cancelled')]
    #[On('type-deleted')]
    public function handleFormCancelled(): void
    {
        $this->reset(['showCreateForm', 'showEditForm', 'showModal', 'typeIdDeletion', 'typeToEdit']);
    }
};

?>

<div class="space-y-6">
    <!-- Header -->


    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="w-full">
            <h1 class="md:text-2xl text-xl font-semibold text-white">Catálogo de Tipos</h1>
            <p class="text-gray-400 mt-1">Define los tipos de consultas que ofrece tu veterinaria</p>
        </div>

        <div class="flex gap-4 w-full md:justify-end">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="bg-gray-800 text-white rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full"
                    placeholder="Buscar tipos...">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <button wire:click="toggleCreateForm"
                class="md:px-6 px-2 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 cursor-pointer transition-colors text-nowrap">
                + Nuevo
            </button>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-gray-800 rounded-lg  border border-gray-700 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-950 text-gray-300">
                <tr
                    class="[&>th]:px-3 [&>th]:md:px-6 [&>th]:py-3 [&>th]:md:py-4 [&>th]:text-left text-xs font-medium uppercase tracking-wider">
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-700">
                @forelse ($this->types as $type)
                    <tr wire:key="type-{{ $type->id }}"
                        class="[&>td]:px-2 [&>td]:md:px-6 [&>td]:py-2 [&>td]:md:py-4 divide-x divide-gray-600">
                        <td>
                            <p class="text-white md:font-medium">{{ $type->name }}</p>
                        </td>
                        <td>
                            <p class="text-sm text-gray-400 max-w-xs truncate">{{ $type->description ?: '-' }}</p>
                        </td>
                        <td>
                            @if ($type->is_active)
                                <span
                                    class="px-2 font-semibold rounded-full bg-green-400/10 text-green-400 border border-green-400/20">
                                    Activo
                                </span>
                            @else
                                <span
                                    class="px-2 font-semibold rounded-full bg-red-400/10 text-red-400 border border-red-400/20">
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class=" flex gap-4">
                            <button wire:click="editType({{ $type->id }})"
                                class="text-blue-400 hover:text-blue-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>

                            <button wire:click="confirmDeletion({{ $type->id }})"
                                class="text-red-400 hover:text-red-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            No se encontraron tipos en el catálogo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($this->types->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $this->types->links() }}
            </div>
        @endif
    </div>

    <!-- Edit/Create Modal -->
    <x-modal show="showModal" wire:click="handleFormCancelled" maxWidth="max-w-xl">
        @if ($showEditForm && $typeToEdit)
            <livewire:forms.veterinaria.type :type="$typeToEdit" :key="'edit-' . $typeToEdit->id" />
        @elseif ($showCreateForm)
            <livewire:forms.veterinaria.type />
        @endif
    </x-modal>

    <!-- Confirmation Modal -->
    <x-confirmation-modal wire:model="confirmingDeletion" action="delete">
        <x-slot name="title">
            Eliminar Tipo de consulta del Catálogo
        </x-slot>

        <x-slot name="content">
            ¿Estás seguro de que deseas eliminar este tipo de consulta del catálogo? Si el tipo ya se encuentra en uso
            en
            algún registro, no se podrá eliminar, solo se desactivará.
        </x-slot>
    </x-confirmation-modal>
</div>
