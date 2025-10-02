<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of games (inventory items)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Game::with(['toyType']);

        // Filtrar por tipo de juguete
        if ($request->has('toy_type_id')) {
            $query->where('toy_type_id', $request->toy_type_id);
        }

        // Filtrar por estado de salud (si tiene unidades en ese estado)
        if ($request->has('health_status')) {
            $healthStatus = $request->health_status;
            $query->where(function ($q) use ($healthStatus) {
                switch ($healthStatus) {
                    case 'excellent':
                        $q->where('excellent_count', '>', 0);
                        break;
                    case 'good':
                        $q->where('good_count', '>', 0);
                        break;
                    case 'fair':
                        $q->where('fair_count', '>', 0);
                        break;
                    case 'poor':
                        $q->where('poor_count', '>', 0);
                        break;
                    case 'broken':
                        $q->where('broken_count', '>', 0);
                        break;
                }
            });
        }

        // Filtrar por estado de disponibilidad
        if ($request->has('status')) {
            $status = $request->status;
            $query->where(function ($q) use ($status) {
                switch ($status) {
                    case 'available':
                        $q->where('available_count', '>', 0);
                        break;
                    case 'in_use':
                        $q->where('in_use_count', '>', 0);
                        break;
                    case 'maintenance':
                        $q->where('maintenance_count', '>', 0);
                        break;
                    case 'retired':
                        $q->where('retired_count', '>', 0);
                        break;
                }
            });
        }

        $games = $query->get();
        
        // Agregar conteos calculados usando los accessors del modelo
        $games->each(function ($game) {
            $game->health_status_counts = $game->health_status_counts;
            $game->availability_status_counts = $game->availability_status_counts;
            $game->available_units = $game->available_units;
            $game->in_use_units = $game->in_use_units;
            $game->maintenance_units = $game->maintenance_units;
            $game->retired_units = $game->retired_units;
        });

        return response()->json($games);
    }

    /**
     * Store a newly created game
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'toy_type_id' => 'required|exists:toy_types,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'condition' => 'nullable|string',
            'image_base64' => 'nullable|string',
            'is_active' => 'boolean',
            // Contadores por estado de salud
            'excellent_count' => 'integer|min:0',
            'good_count' => 'integer|min:0',
            'fair_count' => 'integer|min:0',
            'poor_count' => 'integer|min:0',
            'broken_count' => 'integer|min:0',
            // Contadores por estado de disponibilidad
            'available_count' => 'integer|min:0',
            'in_use_count' => 'integer|min:0',
            'maintenance_count' => 'integer|min:0',
            'retired_count' => 'integer|min:0',
        ]);

        // Si no se proporcionan contadores, inicializar con valores por defecto
        if (!isset($validated['excellent_count'])) {
            $validated['excellent_count'] = 0;
        }
        if (!isset($validated['good_count'])) {
            $validated['good_count'] = $validated['quantity'];
        }
        if (!isset($validated['fair_count'])) {
            $validated['fair_count'] = 0;
        }
        if (!isset($validated['poor_count'])) {
            $validated['poor_count'] = 0;
        }
        if (!isset($validated['broken_count'])) {
            $validated['broken_count'] = 0;
        }
        if (!isset($validated['available_count'])) {
            $validated['available_count'] = $validated['quantity'];
        }
        if (!isset($validated['in_use_count'])) {
            $validated['in_use_count'] = 0;
        }
        if (!isset($validated['maintenance_count'])) {
            $validated['maintenance_count'] = 0;
        }
        if (!isset($validated['retired_count'])) {
            $validated['retired_count'] = 0;
        }

        $game = Game::create($validated);
        $game->load('toyType');

        return response()->json($game, 201);
    }

    /**
     * Display the specified game
     */
    public function show($id): JsonResponse
    {
        $game = Game::with(['toyType'])->findOrFail($id);
        
        // Agregar conteos calculados
        $game->health_status_counts = $game->health_status_counts;
        $game->availability_status_counts = $game->availability_status_counts;
        $game->available_units = $game->available_units;
        $game->in_use_units = $game->in_use_units;
        $game->maintenance_units = $game->maintenance_units;
        $game->retired_units = $game->retired_units;

        return response()->json($game);
    }

    /**
     * Update the specified game
     */
    public function update(Request $request, $id): JsonResponse
    {
        $game = Game::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'toy_type_id' => 'sometimes|exists:toy_types,id',
            'quantity' => 'sometimes|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'condition' => 'nullable|string',
            'image_base64' => 'nullable|string',
            'is_active' => 'boolean',
            // Contadores por estado de salud
            'excellent_count' => 'integer|min:0',
            'good_count' => 'integer|min:0',
            'fair_count' => 'integer|min:0',
            'poor_count' => 'integer|min:0',
            'broken_count' => 'integer|min:0',
            // Contadores por estado de disponibilidad
            'available_count' => 'integer|min:0',
            'in_use_count' => 'integer|min:0',
            'maintenance_count' => 'integer|min:0',
            'retired_count' => 'integer|min:0',
        ]);

        $game->update($validated);
        $game->load('toyType');

        // Agregar conteos calculados
        $game->health_status_counts = $game->health_status_counts;
        $game->availability_status_counts = $game->availability_status_counts;
        $game->available_units = $game->available_units;
        $game->in_use_units = $game->in_use_units;
        $game->maintenance_units = $game->maintenance_units;
        $game->retired_units = $game->retired_units;

        return response()->json($game);
    }

    /**
     * Remove the specified game
     */
    public function destroy($id): JsonResponse
    {
        $game = Game::findOrFail($id);
        $game->delete();

        return response()->json(['message' => 'Game deleted successfully']);
    }

    /**
     * Get available games (with available units > 0)
     */
    public function getAvailable(): JsonResponse
    {
        $games = Game::with(['toyType'])
            ->where('available_count', '>', 0)
            ->where('is_active', true)
            ->get();

        return response()->json($games);
    }

    /**
     * Update unit counters for a specific game
     */
    public function updateUnitCounters(Request $request, $id): JsonResponse
    {
        $game = Game::findOrFail($id);

        $validated = $request->validate([
            'excellent_count' => 'integer|min:0',
            'good_count' => 'integer|min:0',
            'fair_count' => 'integer|min:0',
            'poor_count' => 'integer|min:0',
            'broken_count' => 'integer|min:0',
            'available_count' => 'integer|min:0',
            'in_use_count' => 'integer|min:0',
            'maintenance_count' => 'integer|min:0',
            'retired_count' => 'integer|min:0',
        ]);

        // Verificar que la suma de contadores sea igual a la cantidad total
        $healthTotal = array_sum([
            $validated['excellent_count'] ?? $game->excellent_count,
            $validated['good_count'] ?? $game->good_count,
            $validated['fair_count'] ?? $game->fair_count,
            $validated['poor_count'] ?? $game->poor_count,
            $validated['broken_count'] ?? $game->broken_count,
        ]);

        $availabilityTotal = array_sum([
            $validated['available_count'] ?? $game->available_count,
            $validated['in_use_count'] ?? $game->in_use_count,
            $validated['maintenance_count'] ?? $game->maintenance_count,
            $validated['retired_count'] ?? $game->retired_count,
        ]);

        if ($healthTotal !== $game->quantity) {
            return response()->json([
                'error' => 'La suma de contadores de salud debe ser igual a la cantidad total'
            ], 400);
        }

        if ($availabilityTotal !== $game->quantity) {
            return response()->json([
                'error' => 'La suma de contadores de disponibilidad debe ser igual a la cantidad total'
            ], 400);
        }

        $game->update($validated);
        $game->load('toyType');

        // Agregar conteos calculados
        $game->health_status_counts = $game->health_status_counts;
        $game->availability_status_counts = $game->availability_status_counts;
        $game->available_units = $game->available_units;
        $game->in_use_units = $game->in_use_units;
        $game->maintenance_units = $game->maintenance_units;
        $game->retired_units = $game->retired_units;

        return response()->json($game);
    }
}