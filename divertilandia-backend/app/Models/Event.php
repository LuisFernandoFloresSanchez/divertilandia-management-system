<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_name',
        'contact_phone',
        'secondary_phone',
        'address',
        'google_maps_url',
        'latitude',
        'longitude',
        'event_date',
        'start_time',
        'end_time',
        'extra_hours',
        'extra_hours_cost',
        'package_id',
        'discount_percentage',
        'package_discount_amount',
        'advance_payment',
        'status',
        'notes',
        'has_advance_payment',
        'advance_payment_amount',
        'extra_hours_count',
        'extra_tables',
        'extra_chairs',
        'extra_playpens',
        'extra_toys',
        'extra_services',
        'extra_services_cost',
        'tables_cost',
        'chairs_cost',
        'playpens_cost',
        'toys_cost',
        'total_extras_cost',
        'child_gender',
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime:H:i',
        'extra_hours' => 'integer',
        'extra_hours_count' => 'integer',
        'extra_tables' => 'integer',
        'extra_chairs' => 'integer',
        'extra_playpens' => 'integer',
        'extra_toys' => 'integer',
        'has_advance_payment' => 'boolean',
        'advance_payment_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'package_discount_amount' => 'decimal:2',
        'extra_hours_cost' => 'decimal:2',
        'extra_services_cost' => 'decimal:2',
        'tables_cost' => 'decimal:2',
        'chairs_cost' => 'decimal:2',
        'playpens_cost' => 'decimal:2',
        'toys_cost' => 'decimal:2',
        'total_extras_cost' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    // Relación con uso de vehículos
    public function vehicleUsages()
    {
        return $this->hasMany(EventVehicleUsage::class);
    }

    // Relación con gastos relacionados
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // Método para calcular el precio del paquete con descuento aplicado
    public function getPackageFinalPriceAttribute()
    {
        if (!$this->package) {
            return 0;
        }
        
        $packagePrice = $this->package->price;
        $discountAmount = $packagePrice * ($this->discount_percentage / 100);
        return $packagePrice - $discountAmount;
    }

    // Método para obtener el precio total del evento (paquete con descuento + extras)
    public function getTotalEventPriceAttribute()
    {
        return $this->package_final_price + $this->total_extras_cost;
    }
}
