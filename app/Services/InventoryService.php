<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Order;
use App\Enums\StockMovementType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryService
{
    /**
     * Registra un movimiento de inventario y actualiza el stock del producto
     */
    public function recordMovement(
        int $branchId,
        int $productId,
        StockMovementType $type,
        float $quantity,
        ?float $unitCost = null,
        ?string $notes = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): StockMovement {
        return DB::transaction(function () use ($branchId, $productId, $type, $quantity, $unitCost, $notes, $referenceType, $referenceId) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            $stockBefore = $product->stock;
            $quantityChange = $type->isIncoming() ? $quantity : -$quantity;
            $stockAfter = $stockBefore + $quantityChange;

            // No permitir stock negativo si track_inventory está activo
            if ($product->track_inventory && $stockAfter < 0) {
                throw new \Exception("Stock insuficiente. Stock actual: {$stockBefore}, cantidad solicitada: {$quantity}");
            }

            // Actualizar stock del producto
            $product->stock = $stockAfter;
            $product->save();

            // Registrar movimiento
            return StockMovement::create([
                'branch_id' => $branchId,
                'product_id' => $productId,
                'user_id' => Auth::id(),
                'type' => $type->value,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $unitCost ? $unitCost * $quantity : null,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Registra una venta (salida de inventario)
     */
    public function recordSale(int $branchId, int $productId, float $quantity, ?int $orderId = null): StockMovement
    {
        return $this->recordMovement(
            $branchId,
            $productId,
            StockMovementType::SALE,
            $quantity,
            null,
            null,
            $orderId ? Order::class : null,
            $orderId
        );
    }

    /**
     * Registra una compra (entrada de inventario)
     */
    public function recordPurchase(int $branchId, int $productId, float $quantity, float $unitCost, ?int $purchaseId = null): StockMovement
    {
        return $this->recordMovement(
            $branchId,
            $productId,
            StockMovementType::PURCHASE,
            $quantity,
            $unitCost,
            null,
            $purchaseId ? Purchase::class : null,
            $purchaseId
        );
    }

    /**
     * Ajuste de inventario (entrada o salida)
     */
    public function adjustStock(int $branchId, int $productId, float $newStock, string $notes = null): StockMovement
    {
        $product = Product::findOrFail($productId);
        $currentStock = $product->stock;
        $difference = $newStock - $currentStock;

        $type = $difference >= 0 ? StockMovementType::ADJUSTMENT_IN : StockMovementType::ADJUSTMENT_OUT;

        return $this->recordMovement(
            $branchId,
            $productId,
            $type,
            abs($difference),
            null,
            $notes ?? "Ajuste de inventario: {$currentStock} → {$newStock}"
        );
    }

    /**
     * Registra merma/desperdicio
     */
    public function recordWaste(int $branchId, int $productId, float $quantity, string $notes = null): StockMovement
    {
        return $this->recordMovement(
            $branchId,
            $productId,
            StockMovementType::WASTE,
            $quantity,
            null,
            $notes ?? 'Merma/Desperdicio'
        );
    }

    /**
     * Registra consumo interno
     */
    public function recordInternalUse(int $branchId, int $productId, float $quantity, string $notes = null): StockMovement
    {
        return $this->recordMovement(
            $branchId,
            $productId,
            StockMovementType::INTERNAL_USE,
            $quantity,
            null,
            $notes ?? 'Consumo interno'
        );
    }

    /**
     * Transferencia entre sucursales
     */
    public function transferStock(
        int $fromBranchId,
        int $toBranchId,
        int $productId,
        float $quantity,
        string $notes = null
    ): array {
        return DB::transaction(function () use ($fromBranchId, $toBranchId, $productId, $quantity, $notes) {
            // Salida en origen
            $outMovement = $this->recordMovement(
                $fromBranchId,
                $productId,
                StockMovementType::TRANSFER_OUT,
                $quantity,
                null,
                $notes ?? "Transferencia a sucursal {$toBranchId}"
            );

            // Entrada en destino
            $inMovement = $this->recordMovement(
                $toBranchId,
                $productId,
                StockMovementType::TRANSFER_IN,
                $quantity,
                null,
                $notes ?? "Transferencia desde sucursal {$fromBranchId}"
            );

            return ['out' => $outMovement, 'in' => $inMovement];
        });
    }

    /**
     * Obtener productos con stock bajo
     */
    public function getLowStockProducts(?int $branchId = null)
    {
        return Product::where('track_inventory', true)
            ->where('is_active', true)
            ->whereRaw('stock <= min_stock')
            ->with('category')
            ->orderBy('stock')
            ->get();
    }

    /**
     * Obtener historial de movimientos de un producto
     */
    public function getProductHistory(int $productId, ?int $branchId = null, int $limit = 50)
    {
        $query = StockMovement::where('product_id', $productId)
            ->with(['user', 'branch'])
            ->orderByDesc('created_at');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Resumen de inventario por categoría
     */
    public function getInventorySummary(?int $branchId = null)
    {
        return Product::where('track_inventory', true)
            ->where('is_active', true)
            ->with('category')
            ->selectRaw('category_id, SUM(stock) as total_stock, SUM(stock * cost) as total_value, COUNT(*) as products_count')
            ->groupBy('category_id')
            ->get();
    }

    /**
     * Valorización del inventario
     */
    public function getInventoryValuation(?int $branchId = null)
    {
        return Product::where('track_inventory', true)
            ->where('is_active', true)
            ->selectRaw('SUM(stock * cost) as total_cost, SUM(stock * price) as total_sale_value, COUNT(*) as total_products')
            ->first();
    }
}
