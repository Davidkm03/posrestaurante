<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Zone;
use App\Enums\TableStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        $zones = Zone::where('branch_id', $branchId)
            ->with(['tables' => function ($query) {
                $query->orderBy('number');
            }])
            ->orderBy('name')
            ->get();

        return view('admin.tables.index', compact('zones'));
    }

    public function create()
    {
        $branchId = session('current_branch_id');
        $zones = Zone::where('branch_id', $branchId)->orderBy('name')->get();
        $statuses = TableStatus::cases();

        return view('admin.tables.create', compact('zones', 'statuses'));
    }

    public function store(Request $request)
    {
        $branchId = session('current_branch_id');

        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'number' => [
                'required',
                'string',
                'max:10',
                Rule::unique('tables')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                }),
            ],
            'capacity' => 'required|integer|min:1|max:50',
            'position_x' => 'nullable|integer|min:0',
            'position_y' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['branch_id'] = $branchId;
        $validated['status'] = TableStatus::FREE->value;
        $validated['is_active'] = $request->boolean('is_active', true);

        Table::create($validated);

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Mesa creada exitosamente.');
    }

    public function edit(Table $table)
    {
        $branchId = session('current_branch_id');
        $zones = Zone::where('branch_id', $branchId)->orderBy('name')->get();
        $statuses = TableStatus::cases();

        return view('admin.tables.edit', compact('table', 'zones', 'statuses'));
    }

    public function update(Request $request, Table $table)
    {
        $branchId = session('current_branch_id');

        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'number' => [
                'required',
                'string',
                'max:10',
                Rule::unique('tables')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                })->ignore($table->id),
            ],
            'capacity' => 'required|integer|min:1|max:50',
            'position_x' => 'nullable|integer|min:0',
            'position_y' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $table->update($validated);

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Mesa actualizada exitosamente.');
    }

    public function destroy(Table $table)
    {
        if ($table->orders()->whereNotIn('status', ['paid', 'cancelled', 'voided'])->exists()) {
            return back()->with('error', 'No se puede eliminar la mesa porque tiene órdenes activas.');
        }

        $table->delete();

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Mesa eliminada exitosamente.');
    }

    public function updatePosition(Request $request, Table $table)
    {
        $request->validate([
            'position_x' => 'required|integer|min:0',
            'position_y' => 'required|integer|min:0',
        ]);

        $table->update([
            'position_x' => $request->position_x,
            'position_y' => $request->position_y,
        ]);

        return response()->json(['message' => 'Posición actualizada']);
    }

    // Zonas
    public function createZone()
    {
        return view('admin.tables.create-zone');
    }

    public function storeZone(Request $request)
    {
        $branchId = session('current_branch_id');

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        $validated['branch_id'] = $branchId;
        $validated['is_active'] = $request->boolean('is_active', true);

        Zone::create($validated);

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Zona creada exitosamente.');
    }

    public function editZone(Zone $zone)
    {
        return view('admin.tables.edit-zone', compact('zone'));
    }

    public function updateZone(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $zone->update($validated);

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Zona actualizada exitosamente.');
    }

    public function destroyZone(Zone $zone)
    {
        if ($zone->tables()->exists()) {
            return back()->with('error', 'No se puede eliminar la zona porque tiene mesas asociadas.');
        }

        $zone->delete();

        return redirect()
            ->route('admin.tables.index')
            ->with('success', 'Zona eliminada exitosamente.');
    }
}
