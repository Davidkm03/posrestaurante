<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branchId = session('current_branch_id');
        $zones = Zone::where('branch_id', $branchId)
            ->withCount('tables')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.zones.index', compact('zones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.zones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['branch_id'] = session('current_branch_id');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Zone::create($validated);

        return redirect()->route('admin.zones.index')
            ->with('success', 'Zona creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Zone $zone)
    {
        $zone->load('tables');
        return view('admin.zones.show', compact('zone'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Zone $zone)
    {
        return view('admin.zones.edit', compact('zone'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $zone->update($validated);

        return redirect()->route('admin.zones.index')
            ->with('success', 'Zona actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Zone $zone)
    {
        if ($zone->tables()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una zona que tiene mesas asignadas');
        }

        $zone->delete();

        return redirect()->route('admin.zones.index')
            ->with('success', 'Zona eliminada correctamente');
    }
}
