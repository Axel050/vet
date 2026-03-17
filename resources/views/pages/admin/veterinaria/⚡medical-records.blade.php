<?php

use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Historial Médico')] class extends Component {
    use WithPagination;

    public string $search = '';

    public string $fromDate = '';

    public string $toDate = '';

    public bool $showModal = false;

    public bool $showEditForm = false;

    public bool $showCreateForm = false;

    public bool $isPro = false;

    public bool $confirmingDeletion = false;

    public $recordIdDeletion = null;

    public ?MedicalRecord $recordToEdit = null;

    public function mount()
    {
        $this->isPro = Auth::user()->veterinary->plan === 'pro';

        $this->fromDate = now()->subDays(30)->toDateString();
        $this->toDate = now()->toDateString();
    }

    public function showProNotification()
    {
        $this->dispatch('notify', message: 'El filtrado por fecha es una función exclusiva para usuarios Pro', type: 'info');
    }

    public function updatedFromDate()
    {
        if (!$this->isPro) {
            $this->fromDate = now()->subDays(30)->toDateString();
            $this->showProNotification();
        }
    }

    public function updatedToDate()
    {
        if (!$this->isPro) {
            $this->toDate = now()->toDateString();
            $this->showProNotification();
        }
    }

    #[Computed]
    public function records()
    {
        return MedicalRecord::with(['customer', 'pet.breed', 'type'])
            ->where('veterinary_id', Auth::user()->veterinary_id)

            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('customer', function ($q2) {
                        $q2->where('name', 'like', '%' . $this->search . '%');
                    })

                        ->orWhereHas('pet', function ($q2) {
                            $q2->where('name', 'like', '%' . $this->search . '%');
                        })

                        ->orWhere('custom_type_name', 'like', '%' . $this->search . '%')

                        ->orWhereHas('type', function ($q2) {
                            $q2->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })

            ->when($this->fromDate, function ($q) {
                if (!$this->isPro) {
                    return $q->whereDate('performed_at', '>=', now()->subDays(30));
                }

                return $q->whereDate('performed_at', '>=', $this->fromDate);
            })

            ->when($this->toDate, function ($q) {
                if (!$this->isPro) {
                    return $q->whereDate('performed_at', '<=', now());
                }

                return $q->whereDate('performed_at', '<=', $this->toDate);
            })

            ->orderByDesc('performed_at')
            ->orderByDesc('id')
            ->paginate(15);
    }

    #[Computed]
    public function totalPrice()
    {
        return MedicalRecord::where('veterinary_id', Auth::user()->veterinary_id)
            ->when($this->search, function ($query) {
                $query
                    ->whereHas('customer', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('pet', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('custom_type_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('type', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->fromDate, function ($q) {
                return $q->whereDate('performed_at', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($q) {
                return $q->whereDate('performed_at', '<=', $this->toDate);
            })
            ->sum('price');
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

    public function editRecord(int $id): void
    {
        $this->recordToEdit = MedicalRecord::with(['customer', 'pet'])
            ->where('veterinary_id', Auth::user()->veterinary_id)
            ->findOrFail($id);
        $this->showEditForm = true;
        $this->showModal = true;
    }

    public function confirmDeletion($id): void
    {
        $this->recordIdDeletion = $id;
        $this->confirmingDeletion = true;
    }

    public function delete(): void
    {
        $record = MedicalRecord::where('id', $this->recordIdDeletion)
            ->where('veterinary_id', Auth::user()->veterinary_id)
            ->firstOrFail();

        $record->delete();

        $this->confirmingDeletion = false;
        $this->recordIdDeletion = null;

        $this->dispatch('notify', message: 'Registro eliminado correctamente', type: 'success');
        $this->dispatch('record-deleted');
    }

    #[On('record-created')]
    #[On('record-updated')]
    #[On('form-cancelled')]
    #[On('record-deleted')]
    public function handleFormCancelled(): void
    {
        $this->reset(['showCreateForm', 'showEditForm', 'showModal', 'recordIdDeletion', 'recordToEdit']);
    }

    public function clearFilters(): void
    {
        $this->search = '';
        if ($this->isPro) {
            $this->fromDate = now()->startOfMonth()->toDateString();
            $this->toDate = now()->toDateString();
        } else {
            $this->fromDate = now()->subDays(30)->toDateString();
            $this->toDate = now()->toDateString();
        }
    }
};

?>

<div class="space-y-6">
    <!-- Header -->


    <div class="flex flex-col gap-6">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

            <div class="md:w-fit pr-3">
                <h1 class="md:text-2xl text-xl font-semibold text-white">Historial Médico</h1>
                <p class="text-gray-400 mt-1 text-nowrap">Registros de todas las consultas realizadas</p>
            </div>

            <div class="flex flex-wrap gap-4 w-full md:justify-end">
                <!-- Filtros de Fecha -->
                <div
                    class="flex flex-wrap items-center md:gap-4 gap-1 bg-gray-800 rounded-lg md:px-3 px-1.5 py-1 border border-gray-700  justify-center">

                    <div class="flex items-center gap-2 w-fit relative ">
                        <span class="text-xs text-gray-300 uppercase md:font-bold font-normal">Desde</span>


                        <div class="relative w-fit">
                            <input wire:model.live="fromDate" type="date" id="fromDate"
                                @if (!$isPro) readonly wire:click="showProNotification" @endif
                                class="bg-transparen text-white text-sm focus:outline-none border-none p-1  w-fit no-calendar-icon {{ !$isPro ? 'cursor-pointer' : '' }}">


                            <button type="button"
                                @if ($isPro) onclick="document.getElementById('fromDate').showPicker()" 
                                @else wire:click="showProNotification" @endif
                                class="absolute right-1 top-1/2 -translate-y-1/2 text-white/70 hover:text-white">
                                <svg class="pointer-events-none  w-4 h-4 text-white/70"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </button>

                            @if (!$isPro)
                                <div
                                    class="absolute -top-6 left-0 bg-indigo-600 text-[10px] px-1.5 py-0.5 rounded text-white font-bold tracking-tighter uppercase whitespace-nowrap">
                                    Solo Pro
                                </div>
                            @endif

                        </div>
                    </div>


                    <div class="flex items-center gap-2 w-fit relative">
                        <span class="text-xs text-gray-300 uppercase md:font-bold font-normal">Hasta</span>

                        <div class="relative w-fit">
                            <input wire:model.live="toDate" type="date" id="toDate"
                                @if (!$isPro) readonly wire:click="showProNotification" @endif
                                class="bg-transparent text-white text-sm focus:outline-none border-none p-1 w-fit no-calendar-icon {{ !$isPro ? 'cursor-pointer' : '' }}">
                            <button type="button"
                                @if ($isPro) onclick="document.getElementById('toDate').showPicker()" 
                                @else wire:click="showProNotification" @endif
                                class="absolute right-1 top-1/2 -translate-y-1/2 text-white/70 hover:text-white">
                                <svg class="pointer-events-none  w-4 h-4 text-white/70"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>

                            </button>
                        </div>
                    </div>


                </div>

                <div class="flex md:gap-4 gap-2">
                    <div class="relative grow">
                        <input wire:model.live.debounce.300ms="search" type="text"
                            class="bg-gray-800 text-white rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full"
                            placeholder="Buscar en historial...">
                        <div class="absolute left-3 top-2.5 text-gray-400 ">
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
        </div>

        <!-- Resumen del Filtro -->
        <div
            class="bg-indigo-500/10 border border-indigo-500/20 rounded-lg md:p-4 p-2 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-indigo-500/20 rounded-lg text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-indigo-300 font-bold uppercase tracking-wider">Total del Periodo</p>
                    <p class="md:text-xl text-lg font-bold text-white">
                        ${{ number_format($this->totalPrice, 0, ',', '.') }}</p>
                </div>
            </div>
            @if ($fromDate || $toDate || $search)
                <button wire:click="clearFilters"
                    class="md:text-sm text-xs text-gray-400 hover:text-white transition-colors underline">
                    Limpiar filtros
                </button>
            @endif
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-gray-800 rounded-lg overflow-x-auto border border-gray-700">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-950 text-gray-300">
                <tr
                    class="[&>th]:px-3 [&>th]:md:px-6 [&>th]:py-3 [&>th]:md:py-4 [&>th]:text-left text-xs font-medium uppercase tracking-wider">
                    <th>Fecha</th>
                    <th>Cliente / Mascota</th>
                    <th>Servicio / Peso</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-700">
                @forelse ($this->records as $record)
                    <tr wire:key="record-{{ $record->id }}"
                        class="[&>td]:px-2 [&>td]:md:px-6 [&>td]:py-2 [&>td]:md:py-4 divide-x divide-gray-600">
                        <td>
                            <p class="text-white md:text-base  text-xs ">
                                {{ $record->performed_at->format('d/m/y') }}
                            </p>
                        </td>
                        <td>
                            <p class="text-white font-medium">{{ $record->customer?->name }}</p>
                            <p class="text-xs text-gray-400">{{ $record->pet?->name }} -
                                {{ $record->pet?->species_name }} - {{ $record->pet?->breed_name }}</p>
                        </td>
                        <td>
                            <p class="text-white">
                                {{ $record->type ? $record->type->name : $record->custom_type_name }}</p>
                            @if ($record->weight)
                                <p class="text-xs text-gray-400">{{ number_format($record->weight, 2, ',', '.') }}
                                    kg
                                </p>
                            @endif
                        </td>
                        <td class="font-semibold text-green-400">
                            ${{ number_format($record->price, 0, ',', '.') }}
                        </td>
                        <td>
                            <div class="flex gap-5 text-gray-400 items-center">
                                <button wire:click="editRecord({{ $record->id }})"
                                    class="hover:text-blue-400 transition-colors text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>

                                <button wire:click="confirmDeletion({{ $record->id }})"
                                    class="hover:text-red-400 transition-colors text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            No se encontraron registros médicos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($this->records->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $this->records->links() }}
            </div>
        @endif
    </div>

    <!-- Edit/Create Modal -->
    <x-modal show="showModal" wire:click="handleFormCancelled" maxWidth="max-w-4xl">
        @if ($showEditForm && $recordToEdit)
            <livewire:forms.veterinaria.medical-record :record="$recordToEdit" :key="'edit-record-' . $recordToEdit->id" />
        @elseif ($showCreateForm)
            <livewire:forms.veterinaria.medical-record />
        @endif
    </x-modal>

    <!-- Confirmation Modal -->
    <x-confirmation-modal wire:model="confirmingDeletion" action="delete">
        <x-slot name="title">
            Eliminar Registro Médico
        </x-slot>

        <x-slot name="content">
            ¿Estás seguro de que deseas eliminar este registro médico? Esta acción no se puede deshacer.
        </x-slot>
    </x-confirmation-modal>
</div>
