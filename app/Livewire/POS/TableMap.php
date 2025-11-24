<?php

namespace App\Livewire\POS;

use App\Models\Table;
use App\Models\Zone;
use App\Models\Order;
use App\Enums\TableStatus;
use App\Enums\OrderStatus;
use Livewire\Component;

class TableMap extends Component
{
    public ?int $selectedZoneId = null;
    public array $zones = [];
    public ?int $selectedTableId = null;
    public ?Table $selectedTable = null;
    public bool $showTableModal = false;

    protected $listeners = ['refresh-tables' => '$refresh'];

    public function mount()
    {
        $this->loadZones();
    }

    public function loadZones()
    {
        $branchId = session('current_branch_id');

        $this->zones = Zone::where('branch_id', $branchId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->toArray();

        if (!$this->selectedZoneId && !empty($this->zones)) {
            $this->selectedZoneId = $this->zones[0]['id'];
        }
    }

    public function selectZone(int $zoneId)
    {
        $this->selectedZoneId = $zoneId;
    }

    public function selectTable(int $tableId)
    {
        $this->selectedTableId = $tableId;
        $this->selectedTable = Table::with([
            'currentOrder.items',
            'currentOrder.waiter',
            'currentOrder.customer',
            'zone'
        ])->find($tableId);
        $this->showTableModal = true;
    }

    public function openTable()
    {
        if (!$this->selectedTable) return;

        if ($this->selectedTable->status !== TableStatus::FREE->value) {
            $this->dispatch('notify', type: 'error', message: 'La mesa no está disponible');
            return;
        }

        // Update table status and redirect to POS
        $this->selectedTable->update(['status' => TableStatus::OCCUPIED->value]);

        $this->dispatch('navigate-to-pos', tableId: $this->selectedTableId);
        $this->closeTableModal();
    }

    public function continueOrder()
    {
        if (!$this->selectedTable || !$this->selectedTable->currentOrder) return;

        $this->dispatch('navigate-to-pos',
            tableId: $this->selectedTableId,
            orderId: $this->selectedTable->currentOrder->id
        );
        $this->closeTableModal();
    }

    public function viewOrder()
    {
        if (!$this->selectedTable || !$this->selectedTable->currentOrder) return;

        $this->dispatch('view-order', orderId: $this->selectedTable->currentOrder->id);
        $this->closeTableModal();
    }

    public function printBill()
    {
        if (!$this->selectedTable || !$this->selectedTable->currentOrder) return;

        $this->dispatch('print-bill', orderId: $this->selectedTable->currentOrder->id);
    }

    public function goToPayment()
    {
        if (!$this->selectedTable || !$this->selectedTable->currentOrder) return;

        $this->dispatch('open-payment-modal', orderId: $this->selectedTable->currentOrder->id);
        $this->closeTableModal();
    }

    public function transferTable()
    {
        // Open transfer modal
        $this->dispatch('open-transfer-modal', tableId: $this->selectedTableId);
    }

    public function mergeTable()
    {
        // Open merge modal
        $this->dispatch('open-merge-modal', tableId: $this->selectedTableId);
    }

    public function splitOrder()
    {
        if (!$this->selectedTable || !$this->selectedTable->currentOrder) return;

        $this->dispatch('open-split-modal', orderId: $this->selectedTable->currentOrder->id);
    }

    public function cleanTable()
    {
        if (!$this->selectedTable) return;

        $this->selectedTable->update(['status' => TableStatus::FREE->value]);
        $this->dispatch('notify', type: 'success', message: 'Mesa liberada');
        $this->closeTableModal();
    }

    public function markForCleaning()
    {
        if (!$this->selectedTable) return;

        $this->selectedTable->update(['status' => TableStatus::CLEANING->value]);
        $this->dispatch('notify', type: 'info', message: 'Mesa marcada para limpieza');
        $this->closeTableModal();
    }

    public function closeTableModal()
    {
        $this->showTableModal = false;
        $this->selectedTableId = null;
        $this->selectedTable = null;
    }

    public function getTables()
    {
        if (!$this->selectedZoneId) return collect();

        return Table::where('zone_id', $this->selectedZoneId)
            ->where('is_active', true)
            ->with(['currentOrder' => fn($q) => $q->with('waiter')])
            ->orderBy('number')
            ->get();
    }

    public function getTableStats()
    {
        $branchId = session('current_branch_id');
        $tables = Table::where('branch_id', $branchId)->where('is_active', true)->get();

        return [
            'total' => $tables->count(),
            'free' => $tables->where('status', TableStatus::FREE->value)->count(),
            'occupied' => $tables->where('status', TableStatus::OCCUPIED->value)->count(),
            'reserved' => $tables->where('status', TableStatus::RESERVED->value)->count(),
            'cleaning' => $tables->where('status', TableStatus::CLEANING->value)->count(),
        ];
    }

    public function render()
    {
        return view('livewire.pos.table-map', [
            'tables' => $this->getTables(),
            'stats' => $this->getTableStats(),
        ]);
    }
}
