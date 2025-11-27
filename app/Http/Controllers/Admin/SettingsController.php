<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DIANResolution;
use App\Models\Order;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function general()
    {
        return view('admin.settings.general');
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|max:2048',
            'currency' => 'required|string|max:10',
            'timezone' => 'required|string|max:50',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:20',
        ]);

        // TODO: Implementar guardado de configuración
        // Por ahora solo retornamos mensaje de éxito
        
        Cache::flush();

        return back()->with('success', 'Configuración general actualizada exitosamente.');
    }

    public function taxes()
    {
        $taxes = Tax::orderBy('name')->get();
        return view('admin.settings.taxes', compact('taxes'));
    }

    public function resolutions()
    {
        $resolutions = DIANResolution::with('branch')
            ->orderByDesc('is_active')
            ->orderByDesc('valid_to')
            ->get();
        return view('admin.settings.resolutions', compact('resolutions'));
    }

    public function printers()
    {
        return view('admin.settings.printers');
    }

    public function integrations()
    {
        // Verificar si Rappi está configurado
        $rappiConnected = !empty(config('services.rappi.client_id')) && !empty(config('services.rappi.client_secret'));
        $rappiStoreId = config('services.rappi.store_id');
        $rappiDomain = config('services.rappi.domain');
        $rappiLastSync = Cache::get('rappi_last_sync');
        
        // Estadísticas de órdenes Rappi
        $rappiStats = [
            'pending' => Order::where('source', 'rappi')->where('status', 'pending')->count(),
            'preparing' => Order::where('source', 'rappi')->where('status', 'preparing')->count(),
            'ready' => Order::where('source', 'rappi')->where('status', 'ready')->count(),
            'today' => Order::where('source', 'rappi')->whereDate('created_at', today())->count(),
        ];

        return view('admin.settings.integrations', compact(
            'rappiConnected',
            'rappiStoreId',
            'rappiDomain',
            'rappiLastSync',
            'rappiStats'
        ));
    }

    public function updateIntegrations(Request $request)
    {
        // TODO: Implementar guardado de integraciones
        
        return back()->with('success', 'Integraciones actualizadas exitosamente.');
    }

    public function notifications()
    {
        return view('admin.settings.notifications');
    }

    public function updateNotifications(Request $request)
    {
        // TODO: Implementar guardado de notificaciones
        
        return back()->with('success', 'Notificaciones actualizadas exitosamente.');
    }
}
