<x-layouts.guest title="Iniciar Sesión">
    <x-slot name="subtitle">
        Ingresa tus credenciales para acceder
    </x-slot>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <x-input
            type="email"
            name="email"
            label="Correo electrónico"
            placeholder="admin@pos.com"
            required
            autofocus
        />

        <x-input
            type="password"
            name="password"
            label="Contraseña"
            placeholder="••••••••"
            required
        />

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input
                    id="remember"
                    name="remember"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                >
                <label for="remember" class="ml-2 block text-sm text-gray-900">
                    Recordarme
                </label>
            </div>

            @if (Route::has('password.request'))
                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            @endif
        </div>

        <div>
            <x-button type="submit" variant="primary" class="w-full justify-center">
                Iniciar Sesión
            </x-button>
        </div>
    </form>

    <!-- PIN Login -->
    <div class="mt-6 pt-6 border-t border-gray-200">
        <p class="text-center text-sm text-gray-600 mb-4">O ingresa con tu PIN</p>

        <div x-data="pinLogin()" class="space-y-4">
            <div class="flex justify-center gap-2">
                <template x-for="i in 4">
                    <div
                        class="w-12 h-12 border-2 rounded-lg flex items-center justify-center text-2xl font-bold"
                        :class="pin.length >= i ? 'border-blue-500 bg-blue-50 text-blue-600' : 'border-gray-300'"
                    >
                        <span x-show="pin.length >= i">•</span>
                    </div>
                </template>
            </div>

            <div class="grid grid-cols-3 gap-2 max-w-xs mx-auto">
                <template x-for="num in [1,2,3,4,5,6,7,8,9,'',0,'del']">
                    <button
                        type="button"
                        @click="handleKey(num)"
                        class="h-14 rounded-lg font-semibold text-xl transition-colors"
                        :class="num === '' ? 'cursor-default' : 'bg-gray-100 hover:bg-gray-200 text-gray-800'"
                        x-show="num !== ''"
                    >
                        <span x-show="num !== 'del'" x-text="num"></span>
                        <svg x-show="num === 'del'" class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"></path>
                        </svg>
                    </button>
                </template>
            </div>

            <p x-show="error" x-text="error" class="text-center text-sm text-red-600"></p>
        </div>
    </div>

    <script>
        function pinLogin() {
            return {
                pin: '',
                error: '',
                handleKey(key) {
                    if (key === 'del') {
                        this.pin = this.pin.slice(0, -1);
                        this.error = '';
                    } else if (key !== '' && this.pin.length < 4) {
                        this.pin += key;
                        if (this.pin.length === 4) {
                            this.submitPin();
                        }
                    }
                },
                submitPin() {
                    fetch('{{ route("login.pin") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ pin: this.pin })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect;
                        } else {
                            this.error = data.error || 'PIN incorrecto';
                            this.pin = '';
                        }
                    })
                    .catch(() => {
                        this.error = 'Error al verificar PIN';
                        this.pin = '';
                    });
                }
            }
        }
    </script>
</x-layouts.guest>
