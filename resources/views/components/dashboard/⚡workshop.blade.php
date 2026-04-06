<?php

use App\Enums\SubscriptionStatus;
use App\Models\Veterinary;
use App\Models\MedicalRecord;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public $range = '30_days';
    public $startDate;
    public $endDate;
    public $range_label = 'Últimos 30 días';
    public bool $isPro = false;

    public function mount()
    {
        $user = auth()->user();
        if ($user->veterinary) {
            $user->veterinary->syncSubscriptionStatus();
        }

        $this->isPro = Auth::user()->veterinary->plan === 'pro' || Auth::user()->veterinary->plan === 'free';

        $this->resolveDates();
    }

    #[Computed]
    public function weeklyIncome(): float
    {
        return auth()
            ->user()
            ->veterinary->medicalRecords()
            ->where('performed_at', '>=', now()->startOfWeek())
            ->sum('price');
    }

    #[Computed]
    public function fortnightIncome()
    {
        $startOfFortnight = now()->day <= 15 ? now()->startOfMonth() : now()->startOfMonth()->addDays(15);
        return auth()->user()->veterinary->medicalRecords()->where('performed_at', '>=', $startOfFortnight)->sum('price');
    }

    #[Computed]
    public function monthlyIncome()
    {
        return auth()
            ->user()
            ->veterinary->medicalRecords()
            ->where('performed_at', '>=', now()->startOfMonth())
            ->sum('price');
    }

    protected function resolveDates(): void
    {
        if (!$this->isPro) {
            return;
        }

        switch ($this->range) {
            case '7_days':
                $this->startDate = now()->subDays(7);
                break;

            case '30_days':
                $this->startDate = now()->subDays(30);
                break;

            case 'this_month':
                $this->startDate = now()->startOfMonth();
                break;
            case 'this_year':
                $this->startDate = now()->startOfYear();
                break;
        }

        $this->endDate = now();
    }

    public function updatedRange()
    {
        if (!$this->isPro) {
            return;
        }

        $this->resolveDates();

        switch ($this->range) {
            case '7_days':
                $this->range_label = 'Últimos 7 días';
                break;

            case '30_days':
                $this->range_label = 'Últimos 30 días';
                break;

            case 'this_month':
                $this->range_label = 'Este mes';
                break;
            case 'this_year':
                $this->range_label = 'Este año';
                break;
        }

        $this->dispatch('update-chart', [
            'type' => $this->incomeByType(),
            'species' => $this->incomeBySpecies(),
        ]);
    }

    #[Computed]
    public function incomeByType()
    {
        if (!$this->isPro) {
            return;
        }

        $records = \App\Models\MedicalRecord::where('veterinary_id', auth()->user()->veterinary_id)
            ->whereBetween('performed_at', [$this->startDate, $this->endDate])
            ->where('price', '>', 0)
            ->selectRaw('veterinary_type_id, sum(price) as total')
            ->groupBy('veterinary_type_id')
            ->with('type')
            ->get();

        $labels = [];
        $data = [];
        $colors = ['#6366f1', '#a855f7', '#ec4899', '#3b82f6', '#14b8a6', '#f59e0b', '#ef4444', '#10b981', '#64748b'];

        foreach ($records as $record) {
            $labels[] = $record->type ? $record->type->name : 'General/Otro';
            $data[] = (float) $record->total;
        }

        if (empty($labels)) {
            return [
                'labels' => ['Sin Ingresos'],
                'data' => [0],
                'colors' => ['#374151'],
            ];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels)),
        ];
    }

    #[Computed]
    public function incomeBySpecies()
    {
        if (!$this->isPro) {
            return;
        }

        $records = MedicalRecord::with('pet.species')
            ->where('veterinary_id', auth()->user()->veterinary_id)
            ->where('price', '>', 0)
            ->whereBetween('performed_at', [$this->startDate, $this->endDate])
            ->get();

        $grouped = [];
        foreach ($records as $record) {
            $speciesName = 'Otro';
            if ($record->pet) {
                if ($record->pet->species) {
                    $speciesName = $record->pet->species->name;
                } elseif ($record->pet->specie_custom) {
                    $speciesName = $record->pet->specie_custom;
                }
            }

            if (!isset($grouped[$speciesName])) {
                $grouped[$speciesName] = 0;
            }
            $grouped[$speciesName] += (float) $record->price;
        }

        $labels = array_keys($grouped);
        $data = array_values($grouped);

        $colors = ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899', '#ef4444', '#6b7280'];

        if (empty($labels)) {
            return [
                'labels' => ['Sin Ingresos'],
                'data' => [0],
                'colors' => ['#374151'],
            ];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels)),
        ];
    }
};
?>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="md:text-2xl text-xl font-semibold text-white">¡Bienvenido, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-400 mt-1">Panel de control de {{ auth()->user()->veterinary->name }}</p>
        </div>

        @can('active-veterinaria')
            <div class="flex gap-3">
                <a href="{{ route('veterinaria.customers') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 md:py-2 py-1 rounded-lg text-sm font-medium transition-colors">
                    Nueva consulta
                </a>
            </div>
        @endcan
    </div>

    <!-- Resumen de Ingresos -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
            <svg class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Resumen de Ingresos
        </h3>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <x-dashboard.stat-card title="Esta Semana" :value="'$' . number_format($this->weeklyIncome(), 0, ',', '.')" color="indigo">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </x-slot>
            </x-dashboard.stat-card>

            <x-dashboard.stat-card title="Esta Quincena" :value="'$' . number_format($this->fortnightIncome(), 0, ',', '.')" color="purple">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </x-slot>
            </x-dashboard.stat-card>

            <x-dashboard.stat-card title="Este Mes" :value="'$' . number_format($this->monthlyIncome(), 0, ',', '.')" color="green">
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </x-slot>
            </x-dashboard.stat-card>
        </div>
    </div>

    <!-- Workshop Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <x-dashboard.stat-card title="Mis Clientes" :value="auth()->user()->veterinary->customers()->count()" color="blue" leftIcon>
            <x-slot name="icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card title="Mascotas Registradas" :value="auth()->user()->veterinary->pets()->count()" color="indigo" leftIcon>
            <x-slot name="icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1-1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1.0.01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card title="Consultas Realizadas" :value="auth()->user()->veterinary->medicalRecords()->count()" color="green" leftIcon>
            <x-slot name="icon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </x-slot>
        </x-dashboard.stat-card>
    </div>

    @php
        $veterinary = auth()->user()->veterinary;
    @endphp

    @if ($veterinary && $veterinary->subscription_status === SubscriptionStatus::CANCELLED)
        <div
            class="min-h-[60vh] flex flex-col items-center justify-center text-center p-8 bg-gray-900/50 rounded-3xl border-2 border-dashed border-red-500/30">
            <div class="p-6 bg-red-500/10 rounded-full mb-6 relative">
                <div class="absolute inset-0 bg-red-500/20 blur-2xl rounded-full"></div>
                <svg class="h-16 w-16 text-red-500 relative z-10" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-3xl font-black text-white mb-4 uppercase tracking-tighter">Cuenta Cancelada</h1>
            <p class="text-gray-400 max-w-lg mb-8 text-lg">
                Tu acceso al panel administrativo ha sido revocado permanentemente debido a la inactividad prolongada de
                la suscripción.
            </p>
            <div class="flex flex-col gap-4">
                <p class="text-red-400 font-bold bg-red-500/10 px-4 py-2 rounded-lg border border-red-500/20">
                    Las páginas públicas y el acceso de clientes han sido desactivados.
                </p>
                <div class="flex justify-center gap-4 mt-4">
                    <a href="mailto:soporte@cumbreit.com.ar"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-xl shadow-indigo-500/20 active:scale-95">
                        Contactar Soporte
                    </a>
                </div>
            </div>
        </div>
    @elseif ($veterinary && $veterinary->subscription_status === SubscriptionStatus::PAST_DUE)
        <div class="mb-6 bg-yellow-600/10 border border-yellow-500/50 rounded-xl md:p-4 p-2 flex items-center gap-4">
            <div class="p-2 bg-yellow-500/20 rounded-lg">
                <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-yellow-500">Suscripción Vencida</h3>
                <p class="text-sm text-yellow-200/90">
                    Tu cuenta venció el {{ $veterinary->subscription_ends_at?->format('d/m/Y') }}.
                    Por favor, regulariza tu pago para evitar la suspensión el dia
                    {{ $veterinary->subscription_ends_at?->addDays(7)->format('d/m/Y') }}.
                </p>
            </div>
        </div>
    @elseif ($veterinary && $veterinary->subscription_status === SubscriptionStatus::SUSPENDED)
        <div class="mb-6 bg-red-600/10 border border-red-500/50 rounded-xl p-4 flex items-center gap-4">
            <div class="p-2 bg-red-500/20 rounded-lg">
                <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-red-500">Suscripción Suspendida</h3>
                <p class="text-sm text-red-200/80">
                    Tu cuenta está suspendida.
                    Por favor, regulariza tu pago para reactivar el servicio.
                </p>
                <p class="text-sm text-red-200/80">No podras realizar ninguna acción, tus clientes aun veran su
                    historial, hasta que la cuenta sea cancelada en
                    {{ $veterinary->plan === 'free' ? $veterinary->trial_ends_at?->addDays(20)->format('d/m/Y') : $veterinary->subscription_ends_at?->addDays(20)->format('d/m/Y') }}.
                </p>

            </div>
        </div>
    @endif

    @if ($veterinary && $veterinary->subscription_status !== SubscriptionStatus::CANCELLED)

        @if ($isPro)
            <!-- Dashboard Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:mb-6 mb-4 mt-2">

                <div class="flex justify-start col-span-1 md:col-span-2">
                    <h3 class="md:text-lg text-sm font-semibold text-white flex items-center md:gap-2 gap-1">
                        <svg class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V7zM16 3v4M8 3v4M4 11h16" />
                        </svg>
                        Rango de dias
                    </h3>

                    <select name="range" id="range" wire:model.live="range"
                        class="bg-gray-800 text-white rounded-lg px-2 py-1 ml-2">
                        <option value="7_days">Últimos 7 días</option>
                        <option value="30_days">Últimos 30 días</option>
                        <option value="this_year">Este año</option>
                        <option value="this_month">Este mes</option>
                    </select>
                </div>

                <!-- Pie Chart -->
                <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-lg md:p-6 p-4">

                    <div class="flex justify-between items-center md:mb-4 mb-2">
                        <h3 class="text-sm font-semibold text-gray-300  flex justify-between items-center">
                            Ingresos por Tipo
                        </h3>

                        <span class="text-xs bg-indigo-500/20 text-indigo-400 px-2 py-1 rounded">
                            {{ $range_label }}
                        </span>
                    </div>


                    {{-- <x-charts.doughnut-chart :data="$this->incomeByType()" chart="type" /> --}}
                    <x-charts.bar-chart :data="$this->incomeByType()" chart="type" />


                </div>

                <!-- Pie Chart Species -->
                <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-lg md:p-6 p-4">
                    <div class="flex justify-between items-center md:mb-4 mb-2">
                        <h3 class="text-sm font-semibold text-gray-300  flex justify-between items-center">
                            Ingresos por Especies
                        </h3>
                        <span class="text-xs bg-indigo-500/20 text-indigo-400 px-2 py-1 rounded">
                            {{ $range_label }}
                        </span>

                    </div>


                    <x-charts.doughnut-chart :data="$this->incomeBySpecies()" chart="species" />
                </div>
            </div>
        @endif

        <!-- Recent Activity & Subscription -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Services -->
            <div class="lg:col-span-2 bg-gray-800 rounded-xl border border-gray-700 shadow-lg overflow-hidden">
                <div class="md:p-6 px-3 py-2  border-b border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-white">Últimas consultas</h3>
                    @can('active-veterinaria')
                        <a href="{{ route('veterinaria.records') }}"
                            class="text-sm text-indigo-400 hover:text-indigo-300">Ver
                            historial</a>
                    @endcan
                </div>
                <div class="p-0">
                    @if (auth()->user()->veterinary->medicalRecords->isNotEmpty())
                        <ul class="divide-y divide-gray-700">
                            @foreach (auth()->user()->veterinary->medicalRecords()->with(['customer', 'pet'])->latest()->take(5)->get() as $record)
                                <li class="md:p-4 p-2 hover:bg-gray-700/30 transition-colors">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-white font-medium text-sm">
                                                {{ $record->customer->name ?? 'S/D' }}</p>
                                            <p class="text-gray-400 text-xs mt-0.5">
                                                {{ $record->pet->name }} - {{ $record->pet?->species?->name }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-indigo-400 font-bold text-sm">
                                                ${{ number_format($record->price, 0, ',', '.') }}</p>
                                            <p class="text-gray-500 text-[10px] mt-0.5">
                                                {{ $record->performed_at->format('d/m/y') }}</p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-10 text-center">
                            <p class="text-gray-500 text-sm">Aún no has registrado ningúna consulta.</p>
                        </div>
                    @endif
                </div>
            </div>

            @php
                $veterinary = auth()->user()->veterinary;
                $daysLeft = $veterinary?->daysLeft();
                // $daysLeft = 5;
                $expiringSoon = $daysLeft !== null && $daysLeft <= 3 && $daysLeft >= 0;
                // $expiringSoon = 1;
            @endphp


            <!-- Subscription & Plan -->
            <div
                class="bg-gray-800 rounded-xl border  shadow-lg md:p-6 p-4 flex flex-col justify-between {{ $expiringSoon ? 'border-yellow-500/60' : 'border-gray-700' }}">
                <div>
                    <h3 class="text-lg font-semibold text-white md:mb-4 mb-2">Tu Plan</h3>
                    <div class="bg-gray-900 rounded-lg md:p-4 p-3 border border-gray-700 md:mb-4 mb-2">
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">Plan Actual</p>
                        <p class="text-xl font-bold text-white mt-1">{{ ucfirst($veterinary->plan) }}
                        </p>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-400">Estado</span>
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $veterinary->subscription_status->color() }}">
                                {{ $veterinary->subscription_status->label() }}
                            </span>
                        </div>


                        @if ($expiringSoon)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-yellow-400 font-medium">
                                    ⚠ Vence pronto
                                </span>
                                <span class="text-yellow-300 font-semibold">
                                    Faltan {{ $daysLeft }} {{ Str::plural('día', $daysLeft) }}
                                </span>
                            </div>
                        @endif


                        @if ($veterinary->plan == 'free' && $veterinary->trial_ends_at)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400">Prueba termina</span>
                                <span class="text-white">{{ $veterinary->trial_ends_at->format('d/m/Y') }}</span>
                            </div>
                        @elseif(auth()->user()->veterinary->plan == 'basic' || auth()->user()->veterinary->plan == 'pro')
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400">Subscripción finaliza</span>
                                <span
                                    class="text-white">{{ $veterinary->subscription_ends_at->format('d/m/Y') }}</span>
                            </div>
                        @endif

                    </div>
                </div>

                <div class="mt-8">
                    <p class="text-xs text-gray-500 text-center mb-4">¿Necesitas más funciones?</p>
                    <button
                        class="w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 rounded-xl transition-colors text-sm">
                        Mejorar Plan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
