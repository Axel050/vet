<x-layouts::auth>
    <div class="flex flex-col md:gap-6 gap-4 ">

        <h1 class="md:text-2xl text-xl font-semibold text-gray-800 text-center">Iniciar sesión</h1>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1 ">
                    Correo electrónico
                </label>
                <input type="email" wire:model="email" placeholder="email@example.com" name="email"
                    value="{{ old('email') }}" autocomplete="email"
                    class="w-full px-2 md:px-4 py-1 md:py-1.5 bg-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-800 text-gray-700 placeholder-gray-500" />


                @error('email')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>



            <!-- Password -->
            <div class="relative">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1 ">
                        Contraseña
                    </label>
                    <input type="password" wire:model="password" placeholder="Contraseña" name="password"
                        value="{{ old('password') }}"
                        class="w-full px-2 md:px-4 py-1 md:py-1.5 bg-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-800 text-gray-700 placeholder-gray-500" />


                    @error('password')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if (Route::has('password.request'))
                    <a class="absolute top-0 text-xs right-0 text-emerald-600 hover:text-emerald-700 transition-colors"
                        href="{{ route('password.request') }}" wire:navigate>
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember" type="checkbox" name="remember"
                    class=" size-5 checked:bg-red-500 indeterminate:bg-blue-300 accent-emerald-500 outline-2 outline-emerald-400">
                <label for="remember" class="ml-2 text-sm text-gray-600">Recordarme</label>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit"
                    class="relative w-full py-3 rounded-xl 
                            bg-linear-to-r from-emerald-400 via-emerald-600 to-emerald-600           
                            bg-size-[200%_100%]
                            hover:bg-right
                            transition-all duration-500
                            text-white font-semibold tracking-wide
                            shadow-lg shadow-emerald-900/40
                            hover:shadow-blue-700/50
                            active:scale-[0.98]
                            focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                    data-test="login-button">

                    <span class="relative z-10">Entrar</span>

                    <!-- Glow layer -->
                    <span
                        class="absolute inset-0 rounded-xl bg-blue-500/20 blur-xl opacity-0 hover:opacity-100 transition-opacity duration-500"></span>
                </button>
            </div>
        </form>

    </div>
</x-layouts::auth>
