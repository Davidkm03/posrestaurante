<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Product $product;
    public int $currentStock;
    public int $minStock;

    /**
     * Create a new event instance.
     */
    public function __construct(Product $product, int $currentStock, int $minStock)
    {
        $this->product = $product;
        $this->currentStock = $currentStock;
        $this->minStock = $minStock;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('inventory.' . $this->product->branch_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'current_stock' => $this->currentStock,
            'min_stock' => $this->minStock,
            'category' => $this->product->category?->name ?? 'Sin categoría',
            'alert_type' => $this->currentStock <= 0 ? 'out_of_stock' : 'low_stock',
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'stock.low';
    }
}
