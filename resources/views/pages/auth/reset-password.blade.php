<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <div class="flex w-full flex-col text-center">
            <h1 class="text-2xl font-semibold text-gray-800">{{ __('Reset password') }}</h1>
            <p class="text-gray-500 mt-1">{{ __('Por favor, ingresa tu nueva contraseña') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 ">{{ __('Email') }}</label>
                <input id="email" type="email" name="email" value="{{ request('email') }}" required
                    autocomplete="email"
                    class="mt-1 block w-full border border-gray-500 rounded-xl  text-gray-800 dark:text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 bg-gray-500" />
                @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 ">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    placeholder="{{ __('Password') }}"
                    class="mt-1 block w-full border border-gray-500 rounded-xl  bg-gray-500 text-gray-800 dark:text-gray-100 placeholder-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3" />
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation"
                    class="block text-sm font-medium text-gray-700 ">{{ __('Confirm password') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password" placeholder="{{ __('Confirm password') }}"
                    class="mt-1 block w-full border border-gray-500 rounded-xl bg-gray-500 text-gray-800 dark:text-gray-100 placeholder-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3" />
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end mt-2">
                <button type="submit" data-test="reset-password-button"
                    class="relative w-full py-3 rounded-xl 
                            bg-linear-to-r from-emerald-400 via-emerald-600 to-emerald-600           
                            bg-size-[200%_100%]
                            hover:bg-right
                            transition-all duration-500
                            text-white font-semibold tracking-wide
                            shadow-lg shadow-emerald-900/40
                            hover:shadow-blue-700/50
                            active:scale-[0.98]
                            focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    {{ __('Reset password') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts::auth>
