<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Game;
use App\Models\Package;
use App\Models\Expense;
use App\Models\EventVehicleUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            // Estadísticas de eventos
            $totalEvents = Event::count();
            $pendingEvents = Event::where('status', 'pending')->count();
            $confirmedEvents = Event::where('status', 'confirmed')->count();
            $completedEvents = Event::where('status', 'completed')->count();
            
            // Ingresos - Calculamos el total basado en el precio del paquete y extras
            $totalRevenueEvents = Event::with('package')
                ->whereIn('status', ['confirmed', 'completed'])
                ->get();
            
            $totalRevenue = $totalRevenueEvents->sum(function($event) {
                return $event->total_event_price ?? 0;
            });
            
            $monthlyRevenueEvents = Event::with('package')
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereMonth('event_date', Carbon::now()->month)
                ->whereYear('event_date', Carbon::now()->year)
                ->get();
                
            $monthlyRevenue = $monthlyRevenueEvents->sum(function($event) {
                return $event->total_event_price ?? 0;
            });
            
            // Estadísticas de juegos/inventario
            $totalGames = Game::count();
            $availableGames = Game::where('is_active', true)
                ->where(function($query) {
                    $query->where('excellent_count', '>', 0)
                          ->orWhere('good_count', '>', 0)
                          ->orWhere('fair_count', '>', 0);
                })
                ->count();
            
            $gamesInMaintenance = Game::where('is_active', true)
                ->where(function($query) {
                    $query->where('poor_count', '>', 0)
                          ->orWhere('broken_count', '>', 0);
                })
                ->count();
            
            // Estadísticas de paquetes
            $totalPackages = Package::where('is_active', true)->count();
            
            // Paquetes más populares (basado en eventos)
            $popularPackages = Event::select('package_id', DB::raw('COUNT(*) as bookings'))
                ->whereNotNull('package_id')
                ->groupBy('package_id')
                ->orderBy('bookings', 'desc')
                ->limit(4)
                ->with('package')
                ->get()
                ->map(function($item) use ($totalEvents) {
                    return [
                        'name' => $item->package->name ?? 'Paquete Desconocido',
                        'bookings' => $item->bookings,
                        'percentage' => $totalEvents > 0 ? round(($item->bookings / $totalEvents) * 100) : 0
                    ];
                });
            
            // Próximos eventos (fechas más cercanas)
            $upcomingEvents = Event::with(['package'])
                ->where('event_date', '>=', Carbon::now()->toDateString())
                ->whereIn('status', ['pending', 'confirmed'])
                ->orderBy('event_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->limit(4)
                ->get()
                ->map(function($event) {
                    return [
                        'id' => $event->id,
                        'client' => $event->contact_name ?? 'Cliente',
                        'date' => $event->event_date,
                        'time' => $event->start_time,
                        'package' => $event->package->name ?? 'Sin paquete',
                        'status' => $event->status,
                        'amount' => $event->total_event_price ?? 0,
                        'child' => 'Evento #' . $event->id,
                        'child_gender' => $event->child_gender
                    ];
                });
            
            // Gastos del mes
            $monthlyExpenses = Expense::whereMonth('expense_date', Carbon::now()->month)
                ->whereYear('expense_date', Carbon::now()->year)
                ->sum('amount');
            
            // Gastos de gasolina del mes
            $monthlyFuelExpenses = EventVehicleUsage::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('fuel_cost');
            
            $totalMonthlyExpenses = $monthlyExpenses + $monthlyFuelExpenses;
            
            // Utilidad del mes
            $monthlyProfit = $monthlyRevenue - $totalMonthlyExpenses;
            
            // Clientes únicos (basado en nombres de contacto)
            $uniqueClients = Event::distinct('contact_name')->count('contact_name');
            
            // Nota: Calificación promedio removida - no tenemos datos reales de reviews
            
            return response()->json([
                'totalEvents' => $totalEvents,
                'pendingEvents' => $pendingEvents,
                'confirmedEvents' => $confirmedEvents,
                'completedEvents' => $completedEvents,
                'totalRevenue' => $totalRevenue,
                'monthlyRevenue' => $monthlyRevenue,
                'monthlyExpenses' => $totalMonthlyExpenses,
                'monthlyProfit' => $monthlyProfit,
                'totalGames' => $totalGames,
                'availableGames' => $availableGames,
                'gamesInMaintenance' => $gamesInMaintenance,
                'totalPackages' => $totalPackages,
                'uniqueClients' => $uniqueClients,
                'popularPackages' => $popularPackages,
                'upcomingEvents' => $upcomingEvents,
                'currentMonth' => Carbon::now()->format('F Y'),
                'currentDate' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener estadísticas del dashboard',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}