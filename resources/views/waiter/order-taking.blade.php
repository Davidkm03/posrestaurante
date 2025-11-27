<x-layouts.waiter title="Tomar Orden" :backUrl="route('waiter.index')">
    @livewire('waiter.order-taking', ['table' => $table])
</x-layouts.waiter>
