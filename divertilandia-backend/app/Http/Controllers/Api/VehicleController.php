<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Vehicle::query();

        // Filtrar por estado activo si se especifica
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $vehicles = $query->orderBy('name')->get();

        return response()->json($vehicles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:4',
            'plate_number' => 'nullable|string|max:20',
            'fuel_type' => 'required|in:regular,premium,diesel',
            'fuel_efficiency' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean'
        ]);

        $vehicle = Vehicle::create($request->all());

        return response()->json($vehicle, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $vehicle = Vehicle::findOrFail($id);
        return response()->json($vehicle);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:4',
            'plate_number' => 'nullable|string|max:20',
            'fuel_type' => 'sometimes|in:regular,premium,diesel',
            'fuel_efficiency' => 'sometimes|numeric|min:0',
            'color' => 'nullable|string|max:7',
            'is_active' => 'sometimes|boolean'
        ]);

        $vehicle->update($request->all());

        return response()->json($vehicle);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return response()->json(['message' => 'Vehículo eliminado exitosamente']);
    }

    /**
     * Calcular costo de gasolina para una distancia
     */
    public function calculateFuelCost(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'vehicle_id' => 'required|exists:vehicles,id',
                'kilometers' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $vehicle = Vehicle::findOrFail($request->vehicle_id);
            $fuelCost = $vehicle->calculateFuelCost($request->kilometers);
            $currentFuelPrice = $vehicle->getCurrentFuelPrice();

            return response()->json([
                'success' => true,
                'data' => [
                    'vehicle' => $vehicle,
                    'kilometers' => $request->kilometers,
                    'fuel_cost' => $fuelCost,
                    'cost_per_kilometer' => $vehicle->cost_per_kilometer,
                    'current_fuel_price' => $currentFuelPrice,
                    'fuel_efficiency' => $vehicle->fuel_efficiency,
                    'liters_needed' => $request->kilometers / $vehicle->fuel_efficiency
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular el costo de combustible',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}