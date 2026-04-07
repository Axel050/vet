<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="hidden md:flex ">
            <div class="flex flex-col w-52 bg-gray-950 border-r border-gray-800">
                <!-- Logo -->
                <div class="flex items-center h-16  px-4 bg-gray-950 border-b border-gray-800">
                    <span class="text-xl font-bold text-white">Cumbre<span class="text-indigo-500">Vets</span></span>
                </div>

                <!-- Navigation -->
                <div class="flex-1 flex flex-col overflow-y-auto">
                    <nav class="flex-1 px-2 py-4 space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}"
                            class="{{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors">
                            <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboards
                        </a>

                        @if (auth()->user()->veterinary_id)
                            <div class="pt-4">
                                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Mi Veterinaria
                                </p>
                                <a href="{{ route('veterinaria.customers') }}"
                                    class="{{ request()->routeIs('veterinaria.customers') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1 transition-colors">
                                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Clientes
                                </a>

                                <a href="{{ route('veterinaria.types') }}"
                                    class="{{ request()->routeIs('veterinaria.types') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1 transition-colors">
                                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    Tipos
                                </a>

                                <a href="{{ route('veterinaria.records') }}"
                                    class="{{ request()->routeIs('veterinaria.records') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1 transition-colors">
                                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    Historial
                                </a>

                                @can('pro-veterinaria')
                                    <a href="{{ route('veterinaria.profile.editor') }}"
                                        class="{{ request()->routeIs('veterinaria.profile.editor') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1 transition-colors">
                                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Mi Perfil (PRO)
                                    </a>
                                @endcan
                            </div>
                        @endif

                        {{-- @if (auth()->user()->is_superadmin) --}}
                        @can('manage-veterinarias')
                            <!-- SuperAdmin Section -->
                            <div class="pt-4">
                                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Administración
                                </p>
                                <a href="{{ route('admin.veterinarias') }}"
                                    class="{{ request()->routeIs('admin.veterinarias') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1 transition-colors">
                                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Veterinarias
                                </a>

                                <a href="{{ route('admin.especies') }}"
                                    class="{{ request()->routeIs('admin.especies') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1 transition-colors">
                                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 13l1-3a2 2 0 012-1h12a2 2 0 012 1l1 3M5 13h14M6 16h.01M18 16h.01M5 16a2 2 0 002 2h10a2 2 0 002-2v-3H5v3z" />
                                    </svg>
                                    Especies
                                </a>



                                <a href="{{ route('admin.razas') }}"
                                    class="{{ request()->routeIs('admin.razas') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1 transition-colors">
                                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                    Razas
                                </a>




                                <a href="{{ route('admin.plan-prices') }}"
                                    class="{{ request()->routeIs('admin.plan-prices') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1 transition-colors">
                                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Precios de Planes
                                </a>
                            </div>
                        @endcan
                    </nav>
                </div>

                <!-- User Menu -->
                <div class="shrink-0 flex flex-col border-t border-gray-800 p-4 bg-gray-950 overflow-x-hidden">
                    <div class="flex items-center w-full">
                        <div class="shrink-0">
                            <div
                                class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs font-medium text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="ml-2 text-gray-400 hover:text-red-500 transition-colors flex items-center px-4 py-1 border border-red-500 gap-2 mt-2 rounded-xl">
                            <span>Cerrar Sesión</span>
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>

                </div>
            </div>
        </aside>

        <!-- Mobile menu button -->
        <div
            class="md:hidden fixed top-0 left-0 right-0 z-40 flex items-center justify-between h-16 bg-gray-950 px-4 border-b border-gray-800">
            <a href="{{ route('home') }}" class="text-xl font-bold text-white">Cumbre<span
                    class="text-indigo-500">Vets</span></a>
            <button type="button" class="text-gray-400 hover:text-white"
                onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden fixed inset-0 z-50 bg-gray-950">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-white">Cumbre<span
                            class="text-indigo-500">Vets</span></a>
                    <button type="button" class="text-gray-400 hover:text-white"
                        onclick="document.getElementById('mobile-menu').classList.add('hidden')">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2 text-base font-medium rounded-md transition-colors">
                        <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    @if (auth()->user()->veterinary_id)
                        <div class="pt-4 pb-2">
                            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Mi Veterinaria
                            </p>
                        </div>

                        <a href="{{ route('veterinaria.customers') }}"
                            class="{{ request()->routeIs('veterinaria.customers') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2 text-base font-medium rounded-md transition-colors">
                            <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Clientes
                        </a>
                        <a href="{{ route('veterinaria.types') }}"
                            class="{{ request()->routeIs('veterinaria.types') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2 text-base font-medium rounded-md mt-1 transition-colors">
                            <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Tipos
                        </a>
                        <a href="{{ route('veterinaria.records') }}"
                            class="{{ request()->routeIs('veterinaria.records') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2 text-base font-medium rounded-md mt-1 transition-colors">
                            <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            Historial
                        </a>

                        @if (auth()->user()->veterinaria?->plan === 'pro')
                            <a href="{{ route('veterinaria.profile.editor') }}"
                                class="{{ request()->routeIs('veterinaria.profile.editor') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2 text-base font-medium rounded-md mt-1 transition-colors">
                                <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Mi Perfil (PRO)
                            </a>
                        @endif
                    @endif

                    @can('manage-veterinarias')
                        <div class="pt-4 pb-2">
                            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Administración
                            </p>
                        </div>
                        <a href="{{ route('admin.veterinarias') }}"
                            class="{{ request()->routeIs('admin.veterinarias') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2 text-base font-medium rounded-md transition-colors">
                            <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Gestión de Veterinarias
                        </a>
                        <a href="{{ route('admin.especies') }}"
                            class="{{ request()->routeIs('admin.especies') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2 text-base font-medium rounded-md mt-1 transition-colors">
                            <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 13l1-3a2 2 0 012-1h12a2 2 0 012 1l1 3M5 13h14M6 16h.01M18 16h.01M5 16a2 2 0 002 2h10a2 2 0 002-2v-3H5v3z" />
                            </svg>
                            Especies
                        </a>
                        <a href="{{ route('admin.razas') }}"
                            class="{{ request()->routeIs('admin.razas') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2 text-base font-medium rounded-md mt-1 transition-colors">
                            <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            Razas
                        </a>
                    @endcan

                    <!-- Logout for Mobile -->
                    <div class="pt-4 mt-4 border-t border-gray-800">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left text-gray-300 hover:bg-gray-800 hover:text-white group flex items-center px-3 py-2 text-base font-medium rounded-md transition-colors">
                                <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <main class="flex-1 relative overflow-y-auto focus:outline-none pt-16 md:pt-0">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>
    <x-toast />
    @livewireScripts
</body>

</html>
