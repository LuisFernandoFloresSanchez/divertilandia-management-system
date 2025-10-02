<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventVehicleUsage;
use App\Models\Vehicle;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EventVehicleUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = EventVehicleUsage::with(['event', 'vehicle']);

        // Filtrar por evento
        if ($request->has('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Filtrar por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $usages = $query->orderBy('created_at', 'desc')->get();

        return response()->json($usages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'kilometers' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Obtener el vehículo para calcular el costo
            $vehicle = Vehicle::findOrFail($request->vehicle_id);
            $fuelCost = $vehicle->calculateFuelCost($request->kilometers);

            // Crear el uso del vehículo
            $usage = EventVehicleUsage::create([
                'event_id' => $request->event_id,
                'vehicle_id' => $request->vehicle_id,
                'kilometers' => $request->kilometers,
                'fuel_cost' => $fuelCost,
                'notes' => $request->notes,
                'status' => 'pending'
            ]);

            // Crear automáticamente un gasto de gasolina si no existe
            $this->createFuelExpense($usage);

            $usage->load(['event', 'vehicle']);

            DB::commit();
            return response()->json($usage, 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error al registrar el uso del vehículo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $usage = EventVehicleUsage::with(['event', 'vehicle'])->findOrFail($id);
        return response()->json($usage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $usage = EventVehicleUsage::findOrFail($id);

        $request->validate([
            'kilometers' => 'sometimes|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:pending,paid'
        ]);

        DB::beginTransaction();
        try {
            // Si se actualiza los kilómetros, recalcular el costo
            if ($request->has('kilometers')) {
                $vehicle = $usage->vehicle;
                $fuelCost = $vehicle->calculateFuelCost($request->kilometers);
                $usage->update([
                    'kilometers' => $request->kilometers,
                    'fuel_cost' => $fuelCost,
                    'notes' => $request->notes ?? $usage->notes,
                    'status' => $request->status ?? $usage->status
                ]);
            } else {
                $usage->update($request->only(['notes', 'status']));
            }

            // Actualizar el gasto relacionado si cambió el costo
            if ($request->has('kilometers') || $request->has('status')) {
                $this->updateFuelExpense($usage);
            }

            $usage->load(['event', 'vehicle']);

            DB::commit();
            return response()->json($usage);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error al actualizar el uso del vehículo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $usage = EventVehicleUsage::findOrFail($id);

        DB::beginTransaction();
        try {
            // Eliminar el gasto relacionado si existe
            $this->deleteFuelExpense($usage);

            $usage->delete();

            DB::commit();
            return response()->json(['message' => 'Uso de vehículo eliminado exitosamente']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error al eliminar el uso del vehículo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Marcar uso como pagado
     */
    public function markAsPaid(string $id): JsonResponse
    {
        $usage = EventVehicleUsage::findOrFail($id);

        DB::beginTransaction();
        try {
            $usage->update(['status' => 'paid']);
            
            // Actualizar el gasto relacionado
            $this->updateFuelExpense($usage);

            DB::commit();
            return response()->json($usage);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error al marcar como pagado: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Crear gasto de gasolina automáticamente
     */
    private function createFuelExpense(EventVehicleUsage $usage)
    {
        // Buscar la categoría de gasolina
        $fuelCategory = ExpenseCategory::where('name', 'Gasolina')->first();
        
        if (!$fuelCategory) {
            // Crear la categoría si no existe
            $fuelCategory = ExpenseCategory::create([
                'name' => 'Gasolina',
                'description' => 'Combustible para vehículos',
                'color' => '#F44336',
                'is_active' => true
            ]);
        }

        // Crear el gasto
        Expense::create([
            'concept' => "Gasolina - {$usage->vehicle->name} - {$usage->kilometers} km",
            'description' => "Costo de gasolina para el evento {$usage->event->contact_name} usando {$usage->vehicle->name}. Kilómetros recorridos: {$usage->kilometers} km.",
            'amount' => $usage->fuel_cost,
            'expense_category_id' => $fuelCategory->id,
            'event_id' => $usage->event_id,
            'status' => 'pending',
            'expense_date' => $usage->event->event_date->format('Y-m-d')
        ]);
    }

    /**
     * Actualizar gasto de gasolina relacionado
     */
    private function updateFuelExpense(EventVehicleUsage $usage)
    {
        $expense = Expense::where('event_id', $usage->event_id)
            ->where('concept', 'like', "%Gasolina - {$usage->vehicle->name}%")
            ->first();

        if ($expense) {
            $expense->update([
                'amount' => $usage->fuel_cost,
                'status' => $usage->status,
                'concept' => "Gasolina - {$usage->vehicle->name} - {$usage->kilometers} km",
                'description' => "Costo de gasolina para el evento {$usage->event->contact_name} usando {$usage->vehicle->name}. Kilómetros recorridos: {$usage->kilometers} km.",
                'payment_date' => $usage->status === 'paid' ? now()->toDateString() : null
            ]);
        }
    }

    /**
     * Eliminar gasto de gasolina relacionado
     */
    private function deleteFuelExpense(EventVehicleUsage $usage)
    {
        Expense::where('event_id', $usage->event_id)
            ->where('concept', 'like', "%Gasolina - {$usage->vehicle->name}%")
            ->delete();
    }
}