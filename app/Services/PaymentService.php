<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Table;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TableStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PaymentService
{
    public function __construct(
        protected OrderService $orderService,
        protected InvoiceService $invoiceService
    ) {}

    public function processPayment(Order $order, array $payments, float $tip = 0, bool $generateInvoice = false): array
    {
        $totalPayment = collect($payments)->sum('amount');
        $totalRequired = $order->total + $tip;

        if ($totalPayment < $totalRequired) {
            throw new \InvalidArgumentException(
                "Pago insuficiente. Requerido: {$totalRequired}, Recibido: {$totalPayment}"
            );
        }

        return DB::transaction(function () use ($order, $payments, $tip, $generateInvoice, $totalPayment, $totalRequired) {
            // Update tip
            if ($tip > 0) {
                $order->update(['tip' => $tip]);
            }

            // Register payments
            foreach ($payments as $paymentData) {
                $this->createPayment($order, $paymentData);
            }

            // Calculate change
            $change = $totalPayment - $totalRequired;

            // Complete order
            $order->update([
                'status' => OrderStatus::PAID->value,
                'paid_at' => now(),
                'cashier_id' => auth()->id(),
                'change_amount' => $change,
            ]);

            // Free table
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update([
                    'status' => TableStatus::FREE->value,
                    'current_order_id' => null,
                ]);
            }

            // Generate invoice
            $invoice = null;
            if ($generateInvoice && $order->customer_id) {
                $invoice = $this->invoiceService->generateFromOrder($order);
            }

            return [
                'success' => true,
                'order' => $order->fresh(['payments.method']),
                'change' => $change,
                'invoice' => $invoice,
            ];
        });
    }

    public function createPayment(Order $order, array $data): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $data['payment_method_id'],
            'amount' => $data['amount'],
            'reference' => $data['reference'] ?? null,
            'status' => PaymentStatus::COMPLETED->value,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);
    }

    public function splitPayment(Order $order, string $splitType, array $params): array
    {
        $splits = [];

        switch ($splitType) {
            case 'equal':
                $parts = $params['parts'];
                $amountPerPart = round($order->total / $parts, 2);
                $remainder = $order->total - ($amountPerPart * $parts);

                for ($i = 0; $i < $parts; $i++) {
                    $amount = $amountPerPart + ($i === 0 ? $remainder : 0);
                    $splits[] = [
                        'part' => $i + 1,
                        'amount' => $amount,
                        'paid' => false,
                    ];
                }
                break;

            case 'custom':
                $amounts = $params['amounts'];
                $total = array_sum($amounts);

                if (abs($total - $order->total) > 0.01) {
                    throw new \InvalidArgumentException('La suma de montos no coincide con el total');
                }

                foreach ($amounts as $i => $amount) {
                    $splits[] = [
                        'part' => $i + 1,
                        'amount' => $amount,
                        'paid' => false,
                    ];
                }
                break;

            case 'items':
                $itemGroups = $params['item_groups'];

                foreach ($itemGroups as $i => $itemIds) {
                    $amount = $order->items()
                        ->whereIn('id', $itemIds)
                        ->sum('subtotal');

                    $splits[] = [
                        'part' => $i + 1,
                        'amount' => $amount,
                        'items' => $itemIds,
                        'paid' => false,
                    ];
                }
                break;
        }

        return $splits;
    }

    public function processSplitPayment(Order $order, array $paymentData): array
    {
        return DB::transaction(function () use ($order, $paymentData) {
            $this->createPayment($order, [
                'payment_method_id' => $paymentData['payment_method_id'],
                'amount' => $paymentData['amount'],
                'reference' => $paymentData['reference'] ?? null,
            ]);

            $totalPaid = $order->payments()
                ->where('status', PaymentStatus::COMPLETED->value)
                ->sum('amount');

            $isComplete = $totalPaid >= $order->total;

            if ($isComplete) {
                $order->update([
                    'status' => OrderStatus::PAID->value,
                    'paid_at' => now(),
                    'cashier_id' => auth()->id(),
                    'change_amount' => $totalPaid - $order->total,
                ]);

                if ($order->table_id) {
                    Table::where('id', $order->table_id)->update([
                        'status' => TableStatus::FREE->value,
                        'current_order_id' => null,
                    ]);
                }
            }

            return [
                'success' => true,
                'total_paid' => $totalPaid,
                'remaining' => max(0, $order->total - $totalPaid),
                'is_complete' => $isComplete,
            ];
        });
    }

    public function refund(Payment $payment, float $amount, string $reason): Payment
    {
        if ($amount > $payment->amount) {
            throw new \InvalidArgumentException('El monto del reembolso excede el pago original');
        }

        return DB::transaction(function () use ($payment, $amount, $reason) {
            // Create refund payment (negative)
            $refund = Payment::create([
                'order_id' => $payment->order_id,
                'payment_method_id' => $payment->payment_method_id,
                'amount' => -$amount,
                'reference' => "Reembolso: {$reason}",
                'status' => PaymentStatus::REFUNDED->value,
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'metadata' => [
                    'original_payment_id' => $payment->id,
                    'reason' => $reason,
                ],
            ]);

            // Update original payment status if fully refunded
            if ($amount >= $payment->amount) {
                $payment->update(['status' => PaymentStatus::REFUNDED->value]);
            }

            return $refund;
        });
    }

    public function getPaymentSummary(int $branchId, string $startDate, string $endDate): Collection
    {
        return Payment::whereHas('order', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
            ->where('status', PaymentStatus::COMPLETED->value)
            ->whereBetween('processed_at', [$startDate, $endDate . ' 23:59:59'])
            ->with('method')
            ->get()
            ->groupBy('payment_method_id')
            ->map(function ($payments) {
                return [
                    'method' => $payments->first()->method->name ?? 'Desconocido',
                    'count' => $payments->count(),
                    'total' => $payments->sum('amount'),
                ];
            });
    }
}
