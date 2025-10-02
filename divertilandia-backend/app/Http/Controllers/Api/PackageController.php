<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Package::with(['games.toyClauses']);

        // Filtrar por estado activo si se especifica
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $packages = $query->orderBy('name')->get();

        return response()->json($packages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_base64' => 'nullable|string',
            'max_age' => 'required|integer|min:0|max:18',
            'is_active' => 'boolean',
            'games' => 'required|array|min:1',
            'games.*.game_id' => 'required|exists:games,id',
            'games.*.quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $package = Package::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'image_base64' => $request->image_base64,
                'max_age' => $request->max_age,
                'is_active' => $request->get('is_active', true)
            ]);

            // Sincronizar juegos con cantidades
            $gamesData = [];
            foreach ($request->games as $gameData) {
                $gamesData[$gameData['game_id']] = ['quantity' => $gameData['quantity']];
            }
            $package->games()->sync($gamesData);

            // Cargar relaciones para devolver el objeto completo
            $package->load('games');

            DB::commit();
            return response()->json($package, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear el paquete'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $package = Package::with(['games.toyClauses'])->findOrFail($id);
        return response()->json($package);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $package = Package::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'image_base64' => 'nullable|string',
            'max_age' => 'sometimes|integer|min:0|max:18',
            'is_active' => 'sometimes|boolean',
            'games' => 'sometimes|array|min:1',
            'games.*.game_id' => 'required_with:games|exists:games,id',
            'games.*.quantity' => 'required_with:games|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $package->update($request->only([
                'name', 'description', 'price', 'image_base64', 
                'max_age', 'is_active'
            ]));

            // Actualizar juegos si se proporcionan
            if ($request->has('games')) {
                $gamesData = [];
                foreach ($request->games as $gameData) {
                    $gamesData[$gameData['game_id']] = ['quantity' => $gameData['quantity']];
                }
                $package->games()->sync($gamesData);
            }

            // Cargar relaciones para devolver el objeto completo
            $package->load('games');

            DB::commit();
            return response()->json($package);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar el paquete'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $package = Package::findOrFail($id);
        $package->delete();

        return response()->json(['message' => 'Paquete eliminado exitosamente']);
    }
}
