<?php

use Livewire\Component;
use App\Models\VeterinaryPayment;
use App\Models\Veterinary;
use App\Enums\SubscriptionStatus;
use Illuminate\Support\Facades\Gate;
use App\Models\PlanPrice;

new class extends Component {
    public Veterinary $veterinary;

    public $amount;
    public $payment_date;
    public $payment_method = 'Efectivo';
    public $notes;

    public bool $showForm = false;

    public function mount(Veterinary $veterinary)
    {
        $this->veterinary = $veterinary;
        $this->payment_date = now()->toDateString();
        $this->loadDefaultPrice();
    }

    public function loadDefaultPrice()
    {
        $planPrice = PlanPrice::where('plan', $this->veterinary->plan)->first();
        if ($planPrice) {
            if ($planPrice->plan == 'free' && $this->veterinary->subscription_status?->value == 'trial') {
                $this->amount = PlanPrice::where('plan', 'basic')->first()->price;
            } else {
                $this->amount = $planPrice->price;
            }
        }
    }

    public function save()
    {
        // Gate::authorize('manage-veterinary');

        $this->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        VeterinaryPayment::create([
            'veterinary_id' => $this->veterinary->id,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
        ]);

        switch ($this->veterinary->subscription_status) {
            case SubscriptionStatus::TRIAL:
                $baseDate = $this->veterinary->trial_ends_at ?? now();
                break;

            case SubscriptionStatus::ACTIVE:
                $baseDate = $this->veterinary->subscription_ends_at ?? now();
                break;

            case SubscriptionStatus::PAST_DUE:
                $baseDate = $this->veterinary->subscription_ends_at ?? now();
                break;

            case SubscriptionStatus::SUSPENDED:
            case SubscriptionStatus::CANCELLED:
            default:
                $baseDate = now();
                break;
        }

        if (in_array($this->veterinary->subscription_status, [SubscriptionStatus::TRIAL, SubscriptionStatus::ACTIVE]) && $baseDate?->isPast()) {
            $baseDate = now();
        }

        $this->veterinary->update([
            'subscription_ends_at' => $baseDate->copy()->addDays(30),
            'subscription_status' => SubscriptionStatus::ACTIVE,
        ]);

        $this->reset(['amount', 'notes', 'showForm']);
        $this->payment_date = now()->toDateString();

        $this->dispatch('payment-recorded');
        $this->dispatch('notify', message: 'Pago registrado correctamente', type: 'success');
    }

    public function deletePayment($id)
    {
        Gate::authorize('manage-veterinary');
        VeterinaryPayment::where('veterinary_id', $this->veterinary->id)->findOrFail($id)->delete();
    }
}; ?>

<div class="md:p-6 p-1">
    <div class="flex md:flex-row flex-col gap-3 justify-between md:items-center items-start mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Pagos de {{ $veterinary->name }}</h2>
            <p class="text-sm text-gray-400">Vence el: {{ $veterinary->subscription_ends_at?->format('d/m/Y') ?? 'S/D' }}
            </p>
        </div>
        <button wire:click="$toggle('showForm')"
            class="bg-indigo-600 hover:bg-indigo-700 text-white md:px-4 px-2 md:py-2 py-1 rounded-lg text-sm transition-colors">
            {{ $showForm ? 'Cancelar' : '+ Registrar Pago' }}
        </button>
    </div>

    @if ($showForm)
        <form wire:submit="save"
            class="bg-gray-900/50 md:p-4 p-2 rounded-xl border border-gray-700 mb-6 md:space-y-4 space-y-2">
            <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4 gap-2">

                <x-input label="Monto" model="amount" type="number" step="100" min="0" />

                <x-input label="Fecha de Pago" model="payment_date" type="date" />

                <x-select label="Método" model="payment_method">
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Mercado Pago">Mercado Pago</option>
                    <option value="Otro">Otro</option>
                </x-select>

                <x-input label="Notas (Opcional)" model="notes" />

            </div>
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white md:px-6 px-4 md:py-2 py-1 rounded-lg font-bold text-sm transition-colors">
                    Guardar y Extender Suscripción (30 días)
                </button>
            </div>
        </form>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-950 text-gray-400 text-xs uppercase">
                <tr class="[&>th]:md:py-3 [&>th]:py-2 [&>th]:md:px-4 [&>th]:px-3">
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Método</th>
                    <th>Notas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($veterinary->payments()->orderByDesc('payment_date')->get() as $payment)
                    <tr class="text-sm [&>td]:md:py-3 [&>td]:py-2 [&>td]:md:px-4 [&>td]:px-2">
                        <td class="text-white">{{ $payment->payment_date }}</td>
                        <td class="text-green-400 font-bold">
                            ${{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="text-gray-300">{{ $payment->payment_method }}</td>
                        <td class="text-gray-400 truncate max-w-[150px]">{{ $payment->notes }}</td>
                        <td class="text-center">
                            <button wire:click="deletePayment({{ $payment->id }})"
                                wire:confirm="¿Eliminar este registro de pago?"
                                class="text-red-500 hover:text-red-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay pagos registrados para
                            esta veterinaria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
