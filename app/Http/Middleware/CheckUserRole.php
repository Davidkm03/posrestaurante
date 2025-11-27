<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userPosition = strtolower(auth()->user()->position ?? '');

        // Convertir roles permitidos a minúsculas para comparación
        $allowedRoles = array_map('strtolower', $roles);

        // Verificar si el usuario tiene uno de los roles permitidos
        if (in_array($userPosition, $allowedRoles)) {
            return $next($request);
        }

        // Si no tiene acceso, redirigir según su rol
        return match($userPosition) {
            'mesero' => redirect()->route('waiter.index')->with('error', 'No tienes acceso a esta sección'),
            'cajero' => redirect()->route('pos.index')->with('error', 'No tienes acceso a esta sección'),
            'cocina' => redirect()->route('kitchen.index')->with('error', 'No tienes acceso a esta sección'),
            'gerente', 'administrador' => redirect()->route('admin.dashboard')->with('error', 'No tienes acceso a esta sección'),
            default => redirect()->route('login')->with('error', 'Rol no reconocido'),
        };
    }
}
