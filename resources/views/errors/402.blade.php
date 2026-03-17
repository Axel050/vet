<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción no Activa | ServiceApp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-950 text-white min-h-screen flex flex-col items-center justify-center p-6 font-sans antialiased">
    <div
        class="max-w-md w-full text-center border border-red-400 md:shadow-2xl shadow-xl shadow-red-500/20 rounded-2xl md:p-8 p-5">
        <!-- Icon -->
        <div class="md:mb-8 mb-4 flex justify-center">
            <div
                class="md:p-6 p-4  bg-red-500/10 rounded-full border border-red-500/20 shadow-[0_0_50px_-12px_rgba(239,68,68,0.3)]">
                <svg class="md:size-16 size-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Content -->
        <h1 class="md:text-3xl text-xl font-black mb-4 tracking-tight">Suscripción Requerida</h1>

        <div class="bg-gray-900/50 border border-gray-800 rounded-2xl md:p-6 p-3 md:mb-8 mb-4 backdrop-blur-xl">
            <p class="text-gray-300 leading-relaxed md:text-lg text-base">
                {{ $exception->getMessage() ?: 'No hemos podido verificar una suscripción activa para tu veterinaria.' }}
            </p>
            {{-- <p class="text-gray-500 text-sm mt-4 italic">
                Código de error: 402
            </p> --}}
        </div>

        <!-- Actions -->
        <div class="space-y-4">
            <a href="{{ route('dashboard') }}"
                class="block w-full md:py-4 py-1.5 md:px-6 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-indigo-500/25 active:scale-[0.98]">
                Ir al Dashboard
            </a>

            <a href="mailto:soporte@cumbreit.com.ar"
                class="block w-full md:py-4 py-1.5 md:px-6 px-4 bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium rounded-xl transition-all border border-gray-700">
                Contactar Soporte
            </a>
        </div>


    </div>

    <!-- Footer -->
    <div class="md:mt-12 mt-10">
        <p class="text-gray-500 text-xs">
            &copy; {{ date('Y') }} CumbreIT. Todos los derechos reservados.
        </p>
    </div>
</body>

</html>
