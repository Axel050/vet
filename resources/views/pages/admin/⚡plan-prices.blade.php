<?php

use App\Models\PlanPrice;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Precios de Planes')] class extends Component {
    public $prices = [];

    public function mount()
    {
        $this->loadPrices();
    }

    public function loadPrices()
    {
        $this->prices = PlanPrice::all()->pluck('price', 'plan')->toArray();
    }

    public function save()
    {
        foreach ($this->prices as $plan => $price) {
            PlanPrice::updateOrCreate(['plan' => $plan], ['price' => $price]);
        }

        $this->dispatch('notify', message: 'Precios actualizados correctamente', type: 'success');
    }
}; ?>

<div class="md:space-y-6 space-y-3">
    <div class="w-full">
        <h1 class="md:text-2xl text-xl font-semibold text-white">Configuración de Precios</h1>
        <p class="text-gray-400 mt-1">Precios mensuales sugeridos para cada plan</p>
    </div>

    <div class="max-w-2xl bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <form wire:submit="save">
            <div class="md:p-6 p-3 md:space-y-6 space-y-3">
                @foreach ($prices as $plan => $price)
                    <div
                        class="flex items-center justify-between md:gap-4 gap-3 md:p-4 py-2 px-3 bg-gray-900/50 rounded-lg border border-gray-700">

                        <h3 class="md:text-lg text-md font-medium text-white capitalize text-nowrap">Plan
                            {{ $plan }}</h3>

                        <div class="md:w-48 w-28">
                            <div class="relative">
                                <span class="absolute left-3 md:top-2.5 top-1 text-gray-500">$</span>
                                <input wire:model="prices.{{ $plan }}" type="number" step="100"
                                    min="0"
                                    class="w-full pl-8 md:py-2 py-1 bg-gray-800 border-gray-700 text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-6 py-4 bg-gray-950/50 border-t border-gray-700 flex  justify-center">
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-bold shadow-lg">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
