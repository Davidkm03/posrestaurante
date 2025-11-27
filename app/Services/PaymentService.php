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

    public function processPayment(Order $order, array $paymentData): Payment
    {
        // Get or create payment method with proper type mapping
        $methodCode = $paymentData['method'];
        $typeMapping = [
            'cash' => 'cash',
            'efectivo' => 'cash',
            'card' => 'credit_card',
            'tarjeta' => 'credit_card',
            'credit_card' => 'credit_card',
            'debit_card' => 'debit_card',
            'transfer' => 'transfer',
            'transferencia' => 'transfer',
            'nequi' => 'nequi',
            'daviplata' => 'daviplata',
            'voucher' => 'voucher',
            'bono' => 'voucher',
            'credit' => 'credit',
            'credito' => 'credit',
        ];
        
        $type = $typeMapping[strtolower($methodCode)] ?? 'cash';
        
        $paymentMethod = PaymentMethod::firstOrCreate(
            ['code' => $methodCode],
            [
                'name' => ucfirst($methodCode),
                'type' => $type,
                'is_active' => true,
            ]
        );

        return Payment::create([
            'order_id' => $order->id,
            'cash_session_id' => session('cash_session_id'),
            'user_id' => auth()->id(),
            'payment_method_id' => $paymentMethod->id,
            'amount' => $paymentData['amount'],
            'received_amount' => $paymentData['amount'],
            'change_amount' => 0,
            'reference' => $paymentData['reference'] ?? null,
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    public function completeOrder(Order $order, array $payments): Order
    {
        $totalPayment = collect($payments)->sum('amount');
        $totalRequired = $order->total;

        if ($totalPayment < $totalRequired) {
            throw new \InvalidArgumentException(
                "Pago insuficiente. Requerido: {$totalRequired}, Recibido: {$totalPayment}"
            );
        }

        return DB::transaction(function () use ($order, $payments, $totalPayment, $totalRequired) {
            // Register payments
            foreach ($payments as $paymentData) {
                $this->processPayment($order, $paymentData);
            }

            // Calculate change
            $change = $totalPayment - $totalRequired;

            // Complete order
            $order->update([
                'status' => OrderStatus::PAID->value,
                'payment_status' => PaymentStatus::COMPLETED->value,
                'completed_at' => now(),
                'paid_amount' => $totalPayment,
            ]);

            // Free table
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update([
                    'status' => TableStatus::FREE->value,
                    'current_order_id' => null,
                ]);
            }

            return $order->fresh(['payments']);
        });
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
            ->with('paymentMethod')
            ->get()
            ->groupBy('payment_method_id')
            ->map(function ($payments) {
                return [
                    'method' => $payments->first()->paymentMethod->name ?? 'Desconocido',
                    'count' => $payments->count(),
                    'total' => $payments->sum('amount'),
                ];
            });
    }
}
