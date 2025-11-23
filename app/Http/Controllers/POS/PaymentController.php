<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Table;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TableStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function show(Order $order)
    {
        if ($order->status === OrderStatus::PAID->value) {
            return redirect()->route('pos.index')->with('info', 'Esta orden ya fue pagada.');
        }

        $order->load(['items.product', 'table', 'customer', 'payments.method']);

        $paymentMethods = PaymentMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $totalPaid = $order->payments->where('status', PaymentStatus::COMPLETED->value)->sum('amount');
        $remaining = $order->total - $totalPaid;

        return view('pos.payment', compact('order', 'paymentMethods', 'totalPaid', 'remaining'));
    }

    public function process(Request $request, Order $order)
    {
        if ($order->status === OrderStatus::PAID->value) {
            return response()->json(['error' => 'Esta orden ya fue pagada'], 400);
        }

        $validated = $request->validate([
            'payments' => 'required|array|min:1',
            'payments.*.payment_method_id' => 'required|exists:payment_methods,id',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.reference' => 'nullable|string|max:100',
            'tip' => 'nullable|numeric|min:0',
            'generate_invoice' => 'boolean',
        ]);

        $totalPayment = collect($validated['payments'])->sum('amount');
        $tip = $validated['tip'] ?? 0;
        $totalWithTip = $order->total + $tip;

        if ($totalPayment < $totalWithTip) {
            return response()->json([
                'error' => 'El monto del pago es insuficiente',
                'required' => $totalWithTip,
                'received' => $totalPayment,
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Actualizar propina
            if ($tip > 0) {
                $order->update(['tip' => $tip]);
            }

            // Registrar pagos
            foreach ($validated['payments'] as $paymentData) {
                $paymentMethod = PaymentMethod::find($paymentData['payment_method_id']);

                Payment::create([
                    'order_id' => $order->id,
                    'payment_method_id' => $paymentData['payment_method_id'],
                    'amount' => $paymentData['amount'],
                    'reference' => $paymentData['reference'] ?? null,
                    'status' => PaymentStatus::COMPLETED->value,
                    'processed_at' => now(),
                    'processed_by' => auth()->id(),
                ]);
            }

            // Calcular cambio
            $change = $totalPayment - $totalWithTip;

            // Actualizar orden
            $order->update([
                'status' => OrderStatus::PAID->value,
                'paid_at' => now(),
                'cashier_id' => auth()->id(),
                'change_amount' => $change,
            ]);

            // Liberar mesa si aplica
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update([
                    'status' => TableStatus::FREE->value,
                    'current_order_id' => null,
                ]);
            }

            // Generar factura si se solicitó
            $invoice = null;
            if ($request->boolean('generate_invoice') && $order->customer_id) {
                // Aquí se generaría la factura electrónica
                // $invoice = app(InvoiceService::class)->generate($order);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order->fresh(['payments.method']),
                'change' => $change,
                'invoice' => $invoice,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el pago: ' . $e->getMessage()], 500);
        }
    }

    public function split(Request $request, Order $order)
    {
        if ($order->status === OrderStatus::PAID->value) {
            return response()->json(['error' => 'Esta orden ya fue pagada'], 400);
        }

        $validated = $request->validate([
            'split_type' => 'required|in:equal,custom,items',
            'parts' => 'required_if:split_type,equal|integer|min:2|max:20',
            'amounts' => 'required_if:split_type,custom|array',
            'item_groups' => 'required_if:split_type,items|array',
        ]);

        $splits = [];

        switch ($validated['split_type']) {
            case 'equal':
                $amountPerPart = round($order->total / $validated['parts'], 2);
                $remainder = $order->total - ($amountPerPart * $validated['parts']);

                for ($i = 0; $i < $validated['parts']; $i++) {
                    $amount = $amountPerPart;
                    if ($i === 0) {
                        $amount += $remainder; // Agregar residuo al primer pago
                    }
                    $splits[] = [
                        'part' => $i + 1,
                        'amount' => $amount,
                        'paid' => false,
                    ];
                }
                break;

            case 'custom':
                $total = array_sum($validated['amounts']);
                if (abs($total - $order->total) > 0.01) {
                    return response()->json(['error' => 'La suma de los montos no coincide con el total'], 400);
                }

                foreach ($validated['amounts'] as $i => $amount) {
                    $splits[] = [
                        'part' => $i + 1,
                        'amount' => $amount,
                        'paid' => false,
                    ];
                }
                break;

            case 'items':
                // División por items específicos
                foreach ($validated['item_groups'] as $i => $itemIds) {
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

        return response()->json([
            'success' => true,
            'splits' => $splits,
            'order_total' => $order->total,
        ]);
    }

    public function processSplitPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'part' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'reference' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            Payment::create([
                'order_id' => $order->id,
                'payment_method_id' => $validated['payment_method_id'],
                'amount' => $validated['amount'],
                'reference' => $validated['reference'] ?? null,
                'status' => PaymentStatus::COMPLETED->value,
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'metadata' => ['split_part' => $validated['part']],
            ]);

            // Verificar si se completó el pago total
            $totalPaid = $order->payments()
                ->where('status', PaymentStatus::COMPLETED->value)
                ->sum('amount');

            if ($totalPaid >= $order->total) {
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

            DB::commit();

            return response()->json([
                'success' => true,
                'total_paid' => $totalPaid,
                'remaining' => max(0, $order->total - $totalPaid),
                'is_complete' => $totalPaid >= $order->total,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el pago: ' . $e->getMessage()], 500);
        }
    }

    public function receipt(Order $order)
    {
        $order->load(['items.product', 'payments.method', 'customer', 'branch', 'waiter', 'cashier']);

        return view('pos.receipt', compact('order'));
    }
}
