<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model',
        'year',
        'plate_number',
        'fuel_type',
        'fuel_efficiency',
        'color',
        'is_active'
    ];

    protected $casts = [
        'fuel_efficiency' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relación con uso de vehículos en eventos
    public function eventUsages()
    {
        return $this->hasMany(EventVehicleUsage::class);
    }

    // Scope para vehículos activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessor para el tipo de combustible formateado
    public function getFuelTypeFormattedAttribute()
    {
        $types = [
            'regular' => 'Gasolina Roja',
            'premium' => 'Gasolina Verde',
            'diesel' => 'Diésel'
        ];
        return $types[$this->fuel_type] ?? $this->fuel_type;
    }

    // Método para obtener el precio actual del combustible
    public function getCurrentFuelPrice()
    {
        $fuelPrice = FuelPrice::getCurrentPrice($this->fuel_type);
        return $fuelPrice ? $fuelPrice->price_per_liter : 0;
    }

    // Método para calcular el costo de gasolina por kilómetro
    public function getCostPerKilometerAttribute()
    {
        $currentPrice = $this->getCurrentFuelPrice();
        return $currentPrice > 0 ? $currentPrice / $this->fuel_efficiency : 0;
    }

    // Método para calcular el costo de gasolina para una distancia específica
    public function calculateFuelCost($kilometers)
    {
        $currentPrice = $this->getCurrentFuelPrice();
        if ($currentPrice <= 0) return 0;
        
        $litersNeeded = $kilometers / $this->fuel_efficiency;
        return $litersNeeded * $currentPrice;
    }
}