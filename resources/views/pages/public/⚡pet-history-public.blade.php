<?php

use App\Models\Veterinary;
use App\Models\Pet;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.guest')] #[Title('Historial de Mascota')] class extends Component {
    public $pet;

    public $veterinary;
    public $logo;
    public $isCancelled = false;

    public function mount(Veterinary $veterinary, Pet $pet, $token)
    {
        if ($pet->public_token !== $token || $pet->veterinary_id !== $veterinary->id) {
            abort(404);
        }

        $this->veterinary = $veterinary;

        if ($veterinary->plan === 'pro') {
            $this->logo = $veterinary->profile?->logo;
            if (!$this->logo) {
                $this->logo = asset('assets/logo-v.png');
            } else {
                $this->logo = asset('storage/' . $this->logo);
            }
        } else {
            $this->logo = asset('assets/logo.png');
        }

        if ($veterinary->subscription_status === \App\Enums\SubscriptionStatus::CANCELLED) {
            $this->isCancelled = true;
            $this->logo = asset('assets/logo.png');
            return;
        }

        $this->pet = $pet->load([
            'customer',
            'veterinary',
            'medicalRecords' => function ($query) {
                $query->where('is_visible_to_owner', true)
                      ->with(['type', 'files' => function($q) {
                          $q->where('is_visible_to_owner', true);
                      }])
                      ->orderByDesc('performed_at');
            },
        ]);
    }
}; ?>

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Outfit', sans-serif;
        }

        @media print {
            body {
                background-color: white !important;
                color: black !important;
            }

            .no-print {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            .bg-stone-50,
            .bg-white {
                background-color: white !important;
            }

            .shadow-sm,
            .shadow-md,
            .shadow-xl {
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
            }

            .text-indigo-600,
            .text-teal-600 {
                color: #0d9488 !important;
            }
        }
    </style>
@endpush

{{-- <div
    class="min-h-screen bg-stone-50 md:py-12 py-6 px-4 sm:px-6 lg:px-8 selection:bg-teal-100 selection:text-teal-900 relative"> --}}


<div
    class="min-h-screen bg-linear-to-br from-stone-50 via-white to-teal-50 relative max-w-full overflow-x-hidden pt-4 px-2 overflow-y-hidden">



    <!-- glow -->
    <div
        class="absolute top-[-200px] left-[-200px] w-[500px] h-[500px] bg-teal-200/40 rounded-full blur-[140px] overflow-hidden">
    </div>
    <div class="absolute bottom-[-200px] right-[-200px] w-[500px] h-[500px] bg-emerald-200/40 rounded-full blur-[140px]">
    </div>






    <!-- Decorative background elements -->
    <div class="fixed top-0 left-0 w-full h-1 bg-linear-to-r from-teal-500 via-emerald-500 to-sky-500 z-50"></div>
    <div
        class="absolute top-0 right-0 -translate-y-12 translate-x-12 w-64 h-64 bg-teal-100/50 rounded-full blur-3xl opacity-50">
    </div>
    <div
        class="absolute bottom-0 left-0 translate-y-12 -translate-x-12 w-96 h-96 bg-emerald-100/50 rounded-full blur-3xl opacity-50">
    </div>


    <div class="relative max-w-4xl mx-auto md:space-y-8 space-y-6">

        <!-- Header & Logo -->
        <header class="flex flex-col items-center space-y-4 no-print">
            <div
                class="p-4 bg-white rounded-2xl shadow-sm border border-stone-200/60 transition-transform hover:scale-105 duration-300">
                <img src="{{ $logo }}" class="h-20 md:h-24 object-contain" alt="{{ $veterinary->name }}">
            </div>
            <div class="text-center">
                <h1 class="text-3xl font-extrabold text-stone-800 tracking-tight">{{ $veterinary->name }}</h1>
                <p class="text-teal-600 font-semibold tracking-wide uppercase text-xs mt-1">Historial Clínico Digital
                </p>
            </div>
        </header>

        @if ($isCancelled)
            <div
                class="bg-white rounded-3xl shadow-xl shadow-stone-200/50 border border-stone-200 p-12 text-center overflow-hidden relative">
                <div class="absolute top-0 right-0 p-8 opacity-5">
                    <svg class="w-32 h-32 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="inline-flex p-4 bg-red-50 rounded-full mb-6">
                        <svg class="h-10 w-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-stone-800 mb-2">Acceso Restringido</h2>
                    <p class="text-stone-500 max-w-md mx-auto mb-8">
                        Lo sentimos, el historial de este animalito no se puede visualizar en este momento debido a el
                        estado de la cuenta de la veterinaria <strong>{{ $veterinary->name }}</strong>.
                    </p>
                    <p class="text-sm font-medium text-stone-400">Por favor, contacte directamente a la veterinaria para
                        obtener una copia física del historial.</p>
                </div>
            </div>
        @else
            <!-- Pet Information Card -->
            <section
                class="bg-white rounded-3xl shadow-lg shadow-teal-400/20 border border-stone-200 overflow-hidden relative group">
                <div class="absolute top-0 left-0 w-2 h-full bg-teal-500"></div>

                <div class="p-4 md:p-8 bg-linear-to-br from-white to-stone-50">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div
                                    class="md:size-25 size-20 rounded-2xl bg-teal-50 flex items-center justify-center border-2 border-teal-100/50 overflow-hidden shadow-inner">
                                    @if ($pet->photo_path)
                                        <img src="{{ asset('storage/' . $pet->photo_path) }}"
                                            class="w-full h-full object-cover" alt="{{ $pet->name }}">
                                    @else
                                        <svg class="w-12 h-12 text-teal-200" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                                            <circle cx="12" cy="12" r="5" />
                                        </svg>
                                    @endif
                                </div>
                                <div
                                    class="absolute -bottom-2 -right-2 bg-white md:p-1.5 p-1 rounded-xl shadow-sm border border-stone-100">
                                    <span class="text-lg">🐾</span>
                                </div>
                            </div>
                            <div>
                                <h2 class="md:text-3xl text-2xl font-extrabold text-stone-800 tracking-tight">
                                    {{ $pet->name }}
                                </h2>
                                <p class="text-stone-500 font-medium">{{ $pet->specie_name }} •
                                    {{ $pet->breed_name }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span
                                        class="inline-flex items-center md:px-2.5 px-1.5 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-teal-700 uppercase tracking-tight border border-teal-100">
                                        {{ $pet->age }}
                                    </span>
                                    <span
                                        class="inline-flex items-center md:px-2.5 px-1.5 py-0.5 rounded-full text-xs font-bold {{ $pet->gender == 'male' ? 'bg-blue-50 text-blue-700 border-blue-100' : ($pet->gender == 'female' ? 'bg-pink-50 text-pink-700 border-pink-100' : 'bg-stone-50 text-stone-600 border-stone-200') }} uppercase tracking-tight border">
                                        {{ $pet->gender == 'male' ? 'Macho' : ($pet->gender == 'female' ? 'Hembra' : 'Desconocido') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-1 gap-3 md:gap-4 text-sm">
                            <div class="bg-stone-100/50 md:p-3 p-2 rounded-2xl border border-stone-200/40">
                                <p class="text-stone-400 font-bold text-[10px] uppercase mb-0.5">Dueño</p>
                                <p class="text-stone-800 font-bold leading-none">{{ $pet->customer->name }}</p>
                            </div>
                            <div class="bg-stone-100/50 md:p-3 p-2 rounded-2xl border border-stone-200/40">
                                <p class="text-stone-400 font-bold text-[10px] uppercase mb-0.5">Microchip</p>
                                <p class="text-stone-800 font-bold leading-none">
                                    {{ $pet->microchip_id ?? 'No registrado' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Pet Info -->
                    <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-stone-400 font-bold text-[10px] uppercase tracking-wider">Color</p>
                            <p class="text-stone-700 font-semibold">{{ $pet->color ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-stone-400 font-bold text-[10px] uppercase tracking-wider">Peso Actual</p>
                            <p class="text-stone-700 font-semibold">{{ $pet->weight ? $pet->weight . ' kg' : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-stone-400 font-bold text-[10px] uppercase tracking-wider">Esterilizado</p>
                            <p class="text-stone-700 font-semibold">{{ $pet->is_sterilized ? 'Sí' : 'No' }}</p>
                        </div>
                        <div>
                            <p class="text-stone-400 font-bold text-[10px] uppercase tracking-wider">Tipo Sangre</p>
                            <p class="text-stone-700 font-semibold uppercase">{{ $pet->blood_type ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- @if ($pet->allergies || $pet->chronic_medications)
                        <div class="mt-6 flex flex-col md:flex-row gap-4">
                            @if ($pet->allergies)
                                <div class="flex-1 bg-amber-50 rounded-2xl p-4 border border-amber-100">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-amber-500">⚠️</span>
                                        <h3 class="text-amber-900 font-bold text-xs uppercase">Alergias Conocidas</h3>
                                    </div>
                                    <p class="text-amber-800 text-sm font-medium">{{ $pet->allergies }}</p>
                                </div>
                            @endif
                            @if ($pet->chronic_medications)
                                <div class="flex-1 bg-sky-50 rounded-2xl p-4 border border-sky-100">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sky-500">💊</span>
                                        <h3 class="text-sky-900 font-bold text-xs uppercase">Medicación Crónica</h3>
                                    </div>
                                    <p class="text-sky-800 text-sm font-medium">{{ $pet->chronic_medications }}</p>
                                </div>
                            @endif
                        </div>
                    @endif --}}
                </div>
            </section>

            <!-- Medical History Timeline -->
            <main class="md:space-y-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="md:text-xl text-lg font-extrabold text-stone-800 flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-lg bg-teal-600 flex items-center justify-center text-white shadow-md shadow-teal-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01">
                                </path>
                            </svg>
                        </div>
                        Cronograma de Atenciones
                    </h3>
                    <div class="no-print">
                        <button onclick="window.print()"
                            class="text-stone-400 hover:text-teal-600 transition-colors p-2 rounded-xl hover:bg-teal-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 002 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="md:space-y-4 space-y-2 relative">
                    <!-- Timeline connector -->
                    <div class="absolute top-0 bottom-0 left-3 md:left-8 w-1 bg-stone-200 rounded-full no-print"></div>

                    @forelse($pet->medicalRecords as $record)
                        <article class="relative pl-8 md:pl-20 mb-4 md:mb-8 last:mb-0 group ">
                            <!-- Timeline dot -->
                            <div
                                class="absolute left-1.5 md:left-6 top-8 md:size-5 size-4 rounded-full bg-teal-200 outline-2 outline-offset-3  {{ $loop->first ? 'outline-teal-300 bg-teal-300' : 'outline-white bg-teal-200' }} z-20 shadow-sm transition-transform group-hover:scale-135 duration-300 no-print ">
                            </div>

                            <div
                                class="bg-white rounded-3xl border border-stone-200/70 shadow-md shado-stone-200/20 shadow-teal-200/40 overflow-hidden hover:shadow-xl hover:shadow-stone-200/40 hover:border-teal-200 transition-all duration-300">
                                <div class="p-4 md:p-8 md:space-y-6 space-y-4">
                                    <div
                                        class="flex flex-col md:flex-row md:items-center justify-between md:gap-4 gap-2">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="text-teal-600 text-xs font-black uppercase tracking-widest">{{ $record->performed_at->format('d M, Y') }}</span>
                                                @if ($record->weight)
                                                    <span
                                                        class="bg-stone-100 text-stone-500 px-2 py-0.5 rounded-lg text-[10px] font-bold border border-stone-200/40">
                                                        {{ $record->weight }} kg
                                                    </span>
                                                @endif
                                            </div>
                                            <h4 class="text-lg md:text-2xl font-extrabold text-stone-800">
                                                {{ $record->type ? $record->type->name : $record->custom_type_name }}
                                            </h4>
                                        </div>
                                        @if ($record->next_appointment_at)
                                            <div
                                                class="bg-emerald-50 px-2 md:px-4 py-1 md:py-2 rounded-2xl border border-emerald-100 text-right">
                                                <p class="text-emerald-500 font-bold text-[10px] uppercase md:mb-0.5">
                                                    Próxima Visita</p>
                                                <p class="text-emerald-800 font-extrabold text-sm">
                                                    {{ $record->next_appointment_at->format('d/m/Y') }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @if ($record->notes)
                                            <div class="md:space-y-1">
                                                <h5
                                                    class="text-stone-400 font-bold text-[10px] uppercase tracking-widest">
                                                    Resumen de la Consulta</h5>
                                                <p class="text-stone-600 text-sm leading-relaxed">{{ $record->notes }}
                                                </p>
                                            </div>
                                        @endif

                                        @if ($record->prescriptions)
                                            <div class="bg-stone-50 rounded-2xl p-5 border border-stone-100">
                                                <h5
                                                    class="text-indigo-600 font-bold text-[10px] uppercase tracking-widest flex items-center gap-1.5 mb-2">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19.428 15.428a2 2 0 00-2-2H6.572a2 2 0 00-2 2v4.572a2 2 0 002 2h10.856a2 2 0 002-2v-4.572zM12 11V3m0 0l3 3m-3-3L9 6">
                                                        </path>
                                                    </svg>
                                                    Indicaciones / Medicamentos
                                                </h5>
                                                <p class="text-stone-700 text-sm font-medium whitespace-pre-wrap">
                                                    {{ $record->prescriptions }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($record->recommendations)
                                        <div
                                            class="bg-teal-50/50 rounded-2xl p-5 border border-teal-100/40 relative overflow-hidden">
                                            <div
                                                class="absolute -top-4 -right-4 w-12 h-12 bg-teal-100/20 rounded-full blur-xl">
                                            </div>
                                            <h5
                                                class="text-teal-700 font-bold text-[10px] uppercase tracking-widest flex items-center gap-1.5 mb-2 relative z-10">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                                Recomendaciones para el Hogar
                                            </h5>
                                            <p class="text-teal-800 text-sm font-medium relative z-10">
                                                {{ $record->recommendations }}</p>
                                        </div>
                                    @endif

                                    @if($record->files->isNotEmpty())
                                        <div class="mt-4 pt-4 border-t border-stone-100 flex flex-wrap gap-2">
                                            @foreach($record->files as $file)
                                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-50 border border-stone-200 text-stone-600 rounded-lg text-xs font-semibold hover:bg-stone-100 hover:text-teal-600 transition-colors">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    {{ Str::limit($file->original_name, 20) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="bg-white rounded-3xl p-12 text-center border-2 border-dashed border-stone-200">
                            <div
                                class="w-16 h-16 bg-stone-50 rounded-full flex items-center justify-center mx-auto mb-4 text-stone-200">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-stone-800 font-bold">Sin registros disponibles</h4>
                            <p class="text-stone-400 text-sm max-w-xs mx-auto mt-1">Aún no hay atenciones registradas
                                para mostrar al público.</p>
                        </div>
                    @endforelse
                </div>
            </main>
        @endif

        <!-- Footer -->
        <footer class="text-center space-y-4 pt-10 pb-12 no-print">

            <div class="space-y-1">
                <p class="text-stone-400 text-xs font-bold tracking-widest uppercase">
                    © {{ date('Y') }} {{ config('app.name') }}
                </p>
                <p class="text-stone-400 text-sm font-medium">
                    Desarrollado por <a href="https://www.cumbreit.com.ar" target="_blank"
                        class="text-stone-700 hover:font-bold hover:underline">Cumbre<span
                            class="text-teal-600">IT</span></a>
                </p>
            </div>
        </footer>
    </div>
</div>
