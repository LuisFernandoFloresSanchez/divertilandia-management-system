<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'toy_type_id',
        'quantity',
        'unit_price',
        'condition',
        'image_base64',
        'is_active',
        // Contadores por estado de salud
        'excellent_count',
        'good_count',
        'fair_count',
        'poor_count',
        'broken_count',
        // Contadores por estado de disponibilidad
        'available_count',
        'in_use_count',
        'maintenance_count',
        'retired_count'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relación con ToyType
    public function toyType()
    {
        return $this->belongsTo(ToyType::class);
    }

    /**
     * Obtener el total de unidades disponibles
     */
    public function getAvailableUnitsAttribute()
    {
        return $this->available_count;
    }

    /**
     * Obtener el total de unidades en uso
     */
    public function getInUseUnitsAttribute()
    {
        return $this->in_use_count;
    }

    /**
     * Obtener el total de unidades en mantenimiento
     */
    public function getMaintenanceUnitsAttribute()
    {
        return $this->maintenance_count;
    }

    /**
     * Obtener el total de unidades retiradas
     */
    public function getRetiredUnitsAttribute()
    {
        return $this->retired_count;
    }

    /**
     * Obtener el total de unidades por estado de salud
     */
    public function getHealthStatusCountsAttribute()
    {
        return [
            'excellent' => $this->excellent_count,
            'good' => $this->good_count,
            'fair' => $this->fair_count,
            'poor' => $this->poor_count,
            'broken' => $this->broken_count,
        ];
    }

    /**
     * Obtener el total de unidades por estado de disponibilidad
     */
    public function getAvailabilityStatusCountsAttribute()
    {
        return [
            'available' => $this->available_count,
            'in_use' => $this->in_use_count,
            'maintenance' => $this->maintenance_count,
            'retired' => $this->retired_count,
        ];
    }

    /**
     * Verificar que la suma de contadores sea igual a la cantidad total
     */
    public function validateCounters()
    {
        $healthTotal = $this->excellent_count + $this->good_count + $this->fair_count + 
                      $this->poor_count + $this->broken_count;
        
        $availabilityTotal = $this->available_count + $this->in_use_count + 
                           $this->maintenance_count + $this->retired_count;
        
        return $healthTotal === $this->quantity && $availabilityTotal === $this->quantity;
    }

    /**
     * Relación many-to-many con ToyClause
     */
    public function toyClauses(): BelongsToMany
    {
        return $this->belongsToMany(ToyClause::class, 'game_toy_clause');
    }
}
