<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FuelPrice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FuelPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $fuelPrices = FuelPrice::orderBy('fuel_type')
                ->orderBy('effective_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $fuelPrices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los precios de combustible',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'fuel_type' => 'required|in:regular,premium,diesel,electricity',
                'price_per_liter' => 'required|numeric|min:0',
                'effective_date' => 'required|date',
                'notes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fuelPrice = FuelPrice::setNewPrice(
                $request->fuel_type,
                $request->price_per_liter,
                $request->effective_date,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Precio de combustible actualizado exitosamente',
                'data' => $fuelPrice
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el precio de combustible',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $fuelPrice = FuelPrice::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $fuelPrice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Precio de combustible no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $fuelPrice = FuelPrice::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'fuel_type' => 'sometimes|required|in:regular,premium,diesel,electricity',
                'price_per_liter' => 'sometimes|required|numeric|min:0',
                'effective_date' => 'sometimes|required|date',
                'is_active' => 'sometimes|boolean',
                'notes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fuelPrice->update($request->only([
                'fuel_type', 'price_per_liter', 'effective_date', 'is_active', 'notes'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Precio de combustible actualizado exitosamente',
                'data' => $fuelPrice->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el precio de combustible',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $fuelPrice = FuelPrice::findOrFail($id);
            $fuelPrice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Precio de combustible eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el precio de combustible',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener los precios actuales de combustible
     */
    public function getCurrentPrices(): JsonResponse
    {
        try {
            $currentPrices = FuelPrice::getCurrentPrices();

            return response()->json([
                'success' => true,
                'data' => $currentPrices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los precios actuales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el precio actual de un tipo específico de combustible
     */
    public function getCurrentPrice(string $fuelType): JsonResponse
    {
        try {
            $currentPrice = FuelPrice::getCurrentPrice($fuelType);

            if (!$currentPrice) {
                return response()->json([
                    'success' => false,
                    'message' => "No se encontró precio actual para {$fuelType}"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $currentPrice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el precio actual',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
