<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
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
}
