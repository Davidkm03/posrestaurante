<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'branches']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(20);
        $roles = UserRole::cases();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = UserRole::cases();
        $branches = Branch::where('is_active', true)->get();

        return view('admin.users.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'pin' => 'required|string|size:4',
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|in:' . implode(',', array_column(UserRole::cases(), 'value')),
            'branches' => 'required|array|min:1',
            'branches.*' => 'exists:branches,id',
            'default_branch' => 'required|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'pin' => $validated['pin'],
            'position' => $validated['position'],
            'phone' => $validated['phone'],
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        // Asignar sucursales
        foreach ($validated['branches'] as $branchId) {
            $user->branches()->attach($branchId, [
                'is_default' => $branchId == $validated['default_branch'],
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $roles = UserRole::cases();
        $branches = Branch::where('is_active', true)->get();
        $user->load(['roles', 'branches']);

        return view('admin.users.edit', compact('user', 'roles', 'branches'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'pin' => 'required|string|size:4',
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|in:' . implode(',', array_column(UserRole::cases(), 'value')),
            'branches' => 'required|array|min:1',
            'branches.*' => 'exists:branches,id',
            'default_branch' => 'required|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'pin' => $validated['pin'],
            'position' => $validated['position'],
            'phone' => $validated['phone'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Sincronizar roles
        $user->syncRoles([$validated['role']]);

        // Sincronizar sucursales
        $branchesSync = [];
        foreach ($validated['branches'] as $branchId) {
            $branchesSync[$branchId] = [
                'is_default' => $branchId == $validated['default_branch'],
            ];
        }
        $user->branches()->sync($branchesSync);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        // No permitir eliminar el propio usuario
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // No permitir eliminar super admin si es el único
        if ($user->hasRole(UserRole::SUPER_ADMIN->value)) {
            $superAdminCount = User::role(UserRole::SUPER_ADMIN->value)->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'No se puede eliminar el único Super Administrador.');
            }
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 'Estado del usuario actualizado.');
    }

    public function resetPin(Request $request, User $user)
    {
        $request->validate([
            'pin' => 'required|string|size:4',
        ]);

        $user->update(['pin' => $request->pin]);

        return back()->with('success', 'PIN actualizado exitosamente.');
    }
}
