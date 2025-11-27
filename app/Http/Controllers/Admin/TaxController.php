<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = Tax::orderBy('name')->get();
        return view('admin.settings.taxes', compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $tax = null;
        
        // Manejar presets de Colombia
        $presets = [
            'iva19' => ['name' => 'IVA 19%', 'code' => 'IVA19', 'dian_code' => '01', 'percentage' => 19],
            'iva5' => ['name' => 'IVA 5%', 'code' => 'IVA5', 'dian_code' => '01', 'percentage' => 5],
            'exento' => ['name' => 'Exento', 'code' => 'EXE', 'dian_code' => '01', 'percentage' => 0],
            'impoconsumo' => ['name' => 'Impoconsumo 8%', 'code' => 'INC8', 'dian_code' => '04', 'percentage' => 8],
        ];
        
        $preset = $request->get('preset');
        if ($preset && isset($presets[$preset])) {
            $tax = (object) $presets[$preset];
            $tax->is_active = true;
        }
        
        return view('admin.settings.taxes-form', compact('tax'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'dian_code' => 'nullable|string|max:10',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Tax::create($validated);

        return redirect()->route('admin.settings.taxes')
            ->with('success', 'Impuesto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tax $tax)
    {
        return redirect()->route('admin.settings.taxes.edit', $tax);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $tax)
    {
        return view('admin.settings.taxes-form', compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'dian_code' => 'nullable|string|max:10',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $tax->update($validated);

        return redirect()->route('admin.settings.taxes')
            ->with('success', 'Impuesto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        $tax->delete();

        return redirect()->route('admin.settings.taxes')
            ->with('success', 'Impuesto eliminado exitosamente.');
    }
}
