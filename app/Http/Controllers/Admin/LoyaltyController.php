<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LoyaltyPoint;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function __construct(
        private LoyaltyService $loyaltyService
    ) {}

    /**
     * Dashboard del programa de lealtad
     */
    public function index()
    {
        $stats = [
            'total_customers' => Customer::whereNotNull('loyalty_points')->where('loyalty_points', '>', 0)->count(),
            'total_points_active' => LoyaltyPoint::where('type', 'earned')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })->sum('points') - LoyaltyPoint::where('type', 'redeemed')->sum('points'),
            'points_earned_month' => LoyaltyPoint::where('type', 'earned')
                ->whereMonth('created_at', now()->month)
                ->sum('points'),
            'points_redeemed_month' => LoyaltyPoint::where('type', 'redeemed')
                ->whereMonth('created_at', now()->month)
                ->sum('points'),
        ];

        // Distribución por niveles
        $levelDistribution = [];
        $levels = config('loyalty.levels', []);
        
        foreach ($levels as $level => $config) {
            $nextLevel = null;
            $minPoints = $config['min_points'];
            $maxPoints = PHP_INT_MAX;

            // Encontrar el siguiente nivel
            foreach ($levels as $l => $c) {
                if ($c['min_points'] > $minPoints) {
                    if ($nextLevel === null || $c['min_points'] < $maxPoints) {
                        $nextLevel = $l;
                        $maxPoints = $c['min_points'];
                    }
                }
            }

            $query = Customer::where('loyalty_points', '>=', $minPoints);
            if ($maxPoints < PHP_INT_MAX) {
                $query->where('loyalty_points', '<', $maxPoints);
            }

            $levelDistribution[$level] = [
                'count' => $query->count(),
                'color' => $config['color'],
                'name' => ucfirst($level),
            ];
        }

        // Top clientes por puntos
        $topCustomers = Customer::where('loyalty_points', '>', 0)
            ->orderByDesc('loyalty_points')
            ->limit(10)
            ->get()
            ->map(function ($customer) {
                $customer->level = $this->loyaltyService->getCustomerLevel($customer);
                return $customer;
            });

        // Actividad reciente
        $recentActivity = LoyaltyPoint::with('customer')
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.loyalty.index', compact(
            'stats',
            'levelDistribution',
            'topCustomers',
            'recentActivity'
        ));
    }

    /**
     * Ver historial de un cliente
     */
    public function customerHistory(Customer $customer)
    {
        $history = LoyaltyPoint::where('customer_id', $customer->id)
            ->with('order')
            ->latest()
            ->paginate(20);

        $stats = $this->loyaltyService->getCustomerStats($customer);
        $level = $this->loyaltyService->getCustomerLevel($customer);
        $levelConfig = config("loyalty.levels.{$level}", []);

        return view('admin.loyalty.customer-history', compact(
            'customer',
            'history',
            'stats',
            'level',
            'levelConfig'
        ));
    }

    /**
     * Ajustar puntos manualmente
     */
    public function adjustPoints(Request $request, Customer $customer)
    {
        $request->validate([
            'points' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:255',
        ]);

        $points = $request->input('points');
        $reason = $request->input('reason');

        if ($points > 0) {
            $this->loyaltyService->addBonusPoints($customer, $points, $reason);
            $message = "Se agregaron {$points} puntos al cliente.";
        } else {
            // Ajuste negativo
            LoyaltyPoint::create([
                'customer_id' => $customer->id,
                'type' => 'adjusted',
                'points' => $points,
                'balance_after' => $customer->loyalty_points + $points,
                'description' => "Ajuste manual: {$reason}",
            ]);

            $customer->decrement('loyalty_points', abs($points));
            $message = "Se restaron " . abs($points) . " puntos del cliente.";
        }

        return redirect()
            ->route('admin.loyalty.customer-history', $customer)
            ->with('success', $message);
    }

    /**
     * Configuración del programa
     */
    public function settings()
    {
        $settings = [
            'points_per_amount' => config('loyalty.points_per_amount'),
            'amount_for_points' => config('loyalty.amount_for_points'),
            'point_value' => config('loyalty.point_value'),
            'min_points_redeem' => config('loyalty.min_points_redeem'),
            'expiration_days' => config('loyalty.expiration_days'),
            'birthday_bonus' => config('loyalty.birthday_bonus'),
            'referral_points' => config('loyalty.referral_points'),
        ];

        $levels = config('loyalty.levels', []);

        return view('admin.loyalty.settings', compact('settings', 'levels'));
    }

    /**
     * Guardar configuración
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'points_per_amount' => 'required|integer|min:1',
            'amount_for_points' => 'required|integer|min:100',
            'point_value' => 'required|integer|min:1',
            'min_points_redeem' => 'required|integer|min:1',
            'expiration_days' => 'required|integer|min:0',
        ]);

        // Guardar en .env o base de datos según preferencia
        // Por ahora solo retornamos mensaje
        return redirect()
            ->route('admin.loyalty.settings')
            ->with('success', 'Configuración actualizada correctamente.');
    }

    /**
     * Expirar puntos antiguos
     */
    public function expirePoints()
    {
        $count = $this->loyaltyService->expireOldPoints();

        return redirect()
            ->route('admin.loyalty.index')
            ->with('success', "Se expiraron puntos de {$count} registros.");
    }

    /**
     * Exportar datos de lealtad
     */
    public function export(Request $request)
    {
        $customers = Customer::where('loyalty_points', '>', 0)
            ->orderByDesc('loyalty_points')
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'nombre' => $customer->full_name,
                    'email' => $customer->email,
                    'telefono' => $customer->phone,
                    'puntos' => $customer->loyalty_points,
                    'nivel' => ucfirst($this->loyaltyService->getCustomerLevel($customer)),
                    'registrado' => $customer->created_at->format('Y-m-d'),
                ];
            });

        $filename = 'clientes-lealtad-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($customers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nombre', 'Email', 'Teléfono', 'Puntos', 'Nivel', 'Registrado']);
            
            foreach ($customers as $customer) {
                fputcsv($file, $customer);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
