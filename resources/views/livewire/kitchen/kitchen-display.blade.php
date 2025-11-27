<div class="h-screen flex flex-col bg-gray-900 text-white" wire:poll.{{ $refreshInterval }}s>
    @include('livewire.kitchen.partials.header')
    
    <div class="flex-1 flex overflow-hidden">
        <div class="flex-1 flex flex-col p-4 overflow-hidden">
            @include('livewire.kitchen.partials.orders-grid')
        </div>
        
        @if($readyOrders->count() > 0)
            @include('livewire.kitchen.partials.ready-sidebar')
        @endif
    </div>
    
    @include('livewire.kitchen.partials.scripts')
</div>
