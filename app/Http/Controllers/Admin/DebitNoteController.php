<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DebitNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // TODO: Implementar cuando se cree el modelo DebitNote
        // Por ahora retornamos una colección vacía
        $debitNotes = new Collection();

        return view('admin.debit-notes.index', compact('debitNotes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener facturas que puedan ser referenciadas para notas débito
        $invoices = Invoice::where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return view('admin.debit-notes.create', compact('invoices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implementar cuando se cree el modelo DebitNote
        return redirect()->route('admin.debit-notes.index')
            ->with('info', 'Funcionalidad de notas débito próximamente disponible');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // TODO: Implementar cuando se cree el modelo DebitNote
        // Por ahora creamos un objeto temporal para mostrar la vista
        $debitNote = (object) [
            'id' => $id,
            'number' => 'ND-' . str_pad($id, 6, '0', STR_PAD_LEFT),
            'invoice_id' => null,
            'invoice' => null,
            'customer' => null,
            'reason' => 'Nota débito de prueba',
            'items' => collect([]),
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
            'status' => 'pending',
            'dian_uuid' => null,
            'dian_validated_at' => null,
            'created_at' => now(),
        ];

        return view('admin.debit-notes.show', compact('debitNote'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
