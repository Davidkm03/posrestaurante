<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cliente creado exitosamente',
            'data' => $customer,
        ], 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        $customer->loadCount('orders');
        $customer->loadSum('orders', 'total');

        return response()->json([
            'success' => true,
            'data' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado',
            'data' => $customer,
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $customers = Customer::where('name', 'like', "%{$request->q}%")
            ->orWhere('document_number', 'like', "%{$request->q}%")
            ->orWhere('phone', 'like', "%{$request->q}%")
            ->limit(10)
            ->get(['id', 'name', 'document_type', 'document_number', 'phone', 'email']);

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }

    public function byDocument(string $document): JsonResponse
    {
        $customer = Customer::where('document_number', $document)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $customer,
        ]);
    }

    public function orders(Customer $customer): JsonResponse
    {
        $orders = $customer->orders()
            ->with(['items.product', 'payments'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }
}
