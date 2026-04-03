<?php

use App\Models\Veterinary;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Attributes\Computed;

new #[Layout('layouts.guest')] #[Title('Veterinaria')] class extends Component {
    public Veterinary $veterinary;

    public $profile;

    public $colors = [
        [
            'bg' => 'bg-emerald-500',
            'text' => 'text-emerald-500',
            'border' => 'border-emerald-400/70 group-hover:border-emerald-400/50',
            'shadow' => 'shadow-emerald-500/20',
            'gradient' => 'hover:bg-gradient-to-l from-white to-emerald-50 ',
            'icon' => 'emerald',
        ],

        [
            'bg' => 'bg-cyan-500',
            'text' => 'text-cyan-500',
            'border' => 'border-cyan-400/70 group-hover:border-cyan-400/50',
            'shadow' => 'shadow-cyan-500/20',
            'gradient' => 'bg-gradient-to-l from-white to-cyan-50',
            'icon' => 'cyan',
        ],
        [
            'bg' => 'bg-teal-500',
            'text' => 'text-teal-500',
            'border' => 'border-teal-400/70 group-hover:border-teal-400/50',
            'shadow' => 'shadow-teal-500/20',
            'gradient' => 'bg-gradient-to-l from-white to-teal-50',
            'icon' => 'teal',
        ],
        [
            'bg' => 'bg-lime-500',
            'text' => 'text-lime-500',
            'border' => 'border-lime-400/70 group-hover:border-lime-400/50',
            'shadow' => 'shadow-lime-500/20',
            'gradient' => 'bg-gradient-to-l from-white to-lime-50',
            'icon' => 'lime',
        ],
    ];

    public $colors3 = [
        [
            'bg' => 'bg-emerald-500',
            'text' => 'text-emerald-500',
            'border' => 'border-emerald-400/70 group-hover:border-emerald-400/50',
            'shadow' => 'shadow-emerald-500/20',
            'icon' => 'emerald',
        ],
        [
            'bg' => 'bg-cyan-500',
            'text' => 'text-cyan-500',
            'border' => 'border-cyan-400/70 group-hover:border-cyan-400/50',
            'shadow' => 'shadow-cyan-500/20',
            'icon' => 'cyan',
        ],
        [
            'bg' => 'bg-teal-500',
            'text' => 'text-teal-500',
            'border' => 'border-teal-400/70 group-hover:border-teal-400/50',
            'shadow' => 'shadow-teal-500/20',
            'icon' => 'teal',
        ],
        [
            'bg' => 'bg-lime-500',
            'text' => 'text-lime-500',
            'border' => 'border-lime-400/70 group-hover:border-lime-400/50',
            'shadow' => 'shadow-lime-500/20',
            'icon' => 'lime',
        ],
    ];

    public function mount(Veterinary $veterinary)
    {
        $this->veterinary = $veterinary;
        $this->profile = $veterinary->profile;

        // Redirect if not PRO plan? Maybe not, maybe just show a basic version.
        // Let's assume for now it's only for PRO.
        if ($veterinary->plan !== 'pro' || $veterinary->subscription_status === \App\Enums\SubscriptionStatus::CANCELLED) {
            abort(404);
        }
    }

    #[Computed]
    public function services()
    {
        return $this->veterinary->types()->where('is_active', true)->where('show_in_landing', true)->orderBy('name')->get();
    }

    #[Computed]
    public function socialLinks()
    {
        return $this->veterinary->socialLinks;
    }
};

?>

<!-- PAGE BACKGROUND -->

<div class="bg-slate-50 text-gray-800">

    <!-- HERO -->

    {{-- <section class="relative py-24 overflow-hidden">

        <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-200 blur-3xl opacity-40"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-cyan-200 blur-3xl opacity-40"></div>

        <div class="max-w-7xl mx-auto px-6 text-center relative">

            <h1 class="text-5xl font-black text-gray-900 mb-6">
                Cuidamos la salud de tu mascota 🐾
            </h1>

            <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-10">
                Atención veterinaria moderna, profesional y con mucho cariño para tu compañero.
            </p>

            <div class="flex justify-center gap-4">

                <a href="#servicios"
                    class="px-8 py-4 bg-emerald-500 text-white font-bold rounded-full hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/20">
                    Ver Servicios </a>

                <a href="#contacto"
                    class="px-8 py-4 bg-white border border-gray-200 rounded-full font-semibold hover:bg-gray-100 transition">
                    Reservar Turno </a>

            </div>

        </div>
    </section> --}}

    <section class="relative md:py-28 py-24 overflow-hidden">

        @if ($profile?->cover_image)
            <div class="absolute inset-0">
                <img src="{{ asset('storage/' . $profile->cover_image) }}" class="w-full h-full object-cover opacity-45">
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-cyan-100"></div>
        @endif

        <div class="absolute inset-0 bg-white/80"></div>

        <div class="relative max-w-7xl mx-auto px-6 text-center">

            @if ($profile?->logo)
                <img src="{{ asset('storage/' . $profile->logo) }}"
                    class="size-24 md:size-30 mx-auto mb-6 object-contain">
            @endif

            <h1 class="md:text-5xl text-3xl font-black text-gray-900 mb-6">
                {{ $profile->hero_title ?? 'Cuidamos la salud de tu mascota 🐾' }}
            </h1>

            <p class="md:text-xl text-lg text-gray-600 max-w-2xl mx-auto mb-10">
                {{ $profile->hero_subtitle ?? 'Atención veterinaria moderna y profesional.' }}
            </p>

            <div class="flex justify-center gap-4">

                <a href="#servicios"
                    class="md:px-8 px-4 md:py-4 py-3 bg-emerald-500 text-white font-bold rounded-full hover:bg-emerald-600 transition">
                    Ver Servicios
                </a>

                @if ($profile?->whatsapp)
                    <a href="https://wa.me/{{ $profile->whatsapp }}" target="_blank"
                        class="md:px-8 px-4 md:py-4 py-3 bg-white border border-gray-200 rounded-full font-semibold hover:bg-gray-100 transition">
                        Reservar Turno
                    </a>
                @endif

            </div>

        </div>
    </section>


    <!-- SERVICIOS -->

    <section id="servicios" class="md:py-24 py-20 bg-white">

        <div class="max-w-7xl mx-auto px-6">

            <h2 class="text-4xl font-black text-center mb-4">
                Servicios Veterinarios
            </h2>

            <p class="text-gray-500 text-center mb-16 max-w-2xl mx-auto">
                Brindamos atención completa para garantizar la salud y bienestar de tu mascota.
            </p>

            <div class="grid md:grid-cols-3 gap-8">

                @foreach ($this->services as $service)
                    @php
                        $color = $this->colors[$loop->index % count($this->colors)];
                        if ($service->icon) {
                            $icon = config('service-icons.' . $service->icon);
                        } else {
                            $icon = config('service-icons.default');
                        }
                    @endphp

                    <x-type-consulta-card :title="$service->name" :description="$service->description" :icon="$icon" :color="$color" />
                @endforeach

                {{-- <div class="bg-slate-50 p-8 rounded-2xl border border-gray-100 hover:shadow-lg transition">

                        <div class="{{ $color['text'] }} text-3xl mb-4">🐾</div>

                        <h3 class="text-xl font-bold mb-3">
                            {{ $service->name }}
                        </h3>

                        <p class="text-gray-600">
                            {{ $service->description }}
                        </p>

                    </div> --}}


                {{-- <div
                    class="bg-slate-50 p-8 rounded-2xl border border-gray-100 hover:shadow-lg transition shadow-md shadow-emerald-500/20 hover:translate-y-1">
                    <div class="text-emerald-500 text-3xl mb-4">🩺</div>
                    <h3 class="text-xl font-bold mb-3">Consulta General</h3>
                    <p class="text-gray-600">
                        Revisión completa del estado de salud de tu mascota con diagnóstico profesional.
                    </p>
                </div>

                <div class="bg-slate-50 p-8 rounded-2xl border border-gray-100 hover:shadow-lg transition">
                    <div class="text-cyan-500 text-3xl mb-4">💉</div>
                    <h3 class="text-xl font-bold mb-3">Vacunación</h3>
                    <p class="text-gray-600">
                        Plan de vacunación actualizado para prevenir enfermedades.
                    </p>
                </div>

                <div class="bg-slate-50 p-8 rounded-2xl border border-gray-100 hover:shadow-lg transition">
                    <div class="text-teal-500 text-3xl mb-4">🧪</div>
                    <h3 class="text-xl font-bold mb-3">Estudios Clínicos</h3>
                    <p class="text-gray-600">
                        Análisis y estudios para diagnósticos precisos.
                    </p>
                </div>

                <div class="bg-slate-50 p-8 rounded-2xl border border-gray-100 hover:shadow-lg transition">
                    <div class="text-emerald-500 text-3xl mb-4">🐶</div>
                    <h3 class="text-xl font-bold mb-3">Chequeos Preventivos</h3>
                    <p class="text-gray-600">
                        Control periódico para asegurar una vida saludable.
                    </p>
                </div>

                <div class="bg-slate-50 p-8 rounded-2xl border border-gray-100 hover:shadow-lg transition">
                    <div class="text-cyan-500 text-3xl mb-4">⚕️</div>
                    <h3 class="text-xl font-bold mb-3">Cirugía</h3>
                    <p class="text-gray-600">
                        Procedimientos veterinarios con tecnología moderna.
                    </p>
                </div>

                <div class="bg-slate-50 p-8 rounded-2xl border border-gray-100 hover:shadow-lg transition">
                    <div class="text-teal-500 text-3xl mb-4">🐾</div>
                    <h3 class="text-xl font-bold mb-3">Control y Seguimiento</h3>
                    <p class="text-gray-600">
                        Monitoreo continuo del estado de salud de tu mascota.
                    </p>
                </div> --}}

            </div>
        </div>
    </section>

    <!-- ABOUT -->

    <section class="md:py-24 py-20 bg-slate-100/90">

        <div class="max-w-6xl mx-auto px-6 text-center">

            <h2 class="text-4xl font-black mb-6">
                Amamos lo que hacemos
            </h2>

            <p class="text-gray-600 text-lg max-w-3xl mx-auto">
                {!! nl2br(
                    e(
                        $profile->description ??
                            'Nuestro equipo veterinario combina experiencia, tecnología y pasión por los animales para ofrecer atención de calidad.',
                    ),
                ) !!}
            </p>

        </div>
    </section>

    <!-- STATS -->

    <section class="py-20 bg-white">

        <div class="max-w-6xl mx-auto px-6">

            <div class="flex justify-center  md:flex-row flex-col items-center md:gap-30 gap-8">

                <div>
                    <h3 class="text-4xl font-black text-emerald-500">+1200</h3>
                    <p class="text-gray-500 mt-2">Mascotas atendidas</p>
                </div>

                <div>
                    <h3 class="text-4xl font-black text-cyan-500">+500</h3>
                    <p class="text-gray-500 mt-2">Clientes felices</p>
                </div>

                @if ($profile->years_in_business)
                    <div class="text-center">
                        <h3 class="text-4xl font-black text-teal-500">+{{ $profile->years_in_business }}</h3>
                        <p class="text-gray-500 mt-2">Años de experiencia</p>
                    </div>
                @endif

            </div>
        </div>
    </section>

    <!-- CTA -->

    <section id="contacto" class="md:py-24 py-20 bg-gradient-to-r from-emerald-500 to-cyan-500 text-white">

        <div class="max-w-4xl mx-auto px-6 text-center">

            <h2 class="text-4xl font-black mb-6">
                ¿Necesitas atención veterinaria?
            </h2>

            <p class="text-lg text-emerald-100 mb-10">
                Reserva una consulta para tu mascota y déjanos cuidar su salud.
            </p>

            <a href="https://wa.me/{{ $profile->whatsapp }}" target="_blank"
                class="px-10 py-4 bg-white text-emerald-600 font-bold rounded-full hover:scale-105 transition">
                Reservar Turno </a>

        </div>

    </section>




    <!-- CONTACTO -->

    <section id="contacto" class="md:py-24 py-20 bg-white">

        ```
        <div class="max-w-6xl mx-auto px-6">

            <div class="text-center mb-16">
                <h2 class="text-4xl font-black mb-4">
                    Contacto
                </h2>

                <p class="text-gray-500 max-w-xl mx-auto">
                    Estamos aquí para ayudarte. Contáctanos o visítanos para cuidar la salud de tu mascota.
                </p>
            </div>

            <div class="flex justify-center md:flex-row flex-col md:gap-15 gap-10 text-center">

                @if ($profile?->address)
                    <div class="bg-slate-50 md:p-8 p-4 rounded-2xl border border-gray-100 md:w-1/3">
                        <div class="text-3xl mb-4">📍</div>
                        <h3 class="font-bold text-lg mb-2">Dirección</h3>
                        <p class="text-gray-600">
                            {{ $profile->address }}
                        </p>
                    </div>
                @endif


                @if ($profile?->phone)
                    <div class="bg-slate-50 md:p-8 p-4 rounded-2xl border border-gray-100 md:w-1/3">
                        <div class="text-3xl mb-4">📞</div>
                        <h3 class="font-bold text-lg mb-2">Teléfono</h3>
                        <p class="text-gray-600">
                            {{ $profile->phone }}
                        </p>
                    </div>
                @endif


                @if ($profile?->whatsapp)
                    <div class="bg-slate-50 md:p-8 p-4 rounded-2xl border border-gray-100 md:w-1/3">

                        <a href="https://wa.me/{{ $profile->whatsapp }}" target="_blank">
                            <div class="text-3xl mb-4 flex justify-center">
                                <svg fill="#fff" class="w-6 h-7 lg:flex   bg-red-4 lg:scale-150 scale-115">
                                    <use xlink:href="#what"></use>
                                </svg>

                            </div>
                            <h3 class="font-bold text-lg mb-2">WhatsApp</h3>

                            <a href="https://wa.me/{{ $profile->whatsapp }}" target="_blank"
                                class="text-emerald-600 font-semibold hover:underline">
                                Enviar mensaje
                            </a>
                        </a>
                    </div>
                @endif

            </div>

        </div>





    </section>

    <!-- FOOTER -->



    <footer class="bg-slate-900 text-gray-400 py-12">


        <div class="max-w-6xl mx-auto px-6">

            <div class="grid md:grid-cols-3 gap-10 text-center md:text-left">

                <div>
                    <p class="text-white font-semibold mb-3">
                        {{ $veterinary->name }}
                    </p>

                    <p class="text-sm">
                        Cuidamos la salud y bienestar de tu mascota con atención profesional.
                    </p>
                </div>

                <div class="flex flex-col gap-2 items-center md:items-start">
                    <p class="text-white font-semibold mb-3">Contacto</p>

                    @if ($profile?->address)
                        <p class="text-sm">📍 {{ $profile->address }}</p>
                    @endif

                    @if ($profile?->phone)
                        <p class="text-sm">📞 {{ $profile->phone }}</p>
                    @endif

                    @if ($profile?->whatsapp)
                        <div class="text-sm  ">
                            <a href="https://wa.me/{{ $profile->whatsapp }}" target="_blank"
                                class="flex items-center gap-2">

                                <svg fill="#fff" class="w-4 h-5 lg:flex   bg-red-4 lg:scale-150 scale-115">
                                    <use xlink:href="#what"></use>
                                </svg>

                                {{ $profile->whatsapp }}
                            </a>
                        </div>
                    @endif
                </div>

                <div>
                    <p class="text-white font-semibold mb-3">Síguenos</p>

                    <div class="flex justify-center md:justify-start text-lg space-x-6">

                        @foreach ($this->socialLinks as $social)
                            <a href="{{ $social->url }}" target="_blank" class="h-7" alt="{{ $social->platform }}"
                                title="Ir a {{ $social->platform }}">

                                @if ($social->platform == 'instagram')
                                    <svg fill="#fff" class="w-6 h-7 lg:flex   bg-red-4 lg:scale-150 scale-115">
                                        <use xlink:href="#instagram"></use>
                                    </svg>
                                @endif

                                @if ($social->platform == 'facebook')
                                    <svg fill="#fff" class="w-6 h-7 lg:flex   bg-red-4 lg:scale-150 scale-115">
                                        <use xlink:href="#face"></use>
                                    </svg>
                                @endif

                                @if ($social->platform == 'youtube')
                                    <svg
                                        class="size-6 lg:flex   bg-red-4 lg:scale-150 scale-115 bg-white p-1 rounded-full   ">
                                        <use xlink:href="#youtube"></use>
                                    </svg>
                                @endif


                            </a>
                        @endforeach

                    </div>

                </div>

            </div>

            <div class="border-t border-slate-800 mt-10 pt-6 text-center text-sm">
                © {{ date('Y') }} {{ $veterinary->name }}
                <p>Desarrollado por <a href="https://cumbreit.com.ar/" class="text-white">Cumbre<span
                            class="text-cyan-500">IT</span></a></p>
            </div>

        </div>


    </footer>



</div>
