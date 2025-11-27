<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    public string $previousStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $previousStatus = '')
    {
        $this->order = $order->load(['table', 'waiter', 'items.product']);
        $this->previousStatus = $previousStatus;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('kitchen.' . $this->order->branch_id),
            new Channel('pos.' . $this->order->branch_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'table' => $this->order->table ? [
                'id' => $this->order->table->id,
                'number' => $this->order->table->number,
            ] : null,
            'waiter' => $this->order->waiter ? [
                'id' => $this->order->waiter->id,
                'name' => $this->order->waiter->name,
            ] : null,
            'items' => $this->order->items->map(fn($item) => [
                'id' => $item->id,
                'product_name' => $item->product->name ?? 'N/A',
                'quantity' => $item->quantity,
                'status' => $item->status ?? 'pending',
                'notes' => $item->notes,
            ]),
            'status' => $this->order->status->value,
            'previous_status' => $this->previousStatus,
            'type' => $this->order->type->value,
            'total' => $this->order->total,
            'updated_at' => $this->order->updated_at->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.updated';
    }
}
