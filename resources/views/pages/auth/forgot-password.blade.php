<x-layouts::auth>
    <div class="flex flex-col gap-8">

        <!-- Header -->
        <div class="flex w-full flex-col text-center">
            <h1 class="text-2xl font-semibold text-gray-800">{{ __('Recuperar contraseña') }}</h1>
            <p class="text-gray-500 mt-1">
                {{ __('Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center text-green-400 font-medium" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 ">{{ __('Email') }}</label>
                <input id="email" type="email" name="email" value="{{ request('email') }}" required
                    autocomplete="email"
                    class="mt-1 block w-full border border-gray-500 rounded-xl  text-gray-800 dark:text-gray-100 placeholder-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 bg-gray-500" />
                @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
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
                data-test="email-password-reset-link-button">
                Enviar enlace de recuperación
            </button>
        </form>

        <!-- Back to login -->
        <div class="text-center text-sm text-gray-500">
            <span>¿Recordaste tu contraseña?</span>
            <a href="{{ route('login') }}" wire:navigate
                class="ml-1 text-blue-400 hover:text-blue-300 transition-colors">
                Iniciar sesión
            </a>
        </div>

    </div>
</x-layouts::auth>
