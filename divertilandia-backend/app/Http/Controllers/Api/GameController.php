<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Game::with(['toyType', 'toyClauses']);

        // Filtrar por tipo de juguete si se especifica
        if ($request->has('toy_type_id')) {
            $query->where('toy_type_id', $request->toy_type_id);
        }

        $games = $query->orderBy('name')->get();

        return response()->json($games);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'toy_type_id' => 'required|exists:toy_types,id',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'condition' => 'required|string',
            'image_base64' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $game = Game::create([
            'name' => $request->name,
            'toy_type_id' => $request->toy_type_id,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'condition' => $request->condition,
            'image_base64' => $request->image_base64,
            'is_active' => $request->get('is_active', true)
        ]);

        // Cargar la relación para devolver el objeto completo
        $game->load('toyType');

        return response()->json($game, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $game = Game::with('toyType')->findOrFail($id);
        return response()->json($game);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $game = Game::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'toy_type_id' => 'sometimes|exists:toy_types,id',
            'quantity' => 'sometimes|integer|min:0',
            'unit_price' => 'sometimes|numeric|min:0',
            'condition' => 'sometimes|string',
            'image_base64' => 'nullable|string',
            'is_active' => 'sometimes|boolean'
        ]);

        $game->update($request->only([
            'name', 'toy_type_id', 'quantity', 'unit_price', 
            'condition', 'image_base64', 'is_active'
        ]));

        // Cargar la relación para devolver el objeto completo
        $game->load('toyType');

        return response()->json($game);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $game = Game::findOrFail($id);
        $game->delete();

        return response()->json(['message' => 'Game deleted successfully']);
    }
}
