<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ToyType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ToyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ToyType::query();

        // Filtrar por estado activo si se especifica
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $toyTypes = $query->orderBy('name')->get();

        return response()->json($toyTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:toy_types,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'El nombre es requerido',
            'name.unique' => 'Este nombre ya está en uso',
        ]);

        $toyType = ToyType::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'is_active' => $validated['is_active'] ?? true
        ]);

        return response()->json($toyType, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $toyType = ToyType::findOrFail($id);
        return response()->json($toyType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $toyType = ToyType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:toy_types,name,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean'
        ], [
            'name.unique' => 'Este nombre ya está en uso',
        ]);

        $toyType->update($validated);

        return response()->json($toyType);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $toyType = ToyType::findOrFail($id);
        $toyType->delete();

        return response()->json(['message' => 'Toy type deleted successfully']);
    }

    /**
     * Get active toy types
     */
    public function getActive(): JsonResponse
    {
        $activeToyTypes = ToyType::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($activeToyTypes);
    }
}
