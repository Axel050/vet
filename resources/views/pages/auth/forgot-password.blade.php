<x-layouts::auth>
    <div class="flex flex-col gap-8">

        <!-- Header -->
        <x-auth-header :title="__('Recuperar contraseña')" :description="__('Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center text-green-400 font-medium" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email -->
            <x-input :label="__('Correo electrónico')" model="email" type="email" name="email" required autofocus autocomplete="email"
                placeholder="email@example.com" value="{{ old('email') }}" />

            <!-- Submit Button -->
            <button type="submit"
                class="relative w-full py-3 rounded-xl 
                       bg-gradient-to-r from-blue-600 to-indigo-600
                       text-white font-semibold
                       shadow-lg shadow-blue-900/40
                       hover:shadow-blue-700/50
                       hover:scale-[1.01]
                       active:scale-[0.99]
                       transition-all duration-300"
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
