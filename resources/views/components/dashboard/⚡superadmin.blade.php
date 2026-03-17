<?php

use App\Enums\SubscriptionStatus;
use App\Models\VeterinaryPayment;
use App\Models\Veterinary;
use App\Models\MedicalRecord;
use App\Models\Pet;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public function mount()
    {
        Veterinary::whereIn('subscription_status', [SubscriptionStatus::TRIAL, SubscriptionStatus::ACTIVE, SubscriptionStatus::PAST_DUE])
            ->get()
            ->each->syncSubscriptionStatus();
    }

    #[Computed]
    public function totalPayments()
    {
        return VeterinaryPayment::sum('amount');
    }

    #[Computed]
    public function pastDueVeterinariasCount()
    {
        return Veterinary::where('subscription_status', SubscriptionStatus::PAST_DUE)->count();
    }

    #[Computed]
    public function suspendedVeterinariasCount()
    {
        return Veterinary::where('subscription_status', SubscriptionStatus::SUSPENDED)->count();
    }
};
?>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-white">Dashboard Global</h1>
            <p class="text-gray-400 mt-1">Resumen del sistema</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Veterinarias -->
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-500/10 rounded-lg">
                    <svg class="h-6 w-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400 font-medium">Total Veterinarias</p>
                    <p class="text-2xl font-bold text-white">{{ Veterinary::count() }}</p>
                </div>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-500/10 rounded-lg">
                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400 font-medium">Activos</p>
                    <p class="text-2xl font-bold text-white">
                        {{ Veterinary::where('subscription_status', SubscriptionStatus::ACTIVE)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Past Due Veterinarias -->
        <div class="bg-gray-800 p-6 rounded-xl border border-yellow-500/20 shadow-lg">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-yellow-500/10 rounded-lg">
                    <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400 font-medium">Vencidos</p>
                    <p class="text-2xl font-bold text-white">{{ $this->pastDueVeterinariasCount() }}</p>
                </div>
            </div>
        </div>

        <!-- Suspended Veterinarias -->
        <div class="bg-gray-800 p-6 rounded-xl border border-red-500/20 shadow-lg">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-500/10 rounded-lg">
                    <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400 font-medium">Suspendidos</p>
                    <p class="text-2xl font-bold text-white">{{ $this->suspendedVeterinariasCount() }}</p>
                </div>
            </div>
        </div>

        <!-- Total Mascotas -->
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-500/10 rounded-lg">
                    <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1-1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400 font-medium">Mascotas Totales</p>
                    <p class="text-2xl font-bold text-white">{{ Pet::count() }}</p>

                </div>
            </div>
        </div>

        <!-- Total Consultas -->
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-500/10 rounded-lg">
                    <svg class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400 font-medium">Consultas Totales</p>
                    <p class="text-2xl font-bold text-white">{{ MedicalRecord::count() }}</p>
                </div>
            </div>
        </div>

        <!-- Total Income (Payments) -->
        <div class="bg-gray-800 p-6 rounded-xl border border-green-500/20 shadow-lg">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-500/10 rounded-lg">
                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400 font-medium">Recaudación</p>
                    <p class="text-2xl font-bold text-white">
                        ${{ number_format($this->totalPayments(), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Veterinarias -->
    <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-white">Veterinarias Registradas Recientemente</h3>
            <a href="{{ route('admin.veterinarias') }}" class="text-sm text-indigo-400 hover:text-indigo-300">Ver
                todos</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-900/50 text-gray-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-medium">Veterinaria</th>
                        <th class="px-6 py-4 font-medium">Plan</th>
                        <th class="px-6 py-4 font-medium">Estado</th>
                        <th class="px-6 py-4 font-medium">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach (Veterinary::latest()->take(5)->get() as $veterinary)
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-8 w-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold text-xs">
                                        {{ substr($veterinary->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-medium text-sm">{{ $veterinary->name }}</p>
                                        <p class="text-gray-500 text-xs">{{ $veterinary->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-300">
                                {{ ucfirst($veterinary->plan) }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-full text-[10px] font-bold uppercase {{ $veterinary->subscription_status->color() }}">
                                    {{ $veterinary->subscription_status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $veterinary->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
