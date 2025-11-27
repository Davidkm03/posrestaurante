<div class="relative" x-data="{ open: @entangle('isOpen') }">
    <!-- Notification Bell Button -->
    <button @click="open = !open" 
            type="button" 
            class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        
        <!-- Badge -->
        @if($unreadCount > 0)
        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform bg-red-500 rounded-full">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
        @endif
    </button>

    <!-- Notification Panel -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
        
        <!-- Header -->
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Notificaciones</h3>
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                <button wire:click="markAllAsRead" 
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                    Marcar todas leídas
                </button>
                @endif
                @if(count($notifications) > 0)
                <button wire:click="clearAll" 
                        class="text-xs text-gray-500 hover:text-gray-700">
                    Limpiar
                </button>
                @endif
            </div>
        </div>

        <!-- Notification List -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
            <div wire:key="notification-{{ $notification['id'] }}"
                 class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ !$notification['read'] ? 'bg-blue-50' : '' }}">
                <div class="flex items-start gap-3">
                    <!-- Icon -->
                    <div class="flex-shrink-0 p-2 rounded-full 
                        @switch($notification['color'] ?? 'gray')
                            @case('blue') bg-blue-100 text-blue-600 @break
                            @case('green') bg-green-100 text-green-600 @break
                            @case('yellow') bg-yellow-100 text-yellow-600 @break
                            @case('red') bg-red-100 text-red-600 @break
                            @default bg-gray-100 text-gray-600
                        @endswitch">
                        @switch($notification['icon'] ?? 'bell')
                            @case('bell')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @break
                            @case('check-circle')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                @break
                            @case('exclamation-triangle')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                @break
                            @case('x-circle')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                @break
                            @default
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                        @endswitch
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        @if(isset($notification['link']) && $notification['link'])
                        <a href="{{ $notification['link'] }}" 
                           wire:click="markAsRead('{{ $notification['id'] }}')"
                           class="block">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $notification['title'] }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $notification['message'] }}</p>
                        </a>
                        @else
                        <div wire:click="markAsRead('{{ $notification['id'] }}')" class="cursor-pointer">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $notification['title'] }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $notification['message'] }}</p>
                        </div>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $notification['timestamp'] instanceof \Carbon\Carbon ? $notification['timestamp']->diffForHumans() : $notification['timestamp'] }}
                        </p>
                    </div>

                    <!-- Close Button -->
                    <button wire:click="removeNotification('{{ $notification['id'] }}')"
                            class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500">No hay notificaciones</p>
            </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if(count($notifications) > 0)
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
            <a href="{{ route('admin.settings.notifications') }}" 
               class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                Configurar notificaciones
            </a>
        </div>
        @endif
    </div>
</div>
