<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'concept',
        'description',
        'amount',
        'receipt_image',
        'expense_category_id',
        'event_id',
        'status',
        'expense_date',
        'payment_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'payment_date' => 'date'
    ];

    // Relación con categoría
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    // Relación con evento (opcional)
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Scope para gastos pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope para gastos pagados
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Accessor para formatear el monto
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }

    // Accessor para el estado formateado
    public function getStatusFormattedAttribute()
    {
        return $this->status === 'paid' ? 'Pagado' : 'Pendiente';
    }
}