<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventVehicleUsage extends Model
{
    use HasFactory;

    protected $table = 'event_vehicle_usage';

    protected $fillable = [
        'event_id',
        'vehicle_id',
        'kilometers',
        'fuel_cost',
        'status',
        'notes'
    ];

    protected $casts = [
        'kilometers' => 'decimal:2',
        'fuel_cost' => 'decimal:2'
    ];

    // Relación con evento
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Relación con vehículo
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Scope para usos pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope para usos pagados
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Accessor para el costo formateado
    public function getFormattedFuelCostAttribute()
    {
        return '$' . number_format($this->fuel_cost, 2);
    }

    // Accessor para el estado formateado
    public function getStatusFormattedAttribute()
    {
        return $this->status === 'paid' ? 'Pagado' : 'Pendiente';
    }
}