<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CumbreVets - Sistema para Veterinarias</title>

    <link rel="icon" href="/assets/logo.png" sizes="any">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800,900|inter:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body
    class="bg-slate-50 text-gray-900 font-sans selection:bg-emerald-500 selection:text-white antialiased overflow-x-hidden">

    <!-- NAVBAR -->
    <header class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-xl border-b border-gray-200">

        <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

            <div class="flex items-center gap-2">

                <img src="{{ asset('assets/logo.png') }}" class="h-10 rounded-lg">

                <span class="text-xl font-black tracking-tight">
                    Cumbre<span class="text-emerald-500">Vets</span>
                </span>

            </div>

            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-gray-600">

                <a href="#inicio" class="hover:text-emerald-600">Inicio</a>
                <a href="#funciones" class="hover:text-emerald-600">Funciones</a>
                <a href="#beneficios" class="hover:text-emerald-600">Beneficios</a>

            </div>

            <div>

                @auth

                    <a href="{{ url('/dashboard') }}"
                        class="bg-gray-900 text-white px-6 py-2 rounded-full text-sm font-semibold hover:bg-black transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="bg-gradient-to-r from-emerald-500 to-cyan-500 text-white px-6 py-2 rounded-full text-sm font-semibold hover:opacity-90 transition">
                        Ingresar
                    </a>

                @endauth

            </div>

        </nav>

    </header>

    <main class="pt-20">

        <!-- HERO -->
        <section id="inicio" class="py-24">

            <div class="max-w-6xl mx-auto px-6 text-center">

                <h1 class="text-4xl md:text-6xl font-black tracking-tight">

                    La tecnología que mejora
                    <span class="bg-gradient-to-r from-emerald-500 to-cyan-500 bg-clip-text text-transparent">
                        el cuidado de las mascotas
                    </span>

                </h1>

                <p class="mt-6 text-lg text-gray-600 max-w-2xl mx-auto">

                    Gestioná pacientes, historiales médicos y consultas de forma simple.
                    Un sistema moderno para clínicas veterinarias.

                </p>

                <div class="mt-10 flex md:flex-row flex-col gap-4 justify-center">

                    <a href="https://wa.me/1162841353?text=Hola quiero probar el sistema veterinario" target="_blank"
                        class="px-10 py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-cyan-500 text-white font-semibold shadow-md hover:scale-105 transition">

                        Probar gratis

                    </a>

                    <a href="#funciones"
                        class="px-10 py-4 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition">

                        Ver funcionalidades

                    </a>

                </div>

                <div class="mt-16 text-6xl opacity-30">

                    🐾

                </div>

            </div>

        </section>

        <!-- STATS -->
        <section class="py-20 bg-white border-y border-gray-200">

            <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-10 text-center">

                <div>

                    <div class="text-4xl font-black text-gray-900">+150</div>
                    <div class="text-gray-500 text-sm">Veterinarias</div>

                </div>

                <div>

                    <div class="text-4xl font-black text-gray-900">2k</div>
                    <div class="text-gray-500 text-sm">Mascotas</div>

                </div>

                <div>

                    <div class="text-4xl font-black text-gray-900">8k</div>
                    <div class="text-gray-500 text-sm">Consultas</div>

                </div>

                <div>

                    <div class="text-4xl font-black text-gray-900">100%</div>
                    <div class="text-gray-500 text-sm">Digital</div>

                </div>

            </div>

        </section>

        <!-- FEATURES -->
        <section id="funciones" class="py-28">

            <div class="max-w-7xl mx-auto px-6">

                <div class="text-center mb-20">

                    <h2 class="text-4xl font-black">

                        Todo lo que tu veterinaria necesita

                    </h2>

                    <p class="text-gray-600 mt-6 max-w-2xl mx-auto">

                        Herramientas diseñadas para mejorar la atención, organización y seguimiento de cada mascota.

                    </p>

                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                    <!-- Feature -->
                    <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">

                        <div class="text-4xl mb-4">📋</div>

                        <h3 class="text-xl font-bold mb-3">
                            Historial Médico
                        </h3>

                        <p class="text-gray-600">
                            Los dueños pueden consultar consultas, vacunas y tratamientos desde un link o código QR.
                        </p>

                    </div>

                    <!-- Feature -->
                    <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">

                        <div class="text-4xl mb-4">🐶</div>

                        <h3 class="text-xl font-bold mb-3">
                            Gestión de Mascotas
                        </h3>

                        <p class="text-gray-600">
                            Registrá pacientes con toda su información médica y la del dueño.
                        </p>

                    </div>

                    <!-- Feature -->
                    <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">

                        <div class="text-4xl mb-4">📅</div>

                        <h3 class="text-xl font-bold mb-3">
                            Agenda de Turnos
                        </h3>

                        <p class="text-gray-600">
                            Organizá consultas, recordatorios de vacunas y seguimientos médicos.
                        </p>

                    </div>

                </div>

            </div>

        </section>

        <!-- CTA -->
        <section id="beneficios" class="py-28 bg-white border-t border-gray-200">

            <div class="max-w-4xl mx-auto px-6 text-center">

                <h2 class="text-4xl md:text-5xl font-black mb-6">

                    Llevá tu veterinaria al siguiente nivel

                </h2>

                <p class="text-lg text-gray-600 mb-10">

                    Digitalizá tu clínica veterinaria y brindá una mejor experiencia a los dueños de mascotas.

                </p>

                <a href="https://wa.me/1162841353?text=Hola quiero probar el sistema veterinario"
                    class="px-12 py-5 bg-gradient-to-r from-emerald-500 to-cyan-500 text-white font-bold rounded-xl shadow-lg hover:scale-105 transition">

                    Solicitar Demo

                </a>

            </div>

        </section>

    </main>

    <!-- FOOTER -->
    <footer class="py-16 bg-white border-t border-gray-200">

        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">

            <div class="flex items-center gap-2">

                <img src="{{ asset('assets/logo.png') }}" class="h-8">

                <span class="font-bold">
                    Cumbre<span class="text-emerald-500">Vets</span>
                </span>

            </div>

            <div class="text-gray-500 text-sm">

                © {{ date('Y') }} CumbreVets

            </div>

        </div>

    </footer>

</body>

</html>
