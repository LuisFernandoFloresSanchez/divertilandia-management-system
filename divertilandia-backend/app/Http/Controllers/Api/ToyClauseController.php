<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ToyClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ToyClauseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $clauses = ToyClause::orderBy('name')->get();
            
            return response()->json([
                'success' => true,
                'data' => $clauses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cláusulas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $clause = ToyClause::create($request->only(['name', 'description', 'is_active']));

            return response()->json([
                'success' => true,
                'message' => 'Cláusula creada exitosamente',
                'data' => $clause
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la cláusula',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $clause = ToyClause::with('games.toyType')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $clause
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cláusula no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $clause = ToyClause::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $clause->update($request->only(['name', 'description', 'is_active']));

            return response()->json([
                'success' => true,
                'message' => 'Cláusula actualizada exitosamente',
                'data' => $clause
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la cláusula',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $clause = ToyClause::findOrFail($id);
            $clause->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cláusula eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la cláusula',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get clauses for a specific game
     */
    public function getGameClauses(string $gameId)
    {
        try {
            $clauses = ToyClause::whereHas('games', function ($query) use ($gameId) {
                $query->where('game_id', $gameId);
            })->get();

            return response()->json([
                'success' => true,
                'data' => $clauses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cláusulas del juguete',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign clauses to a game
     */
    public function assignClausesToGame(Request $request, string $gameId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'clause_ids' => 'required|array',
                'clause_ids.*' => 'exists:toy_clauses,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $game = \App\Models\Game::findOrFail($gameId);
            $game->toyClauses()->sync($request->clause_ids);

            return response()->json([
                'success' => true,
                'message' => 'Cláusulas asignadas exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar las cláusulas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}