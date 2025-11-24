<div class="fixed top-4 right-4 z-[100] space-y-2 max-w-sm w-full pointer-events-none">
    @foreach($notifications as $notification)
        <div x-data="{ show: true }"
             x-init="setTimeout(() => { show = false; $wire.removeNotification('{{ $notification['id'] }}') }, {{ $notification['duration'] }})"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             wire:key="notification-{{ $notification['id'] }}"
             class="pointer-events-auto rounded-lg shadow-lg p-4 flex items-start gap-3 {{ match($notification['type']) {
                 'success' => 'bg-green-600 text-white',
                 'error' => 'bg-red-600 text-white',
                 'warning' => 'bg-yellow-600 text-white',
                 'info' => 'bg-blue-600 text-white',
                 default => 'bg-gray-700 text-white',
             } }}">
            <!-- Icon -->
            <div class="flex-shrink-0">
                @switch($notification['type'])
                    @case('success')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @break
                    @case('error')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @break
                    @case('warning')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        @break
                    @default
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                @endswitch
            </div>

            <!-- Message -->
            <p class="flex-1 text-sm font-medium">{{ $notification['message'] }}</p>

            <!-- Close Button -->
            <button @click="show = false; $wire.removeNotification('{{ $notification['id'] }}')"
                    class="flex-shrink-0 p-1 hover:bg-white/20 rounded transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endforeach
</div>
