<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;

class KitchenDisplayController extends Controller
{
    public function index()
    {
        // Retornar la vista que usa el componente Livewire para actualizaciones automáticas
        return view('kitchen.display.livewire');
    }

    public function updateStatus(Order $order, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:in_preparation,ready',
        ]);

        $statusMap = [
            'in_preparation' => OrderStatus::IN_PREPARATION,
            'ready' => OrderStatus::READY,
        ];

        $order->update([
            'status' => $statusMap[$validated['status']]->value,
        ]);

        return back()->with('success', 'Estado actualizado correctamente');
    }

    public function markItemReady(Order $order, $itemId)
    {
        $item = $order->items()->findOrFail($itemId);
        
        $item->update([
            'is_ready' => true,
        ]);

        // Si todos los items están listos, marcar orden como lista
        if ($order->items()->where('is_ready', false)->count() === 0) {
            $order->update([
                'status' => OrderStatus::READY->value,
            ]);
        }

        return back()->with('success', 'Item marcado como listo');
    }
}
