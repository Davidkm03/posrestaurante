<?php

namespace App\Events;

use App\Models\OrderItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderItemReady implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public OrderItem $item;

    /**
     * Create a new event instance.
     */
    public function __construct(OrderItem $item)
    {
        $this->item = $item->load(['order.table', 'product']);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('pos.' . $this->item->order->branch_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'item_id' => $this->item->id,
            'order_id' => $this->item->order_id,
            'order_number' => $this->item->order->order_number,
            'product_name' => $this->item->product->name ?? 'N/A',
            'table' => $this->item->order->table?->number ?? 'N/A',
            'quantity' => $this->item->quantity,
            'ready_at' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'item.ready';
    }
}
