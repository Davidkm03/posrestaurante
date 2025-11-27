<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BranchController extends Controller
{
    /**
     * Lista de sucursales
     */
    public function index()
    {
        $branches = Branch::withCount(['users', 'orders', 'tables', 'zones'])
            ->orderBy('is_main', 'desc')
            ->orderBy('name')
            ->get();

        return view('admin.settings.branches.index', compact('branches'));
    }

    /**
     * Formulario de nueva sucursal
     */
    public function create()
    {
        return view('admin.settings.branches.create');
    }

    /**
     * Guardar nueva sucursal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:branches,code',
            'nit' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Manejar logo
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('branches', 'public');
        }

        // Configuración por defecto
        $validated['settings'] = [
            'timezone' => 'America/Bogota',
            'currency' => 'COP',
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'tax_included' => true,
            'default_tax_rate' => 19,
            'service_charge' => 0,
            'tip_suggestion' => [10, 15, 20],
            'receipt_header' => '',
            'receipt_footer' => 'Gracias por su visita',
            'invoice_notes' => '',
            'opening_hours' => [
                'monday' => ['open' => '08:00', 'close' => '22:00', 'closed' => false],
                'tuesday' => ['open' => '08:00', 'close' => '22:00', 'closed' => false],
                'wednesday' => ['open' => '08:00', 'close' => '22:00', 'closed' => false],
                'thursday' => ['open' => '08:00', 'close' => '22:00', 'closed' => false],
                'friday' => ['open' => '08:00', 'close' => '23:00', 'closed' => false],
                'saturday' => ['open' => '08:00', 'close' => '23:00', 'closed' => false],
                'sunday' => ['open' => '09:00', 'close' => '21:00', 'closed' => false],
            ],
            'kitchen_display' => true,
            'require_table' => false,
            'auto_print_receipt' => false,
            'auto_print_kitchen' => true,
        ];

        $branch = Branch::create($validated);

        // Asignar al usuario actual
        auth()->user()->branches()->attach($branch->id, ['is_default' => false]);

        return redirect()
            ->route('admin.settings.branches')
            ->with('success', 'Sucursal creada correctamente.');
    }

    /**
     * Ver detalle de sucursal
     */
    public function show(Branch $branch)
    {
        $branch->load(['users', 'zones.tables', 'cashRegisters', 'dianResolutions']);
        
        $stats = [
            'total_orders_today' => $branch->orders()->whereDate('created_at', today())->count(),
            'revenue_today' => $branch->orders()
                ->whereDate('created_at', today())
                ->whereIn('status', ['completed', 'paid'])
                ->sum('total'),
            'active_tables' => $branch->tables()->where('status', 'occupied')->count(),
            'total_tables' => $branch->tables()->count(),
        ];

        return view('admin.settings.branches.show', compact('branch', 'stats'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Branch $branch)
    {
        $availableUsers = User::whereDoesntHave('branches', function ($q) use ($branch) {
            $q->where('branch_id', $branch->id);
        })->orderBy('name')->get();

        return view('admin.settings.branches.edit', compact('branch', 'availableUsers'));
    }

    /**
     * Actualizar sucursal
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:branches,code,' . $branch->id,
            'nit' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Manejar logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior
            if ($branch->logo) {
                Storage::disk('public')->delete($branch->logo);
            }
            $validated['logo'] = $request->file('logo')->store('branches', 'public');
        }

        $branch->update($validated);

        return redirect()
            ->route('admin.settings.branches')
            ->with('success', 'Sucursal actualizada correctamente.');
    }

    /**
     * Eliminar sucursal
     */
    public function destroy(Branch $branch)
    {
        if ($branch->is_main) {
            return back()->with('error', 'No se puede eliminar la sucursal principal.');
        }

        if ($branch->orders()->exists()) {
            return back()->with('error', 'No se puede eliminar una sucursal con órdenes asociadas.');
        }

        // Eliminar logo
        if ($branch->logo) {
            Storage::disk('public')->delete($branch->logo);
        }

        $branch->delete();

        return redirect()
            ->route('admin.settings.branches')
            ->with('success', 'Sucursal eliminada correctamente.');
    }

    /**
     * Configuración de la sucursal
     */
    public function settings(Branch $branch)
    {
        return view('admin.settings.branches.settings', compact('branch'));
    }

    /**
     * Actualizar configuración
     */
    public function updateSettings(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'timezone' => 'required|string',
            'currency' => 'required|string|size:3',
            'tax_included' => 'boolean',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'service_charge' => 'required|numeric|min:0|max:100',
            'receipt_header' => 'nullable|string|max:500',
            'receipt_footer' => 'nullable|string|max:500',
            'invoice_notes' => 'nullable|string|max:1000',
            'kitchen_display' => 'boolean',
            'require_table' => 'boolean',
            'auto_print_receipt' => 'boolean',
            'auto_print_kitchen' => 'boolean',
            'opening_hours' => 'array',
        ]);

        $settings = $branch->settings ?? [];
        $settings = array_merge($settings, $validated);
        
        $branch->update(['settings' => $settings]);

        return redirect()
            ->route('admin.settings.branches.settings', $branch)
            ->with('success', 'Configuración actualizada correctamente.');
    }

    /**
     * Gestión de usuarios de la sucursal
     */
    public function users(Branch $branch)
    {
        $branchUsers = $branch->users()->orderBy('name')->get();
        $availableUsers = User::whereDoesntHave('branches', function ($q) use ($branch) {
            $q->where('branch_id', $branch->id);
        })->orderBy('name')->get();

        return view('admin.settings.branches.users', compact('branch', 'branchUsers', 'availableUsers'));
    }

    /**
     * Asignar usuario a sucursal
     */
    public function addUser(Request $request, Branch $branch)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $branch->users()->attach($request->user_id, ['is_default' => false]);

        return back()->with('success', 'Usuario asignado a la sucursal.');
    }

    /**
     * Remover usuario de sucursal
     */
    public function removeUser(Branch $branch, User $user)
    {
        $branch->users()->detach($user->id);

        return back()->with('success', 'Usuario removido de la sucursal.');
    }

    /**
     * Cambiar de sucursal activa
     */
    public function switchBranch(Branch $branch)
    {
        // Verificar que el usuario tenga acceso a esta sucursal
        if (!auth()->user()->branches->contains($branch->id)) {
            return back()->with('error', 'No tienes acceso a esta sucursal.');
        }

        session([
            'current_branch_id' => $branch->id,
            'current_branch_name' => $branch->name,
        ]);

        // Limpiar sesión de caja al cambiar de sucursal
        session()->forget('cash_session_id');

        return back()->with('success', "Cambiado a sucursal: {$branch->name}");
    }

    /**
     * Horarios de apertura
     */
    public function hours(Branch $branch)
    {
        return view('admin.settings.branches.hours', compact('branch'));
    }

    /**
     * Actualizar horarios
     */
    public function updateHours(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'opening_hours' => 'required|array',
            'opening_hours.*.open' => 'required|date_format:H:i',
            'opening_hours.*.close' => 'required|date_format:H:i',
            'opening_hours.*.closed' => 'boolean',
        ]);

        $settings = $branch->settings ?? [];
        $settings['opening_hours'] = $validated['opening_hours'];
        
        $branch->update(['settings' => $settings]);

        return back()->with('success', 'Horarios actualizados correctamente.');
    }
}
