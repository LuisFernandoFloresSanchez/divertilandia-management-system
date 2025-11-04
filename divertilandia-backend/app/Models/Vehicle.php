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
        'vehicle_type',
        'fuel_efficiency',
        'battery_capacity_kwh',
        'electric_range_km',
        'battery_min_percentage',
        'hybrid_efficiency_km_per_liter',
        'color',
        'is_active'
    ];

    protected $casts = [
        'fuel_efficiency' => 'decimal:2',
        'battery_capacity_kwh' => 'decimal:2',
        'electric_range_km' => 'decimal:2',
        'battery_min_percentage' => 'decimal:2',
        'hybrid_efficiency_km_per_liter' => 'decimal:2',
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
            'diesel' => 'Diésel',
            'electricity' => 'Electricidad'
        ];
        return $types[$this->fuel_type] ?? $this->fuel_type;
    }

    // Verificar si el vehículo es híbrido
    public function isHybrid(): bool
    {
        return $this->vehicle_type === 'hybrid';
    }

    // Obtener el precio actual de electricidad
    public function getCurrentElectricityPrice()
    {
        $electricityPrice = FuelPrice::getCurrentPrice('electricity');
        return $electricityPrice ? $electricityPrice->price_per_liter : 0; // Usamos price_per_liter para kWh
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
        // Si es vehículo híbrido, usar cálculo híbrido
        if ($this->isHybrid()) {
            return $this->calculateHybridCost($kilometers);
        }
        
        // Cálculo tradicional para vehículos regulares
        $currentPrice = $this->getCurrentFuelPrice();
        if ($currentPrice <= 0) return 0;
        
        $litersNeeded = $kilometers / $this->fuel_efficiency;
        return $litersNeeded * $currentPrice;
    }

    // Método para calcular el costo de un vehículo híbrido
    public function calculateHybridCost($kilometers)
    {
        if (!$this->isHybrid()) {
            return 0;
        }

        // Calcular rango eléctrico efectivo (considerando que la batería cambia al 25%)
        // El rango efectivo es 75% del rango total (100% - 25% mínimo)
        $effectiveElectricRange = $this->electric_range_km * (1 - ($this->battery_min_percentage / 100));
        
        $electricityPrice = $this->getCurrentElectricityPrice();
        $fuelPrice = $this->getCurrentFuelPrice();
        $hybridEfficiency = $this->hybrid_efficiency_km_per_liter;
        
        $totalCost = 0;
        
        // Si los kilómetros están dentro del rango eléctrico efectivo
        if ($kilometers <= $effectiveElectricRange) {
            // Calcular kWh consumidos
            // Consumo = (kilometers / electric_range_km) * battery_capacity_kwh
            $kwhConsumed = ($kilometers / $this->electric_range_km) * $this->battery_capacity_kwh;
            $totalCost = $kwhConsumed * $electricityPrice;
        } else {
            // Parte eléctrica (hasta el límite efectivo)
            $electricKm = $effectiveElectricRange;
            $kwhConsumed = ($electricKm / $this->electric_range_km) * $this->battery_capacity_kwh;
            $electricCost = $kwhConsumed * $electricityPrice;
            
            // Parte con gasolina (kilómetros restantes)
            $fuelKm = $kilometers - $effectiveElectricRange;
            $litersNeeded = $fuelKm / $hybridEfficiency;
            $fuelCost = $litersNeeded * $fuelPrice;
            
            $totalCost = $electricCost + $fuelCost;
        }
        
        return round($totalCost, 2);
    }
}