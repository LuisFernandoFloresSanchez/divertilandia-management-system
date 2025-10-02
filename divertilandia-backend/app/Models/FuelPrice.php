<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FuelPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'fuel_type',
        'price_per_liter',
        'effective_date',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'price_per_liter' => 'decimal:2',
        'effective_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
        });
    }

    /**
     * Obtener el precio actual de un tipo de combustible
     */
    public static function getCurrentPrice($fuelType)
    {
        return self::where('fuel_type', $fuelType)
                   ->where('is_active', true)
                   ->orderBy('effective_date', 'desc')
                   ->first();
    }

    /**
     * Obtener todos los precios activos
     */
    public static function getCurrentPrices()
    {
        return self::where('is_active', true)
                   ->orderBy('fuel_type')
                   ->orderBy('effective_date', 'desc')
                   ->get();
    }

    /**
     * Establecer un nuevo precio como activo y desactivar los anteriores
     */
    public static function setNewPrice($fuelType, $pricePerLiter, $effectiveDate, $notes = null)
    {
        // Desactivar todos los precios anteriores del mismo tipo
        self::where('fuel_type', $fuelType)->update(['is_active' => false]);

        // Crear el nuevo precio
        return self::create([
            'fuel_type' => $fuelType,
            'price_per_liter' => $pricePerLiter,
            'effective_date' => $effectiveDate,
            'is_active' => true,
            'notes' => $notes
        ]);
    }

    /**
     * Accessor para formatear el tipo de combustible
     */
    public function getFuelTypeFormattedAttribute()
    {
        return $this->fuel_type === 'regular' ? 'Gasolina Roja' : 'Gasolina Verde';
    }

    /**
     * Accessor para formatear el precio
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price_per_liter, 2);
    }
}
