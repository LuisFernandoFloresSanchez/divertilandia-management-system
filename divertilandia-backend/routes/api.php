<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\EventVehicleUsageController;
use App\Http\Controllers\Api\FuelPriceController;
use App\Http\Controllers\Api\ToyClauseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Ruta para Dashboard
Route::get('dashboard/stats', [DashboardController::class, 'getStats']);

// Rutas para Paquetes
Route::apiResource('packages', PackageController::class);
Route::get('packages/year/{year}', [PackageController::class, 'getByYear']);

// Rutas para Eventos
Route::apiResource('events', EventController::class);
Route::get('events/date/{date}', [EventController::class, 'getByDate']);
Route::get('events/range/{start_date}/{end_date}', [EventController::class, 'getByDateRange']);
Route::get('events/status/{status}', [EventController::class, 'getByStatus']);

// Rutas para Tipos de Juguetes
Route::apiResource('toy-types', \App\Http\Controllers\Api\ToyTypeController::class);
Route::get('toy-types/active/active', [\App\Http\Controllers\Api\ToyTypeController::class, 'getActive']);

// Rutas para Inventario
Route::apiResource('inventory', InventoryController::class);
Route::get('inventory/available', [InventoryController::class, 'getAvailable']);

// Rutas para unidades individuales del inventario
Route::post('inventory/{itemId}/units', [InventoryController::class, 'addUnit']);
Route::put('inventory-units/{unitId}', [InventoryController::class, 'updateUnit']);
Route::delete('inventory-units/{unitId}', [InventoryController::class, 'deleteUnit']);

// Rutas para Configuraciones
Route::apiResource('settings', \App\Http\Controllers\Api\EventSettingController::class);

// Rutas para Gastos
Route::apiResource('expenses', ExpenseController::class);
Route::post('expenses/{id}/mark-paid', [ExpenseController::class, 'markAsPaid']);
Route::get('expenses-summary', [ExpenseController::class, 'summary']);

// Rutas para Categorías de Gastos
Route::apiResource('expense-categories', ExpenseCategoryController::class);

// Rutas para Vehículos
Route::apiResource('vehicles', VehicleController::class);
Route::post('vehicles/calculate-fuel-cost', [VehicleController::class, 'calculateFuelCost']);

// Rutas para Uso de Vehículos en Eventos
Route::apiResource('event-vehicle-usage', EventVehicleUsageController::class);
Route::post('event-vehicle-usage/{id}/mark-paid', [EventVehicleUsageController::class, 'markAsPaid']);

// Rutas para precios de combustible
Route::apiResource('fuel-prices', FuelPriceController::class);
Route::get('fuel-prices/current/all', [FuelPriceController::class, 'getCurrentPrices']);
Route::get('fuel-prices/current/{fuelType}', [FuelPriceController::class, 'getCurrentPrice']);
Route::get('settings/active/active', [\App\Http\Controllers\Api\EventSettingController::class, 'getActive']);
Route::get('settings/key/{key}', [\App\Http\Controllers\Api\EventSettingController::class, 'getByKey']);

// Rutas para Juegos (ahora usado como inventario)
Route::apiResource('games', GameController::class);
Route::get('games/type/{type}', [GameController::class, 'getByType']);

// Rutas para Inventario (usando la tabla games)
Route::prefix('inventory')->group(function () {
    Route::get('/', [InventoryController::class, 'index']);
    Route::post('/', [InventoryController::class, 'store']);
    Route::get('/available', [InventoryController::class, 'getAvailable']);
    Route::get('/{id}', [InventoryController::class, 'show']);
    Route::put('/{id}', [InventoryController::class, 'update']);
    Route::delete('/{id}', [InventoryController::class, 'destroy']);
    Route::put('/{id}/unit-counters', [InventoryController::class, 'updateUnitCounters']);
});

// Rutas para Dashboard
Route::prefix('dashboard')->group(function () {
    Route::get('stats', [DashboardController::class, 'getStats']);
    Route::get('revenue/{year}', [DashboardController::class, 'getRevenue']);
    Route::get('events-summary', [DashboardController::class, 'getEventsSummary']);
    Route::get('inventory-summary', [DashboardController::class, 'getInventorySummary']);
});

// Rutas para Cláusulas de Juguetes
Route::apiResource('toy-clauses', ToyClauseController::class);
Route::get('games/{gameId}/clauses', [ToyClauseController::class, 'getGameClauses']);
Route::post('games/{gameId}/clauses', [ToyClauseController::class, 'assignClausesToGame']);
