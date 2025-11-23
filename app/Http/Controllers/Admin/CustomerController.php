<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Enums\CustomerType;
use App\Enums\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('orders')
            ->withSum('orders', 'total');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('customer_type', $request->type);
        }

        $customers = $query->latest()->paginate(20);
        $customerTypes = CustomerType::cases();

        return view('admin.customers.index', compact('customers', 'customerTypes'));
    }

    public function create()
    {
        $customerTypes = CustomerType::cases();
        $documentTypes = DocumentType::cases();

        return view('admin.customers.create', compact('customerTypes', 'documentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'customer_type' => 'required|in:' . implode(',', array_column(CustomerType::cases(), 'value')),
            'document_type' => 'required|in:' . implode(',', array_column(DocumentType::cases(), 'value')),
            'document_number' => 'required|string|max:20|unique:customers,document_number',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            // Campos empresa
            'business_name' => 'nullable|required_if:customer_type,business|string|max:255',
            'tax_regime' => 'nullable|string|max:100',
            'tax_responsibilities' => 'nullable|string|max:255',
        ]);

        Customer::create($validated);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders' => function ($query) {
            $query->with(['items.product', 'payments'])->latest()->limit(20);
        }]);

        $stats = [
            'total_orders' => $customer->orders()->count(),
            'total_spent' => $customer->orders()->sum('total'),
            'avg_ticket' => $customer->orders()->avg('total') ?? 0,
            'last_order' => $customer->orders()->latest()->first()?->created_at,
        ];

        return view('admin.customers.show', compact('customer', 'stats'));
    }

    public function edit(Customer $customer)
    {
        $customerTypes = CustomerType::cases();
        $documentTypes = DocumentType::cases();

        return view('admin.customers.edit', compact('customer', 'customerTypes', 'documentTypes'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'customer_type' => 'required|in:' . implode(',', array_column(CustomerType::cases(), 'value')),
            'document_type' => 'required|in:' . implode(',', array_column(DocumentType::cases(), 'value')),
            'document_number' => ['required', 'string', 'max:20', Rule::unique('customers')->ignore($customer->id)],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'business_name' => 'nullable|string|max:255',
            'tax_regime' => 'nullable|string|max:100',
            'tax_responsibilities' => 'nullable|string|max:255',
        ]);

        $customer->update($validated);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->orders()->exists()) {
            return back()->with('error', 'No se puede eliminar el cliente porque tiene órdenes asociadas.');
        }

        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }

    public function search(Request $request)
    {
        $search = $request->get('q', '');

        $customers = Customer::where('name', 'like', "%{$search}%")
            ->orWhere('document_number', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'document_type', 'document_number', 'phone', 'email']);

        return response()->json($customers);
    }
}
