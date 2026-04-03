<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-slate-50 text-gray-800 antialiased pt-6">

    <!-- Background Effects -->
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(16,185,129,0.15),transparent_40%)]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_70%,rgba(6,182,212,0.15),transparent_40%)]"></div>

    <div class="relative flex min-h-screen items-center justify-center px-6">

        <div class="w-full max-w-md">

            <!-- Logo Section -->
            <div class="flex flex-col items-center mb-4 space-y-3">

                <div class="relative flex flex-col items-center ">
                    <img src="{{ asset('assets/logo.png') }}"
                        class="h-24 object-contain drop-shadow-[0_0_30px_rgba(16,185,129,0.4)]" alt="CumbrePets">


                </div>

                <h1 class="text-2xl font-semibold tracking-wide text-gray-800">
                    Bienvenido a Cumbre<span class="text-emerald-500">Vets</span>
                </h1>


                <p class="text-sm text-gray-500 text-center max-w-xs">
                    Plataforma moderna para la gestión de clínicas veterinarias
                </p>

            </div>

            <!-- Card -->
            <div
                class="relative bg-white border border-gray-200 shadow-md shadow-emerald-500/20 rounded-2xl  p-6 space-y-6 ">

                <!-- Soft Glow -->
                <div class="absolute -top-20 -right-20 size-60 bg-emerald-400/20 blur-[120px] rounded-full"></div>

                {{ $slot }}

            </div>

            <!-- Footer -->
            <p class="text-center text-sm text-gray-500 mt-8 pb-8">
                © {{ date('Y') }}
                <a href="https://cumbreit.com" target="_blank"
                    class="text-gray-700 hover:text-emerald-600 font-medium transition">
                    Cumbre<span class="text-emerald-500">IT</span>
                </a>.
                Todos los derechos reservados.
            </p>

        </div>

    </div>

</body>

</html>
