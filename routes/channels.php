<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privado para la sucursal
Broadcast::channel('branch.{branchId}', function ($user, $branchId) {
    return $user->branch_id == $branchId || $user->hasRole('administrador');
});

// Canal público para cocina (autenticación básica)
Broadcast::channel('kitchen.{branchId}', function ($user, $branchId) {
    return true; // Público para usuarios autenticados
});

// Canal público para POS
Broadcast::channel('pos.{branchId}', function ($user, $branchId) {
    return true;
});

// Canal de inventario (solo admin/gerente)
Broadcast::channel('inventory.{branchId}', function ($user, $branchId) {
    return $user->hasAnyRole(['administrador', 'gerente']);
});
