<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Game;
use App\Models\InventoryItem;
use App\Models\InventoryUnit;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Obtener todos los juegos existentes
        $games = Game::with('toyType')->get();
        
        echo "Iniciando migración de " . $games->count() . " juegos...\n";
        
        foreach ($games as $game) {
            try {
                // Crear el item de inventario
                $inventoryItem = InventoryItem::create([
                    'name' => $game->name,
                    'description' => $game->description ?? '',
                    'toy_type_id' => $game->toy_type_id,
                    'brand' => $game->brand ?? '',
                    'model' => $game->model ?? '',
                    'unit_price' => $game->unit_price ?? 0,
                    'purchase_date' => $game->purchase_date ?? null,
                    'notes' => $game->notes ?? '',
                    'image_base64' => $game->image_base64 ?? null,
                    'health_status' => $this->mapGameConditionToHealthStatus($game->condition),
                    'status' => $game->is_active ? 'available' : 'retired',
                    'quantity' => $game->quantity,
                ]);
                
                echo "Creado item de inventario: {$inventoryItem->name} (ID: {$inventoryItem->id}) - Tipo: " . ($game->toyType ? $game->toyType->name : 'Sin tipo') . "\n";
                
                // Si la cantidad es > 1, crear unidades individuales
                if ($game->quantity > 1) {
                    $this->createInventoryUnits($inventoryItem, $game);
                } else {
                    // Si es cantidad 1, crear una sola unidad
                    $this->createInventoryUnits($inventoryItem, $game, 1);
                }
                
            } catch (Exception $e) {
                echo "Error migrando juego {$game->id}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "Migración completada!\n";
    }

    /**
     * Crear unidades de inventario para un item
     */
    private function createInventoryUnits(InventoryItem $inventoryItem, Game $game, ?int $maxUnits = null): void
    {
        $quantity = $maxUnits ?? $game->quantity;
        $baseHealthStatus = $this->mapGameConditionToHealthStatus($game->condition);
        $baseStatus = $game->is_active ? 'available' : 'retired';
        
        for ($i = 1; $i <= $quantity; $i++) {
            // Crear identificador único para la unidad
            $unitIdentifier = $this->generateUnitIdentifier($inventoryItem->name, $i, $quantity);
            
            // Para productos con muchas unidades (como pelotas), usar un estado más genérico
            if ($quantity >= 50) {
                $healthStatus = $baseHealthStatus;
                $status = $baseStatus;
                $location = 'Almacén General';
                $notes = "Unidad {$i} de {$quantity}";
            } else {
                // Para productos con pocas unidades, variar ligeramente el estado
                $healthStatus = $this->varyHealthStatus($baseHealthStatus, $i);
                $status = $baseStatus;
                $location = $this->generateLocation($i);
                $notes = "Migrado desde juego ID: {$game->id}";
            }
            
            InventoryUnit::create([
                'inventory_item_id' => $inventoryItem->id,
                'unit_identifier' => $unitIdentifier,
                'serial_number' => null,
                'health_status' => $healthStatus,
                'status' => $status,
                'location' => $location,
                'notes' => $notes,
                'last_maintenance_date' => null,
                'next_maintenance_date' => null,
            ]);
        }
        
        echo "  Creadas {$quantity} unidades para {$inventoryItem->name}\n";
    }

    /**
     * Mapear la condición del juego al estado de salud
     */
    private function mapGameConditionToHealthStatus(?string $condition): string
    {
        if (!$condition) return 'good';
        
        switch (strtolower($condition)) {
            case 'excellent':
                return 'excellent';
            case 'good':
                return 'good';
            case 'fair':
                return 'fair';
            case 'needs_repair':
            case 'out_of_service':
                return 'broken';
            default:
                return 'good';
        }
    }

    /**
     * Generar identificador único para la unidad
     */
    private function generateUnitIdentifier(string $itemName, int $unitNumber, int $totalUnits): string
    {
        // Limpiar el nombre del item
        $cleanName = preg_replace('/[^a-zA-Z0-9\s]/', '', $itemName);
        $cleanName = preg_replace('/\s+/', '', $cleanName);
        $cleanName = substr($cleanName, 0, 10); // Limitar a 10 caracteres
        
        // Generar el identificador
        if ($totalUnits >= 50) {
            return strtoupper($cleanName) . '-' . str_pad($unitNumber, 3, '0', STR_PAD_LEFT);
        } else {
            return strtoupper($cleanName) . '-' . str_pad($unitNumber, 2, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Variar ligeramente el estado de salud para simular diferencias entre unidades
     */
    private function varyHealthStatus(string $baseStatus, int $unitNumber): string
    {
        // Para la mayoría de unidades, mantener el estado base
        if ($unitNumber % 4 !== 0) {
            return $baseStatus;
        }
        
        // Cada 4ta unidad, variar ligeramente el estado
        switch ($baseStatus) {
            case 'excellent':
                return 'good';
            case 'good':
                return rand(0, 1) ? 'excellent' : 'fair';
            case 'fair':
                return rand(0, 1) ? 'good' : 'poor';
            case 'poor':
                return rand(0, 1) ? 'fair' : 'broken';
            default:
                return $baseStatus;
        }
    }

    /**
     * Generar ubicación para la unidad
     */
    private function generateLocation(int $unitNumber): string
    {
        $locations = [
            'Almacén A',
            'Almacén B', 
            'Almacén Principal',
            'Estante Superior',
            'Estante Inferior',
            'Zona de Acceso Rápido'
        ];
        
        return $locations[($unitNumber - 1) % count($locations)];
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar todos los items de inventario creados por esta migración
        $inventoryItems = InventoryItem::whereNotNull('created_at')
            ->where('created_at', '>=', now()->subHour())
            ->get();
            
        foreach ($inventoryItems as $item) {
            // Eliminar unidades asociadas
            InventoryUnit::where('inventory_item_id', $item->id)->delete();
            // Eliminar el item
            $item->delete();
        }
        
        echo "Rollback completado. Eliminados " . $inventoryItems->count() . " items de inventario.\n";
    }
};
