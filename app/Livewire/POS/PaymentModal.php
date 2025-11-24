<?php

namespace App\Livewire\POS;

use App\Models\Order;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Services\PaymentService;
use App\Enums\PaymentMethod as PaymentMethodEnum;
use App\Enums\InvoiceType;
use Livewire\Component;
use Livewire\Attributes\On;

class PaymentModal extends Component
{
    public bool $isOpen = false;
    public ?int $orderId = null;
    public ?Order $order = null;

    // Payment
    public array $payments = [];
    public string $selectedMethod = 'cash';
    public float $paymentAmount = 0;
    public string $reference = '';

    // Customer & Invoice
    public ?int $customerId = null;
    public ?Customer $customer = null;
    public string $invoiceType = 'pos';
    public bool $showCustomerSearch = false;
    public string $customerSearch = '';
    public array $customerResults = [];

    // Calculated
    public float $totalPaid = 0;
    public float $remaining = 0;
    public float $change = 0;

    // Quick amounts
    public array $quickAmounts = [1000, 2000, 5000, 10000, 20000, 50000, 100000];

    protected $listeners = ['open-payment-modal' => 'open'];

    #[On('open-payment-modal')]
    public function open(int $orderId)
    {
        $this->orderId = $orderId;
        $this->loadOrder();
        $this->isOpen = true;
    }

    public function loadOrder()
    {
        $this->order = Order::with(['items', 'customer'])->find($this->orderId);

        if (!$this->order) return;

        $this->customer = $this->order->customer;
        $this->customerId = $this->order->customer_id;
        $this->paymentAmount = $this->order->total;
        $this->payments = [];
        $this->recalculate();
    }

    public function selectPaymentMethod(string $method)
    {
        $this->selectedMethod = $method;
        $this->reference = '';

        if ($method === 'cash') {
            $this->paymentAmount = $this->remaining > 0 ? $this->remaining : $this->order->total;
        } else {
            $this->paymentAmount = $this->remaining > 0 ? $this->remaining : $this->order->total;
        }
    }

    public function setQuickAmount(float $amount)
    {
        $this->paymentAmount = $amount;
    }

    public function setExactAmount()
    {
        $this->paymentAmount = $this->remaining > 0 ? $this->remaining : $this->order->total;
    }

    public function addPayment()
    {
        if ($this->paymentAmount <= 0) {
            $this->dispatch('notify', type: 'error', message: 'Ingrese un monto válido');
            return;
        }

        // Validate reference for non-cash methods
        if ($this->selectedMethod !== 'cash' && empty($this->reference)) {
            $this->dispatch('notify', type: 'error', message: 'Ingrese la referencia');
            return;
        }

        $this->payments[] = [
            'method' => $this->selectedMethod,
            'amount' => $this->paymentAmount,
            'reference' => $this->reference,
        ];

        $this->paymentAmount = 0;
        $this->reference = '';
        $this->recalculate();
    }

    public function removePayment(int $index)
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments);
        $this->recalculate();
    }

    public function recalculate()
    {
        if (!$this->order) return;

        $this->totalPaid = array_sum(array_column($this->payments, 'amount'));
        $this->remaining = max(0, $this->order->total - $this->totalPaid);
        $this->change = max(0, $this->totalPaid - $this->order->total);
    }

    public function searchCustomer()
    {
        if (strlen($this->customerSearch) < 2) {
            $this->customerResults = [];
            return;
        }

        $this->customerResults = Customer::where('name', 'like', "%{$this->customerSearch}%")
            ->orWhere('document_number', 'like', "%{$this->customerSearch}%")
            ->orWhere('phone', 'like', "%{$this->customerSearch}%")
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function selectCustomer(int $customerId)
    {
        $this->customer = Customer::find($customerId);
        $this->customerId = $customerId;
        $this->showCustomerSearch = false;
        $this->customerSearch = '';
        $this->customerResults = [];

        // Update invoice type based on customer
        if ($this->customer && $this->customer->document_type !== 'consumidor_final') {
            $this->invoiceType = 'electronic';
        }
    }

    public function removeCustomer()
    {
        $this->customer = null;
        $this->customerId = null;
        $this->invoiceType = 'pos';
    }

    public function processPayment()
    {
        if (empty($this->payments)) {
            $this->dispatch('notify', type: 'error', message: 'Agregue al menos un pago');
            return;
        }

        if ($this->remaining > 0) {
            $this->dispatch('notify', type: 'error', message: 'El monto pagado es insuficiente');
            return;
        }

        // Validate electronic invoice requires customer
        if ($this->invoiceType === 'electronic' && !$this->customerId) {
            $this->dispatch('notify', type: 'error', message: 'Factura electrónica requiere cliente');
            return;
        }

        try {
            $paymentService = app(PaymentService::class);

            // Update customer if changed
            if ($this->customerId !== $this->order->customer_id) {
                $this->order->update(['customer_id' => $this->customerId]);
            }

            // Process each payment
            foreach ($this->payments as $payment) {
                $paymentService->processPayment($this->order, [
                    'method' => $payment['method'],
                    'amount' => $payment['amount'],
                    'reference' => $payment['reference'],
                ]);
            }

            // Generate invoice if needed
            if ($this->invoiceType === 'electronic') {
                $invoiceService = app(\App\Services\InvoiceService::class);
                $invoiceService->generate($this->order);
            }

            $this->dispatch('notify', type: 'success', message: 'Pago procesado correctamente');
            $this->dispatch('payment-completed', orderId: $this->orderId, change: $this->change);
            $this->close();

        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function close()
    {
        $this->isOpen = false;
        $this->reset(['orderId', 'order', 'payments', 'customerId', 'customer', 'invoiceType']);
    }

    public function render()
    {
        return view('livewire.pos.payment-modal');
    }
}
