<?php

use Livewire\Component;
use App\Models\Species;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Gate;
use App\Models\Pet;
use App\Models\SpeciesRequest;

new #[Title('Gestión de Especies')] class extends Component {
    public bool $showCreateForm = false;
    public bool $showEditForm = false;
    public bool $showModal = false;

    public string $search = '';
    public string $activeTab = 'all'; // all | pending

    public bool $confirmingEspecieDeletion = false;
    public $especieIdDeletion = null;

    public ?Species $especieToEdit = null;

    #[Computed]
    public function especies()
    {
        return Species::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
            ->latest()
            ->paginate(20);
    }

    #[Computed]
    public function pendingRequests()
    {
        return \App\Models\SpeciesRequest::with(['veterinary', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->get();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function approveRequest(int $id): void
    {
        Gate::authorize('manage-veterinarias');

        $request = SpeciesRequest::findOrFail($id);

        // 1. Create the official Species
        $species = Species::firstOrCreate(['name' => $request->name]);

        // 2. Update all pets that were using this custom species name
        Pet::where('veterinary_id', $request->veterinary_id)
            ->where('specie_custom', $request->name)
            ->where(function ($query) use ($request) {
                $query->where('species_id', $request->species_id)->orWhere('specie_custom', $request->custom_species);
            })
            ->update([
                'species_id' => $species->id,
                'specie_custom' => null,
            ]);

        // 3. Mark request as approved
        $request->update(['status' => 'approved']);

        $this->dispatch('notify', message: 'Especie aprobada y actualizada en las mascotas.', type: 'success');
    }

    public function rejectRequest(int $id): void
    {
        Gate::authorize('manage-veterinarias');

        $request = \App\Models\SpeciesRequest::findOrFail($id);
        $request->update(['status' => 'rejected']);

        $this->dispatch('notify', message: 'Solicitud rechazada.', type: 'info');
    }

    public function toggleCreateForm(): void
    {
        Gate::authorize('manage-veterinarias');

        if ($this->showCreateForm || $this->showEditForm) {
            $this->handleFormCancelled();
            return;
        }

        $this->especieToEdit = null;
        $this->showCreateForm = true;
        $this->showModal = true;
    }

    public function editEspecie(int $id): void
    {
        Gate::authorize('manage-veterinarias');

        $this->especieToEdit = Species::findOrFail($id);
        $this->showEditForm = true;
        $this->showCreateForm = false;
        $this->showModal = true;
    }

    public function confirmEspecieDeletion(int $id): void
    {
        Gate::authorize('manage-veterinarias');

        $this->especieIdDeletion = $id;
        $this->confirmingEspecieDeletion = true;
    }

    public function deleteEspecie(): void
    {
        Gate::authorize('manage-veterinarias');

        Species::findOrFail($this->especieIdDeletion)->delete();

        $this->confirmingEspecieDeletion = false;
        $this->especieIdDeletion = null;
        $this->dispatch('especie-deleted');
        $this->dispatch('notify', message: 'Especie eliminada correctamente', type: 'success');
    }

    #[On('especie-created')]
    #[On('especie-updated')]
    #[On('form-cancelled')]
    public function handleFormCancelled(): void
    {
        $this->reset(['showCreateForm', 'showEditForm', 'showModal', 'especieToEdit']);
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
            <h1 class="md:text-2xl text-xl font-semibold text-white">Gestión de Especies</h1>
            <p class="text-gray-400 mt-1">Administración de especies animales (Perro, Gato, etc.)</p>
        </div>

        <div class="flex gap-4 w-full justify-end">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="bg-gray-800 text-white rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full"
                    placeholder="Buscar especies...">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            @can('manage-veterinarias')
                <button wire:click="toggleCreateForm"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 cursor-pointer transition-colors text-nowrap">
                    + Crear
                </button>
            @endcan
        </div>
    </div>

    <!-- Modal -->
    <x-modal show="showModal" wire:click="handleFormCancelled" maxWidth="max-w-xl">
        @if ($showEditForm && $especieToEdit)
            <livewire:forms.specie :especie="$especieToEdit" :key="'edit-' . $especieToEdit->id" />
        @elseif ($showCreateForm)
            <livewire:forms.specie />
        @endif
    </x-modal>

    <!-- Tabs -->
    <div class="flex border-b border-gray-700 mb-6">
        <button wire:click="setTab('all')"
            class="px-6 py-3 text-sm font-medium transition-colors relative {{ $activeTab === 'all' ? 'text-indigo-400 border-b-2 border-indigo-400' : 'text-gray-400 hover:text-white' }}">
            Todas las Especies
        </button>
        <button wire:click="setTab('pending')"
            class="px-6 py-3 text-sm font-medium transition-colors relative {{ $activeTab === 'pending' ? 'text-indigo-400 border-b-2 border-indigo-400' : 'text-gray-400 hover:text-white' }}">
            Solicitudes Pendientes
            @if ($this->pendingRequests->count() > 0)
                <span class="absolute top-2 right-0 flex h-2 w-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                </span>
            @endif
        </button>
    </div>

    @if ($activeTab === 'all')
        <!-- Tabla Especies -->
        <div class="bg-gray-800 rounded-lg overflow-hidden border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-950 text-gray-300">
                    <tr class="[&>th]:px-3 [&>th]:md:px-6 [&>th]:py-3 [&>th]:md:py-4 [&>th]:text-left">
                        <th>Especie</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-700">
                    @foreach ($this->especies as $especie)
                        <tr wire:key="especie-{{ $especie->id }}"
                            class="hover:bg-gray-750 transition-colors [&>td]:px-2 [&>td]:md:px-6 [&>td]:py-2 [&>td]:md:py-4">

                            <td>
                                <p class="text-white font-medium">{{ $especie->name }}</p>
                            </td>

                            <td>
                                <div class="flex gap-4 items-center">
                                    @can('manage-veterinarias')
                                        <button wire:click="editEspecie({{ $especie->id }})"
                                            class="text-blue-400 hover:text-blue-300 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>

                                        <button wire:click="confirmEspecieDeletion({{ $especie->id }})"
                                            class="text-red-400 hover:text-red-300 transition-colors">
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
            @if ($this->especies->hasPages())
                <div class="px-6 py-4 border-t border-gray-700 bg-gray-900/50">
                    {{ $this->especies->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Solicitudes Pendientes -->
        <div class="bg-gray-800 rounded-lg overflow-hidden border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-950 text-gray-300">
                    <tr class="[&>th]:px-3 [&>th]:md:px-6 [&>th]:py-3 [&>th]:md:py-4 [&>th]:text-left">
                        <th>Nombre Sugerido</th>
                        <th>Clínica</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-700">
                    @forelse ($this->pendingRequests as $request)
                        <tr wire:key="request-{{ $request->id }}"
                            class="hover:bg-gray-750 transition-colors [&>td]:px-2 [&>td]:md:px-6 [&>td]:py-2 [&>td]:md:py-4">
                            <td>
                                <span class="text-white font-semibold">{{ $request->name }}</span>
                            </td>
                            <td>
                                <span class="text-gray-300">{{ $request->veterinary?->name }}</span>
                            </td>
                            <td>
                                <span class="text-gray-300">{{ $request->user?->name }}</span>
                            </td>
                            <td>
                                <span
                                    class="text-gray-400 text-sm">{{ $request->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <button wire:click="approveRequest({{ $request->id }})"
                                        class="px-3 py-1 bg-green-600/20 text-green-400 hover:bg-green-600/30 rounded border border-green-600/30 transition-colors text-xs font-bold uppercase tracking-wider">
                                        Aprobar
                                    </button>
                                    <button wire:confirm="¿Seguro que deseas rechazar esta solicitud?"
                                        wire:click="rejectRequest({{ $request->id }})"
                                        class="px-3 py-1 bg-red-600/20 text-red-400 hover:bg-red-600/30 rounded border border-red-600/30 transition-colors text-xs font-bold uppercase tracking-wider">
                                        Rechazar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <p>No hay solicitudes pendientes.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif


    <x-confirmation-modal wire:model="confirmingEspecieDeletion" action="deleteEspecie">
        <x-slot name="title">
            Eliminar Especie
        </x-slot>

        <x-slot name="content">
            ¿Estás seguro de que deseas eliminar esta especie?
        </x-slot>
    </x-confirmation-modal>

</div>
