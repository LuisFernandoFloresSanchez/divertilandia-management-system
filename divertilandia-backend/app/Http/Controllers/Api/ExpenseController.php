<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Expense::with(['category', 'event']);

        // Filtrar por categoría
        if ($request->has('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        // Filtrar por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtrar por rango de fechas
        if ($request->has('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        return response()->json($expenses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'concept' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'receipt_image' => 'nullable|string',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'event_id' => 'nullable|exists:events,id',
            'status' => 'required|in:pending,paid',
            'expense_date' => 'required|date',
            'payment_date' => 'nullable|date|after_or_equal:expense_date'
        ], [
            'concept.required' => 'El concepto es requerido',
            'amount.required' => 'El monto es requerido',
            'amount.min' => 'El monto debe ser mayor a 0',
            'expense_category_id.required' => 'La categoría es requerida',
            'expense_category_id.exists' => 'La categoría seleccionada no existe',
            'status.required' => 'El estado es requerido',
            'status.in' => 'El estado debe ser "pending" o "paid"',
            'expense_date.required' => 'La fecha del gasto es requerida',
            'expense_date.date' => 'La fecha del gasto no es válida',
            'payment_date.after_or_equal' => 'La fecha de pago debe ser igual o posterior a la fecha del gasto'
        ]);

        $expense = Expense::create($validated);
        $expense->load(['category', 'event']);

        return response()->json($expense, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $expense = Expense::with(['category', 'event'])->findOrFail($id);
        return response()->json($expense);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'concept' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'sometimes|numeric|min:0.01',
            'receipt_image' => 'nullable|string',
            'expense_category_id' => 'sometimes|exists:expense_categories,id',
            'event_id' => 'nullable|exists:events,id',
            'status' => 'sometimes|in:pending,paid',
            'expense_date' => 'sometimes|date',
            'payment_date' => 'nullable|date|after_or_equal:expense_date'
        ], [
            'concept.string' => 'El concepto debe ser texto',
            'concept.max' => 'El concepto no puede tener más de 255 caracteres',
            'amount.numeric' => 'El monto debe ser numérico',
            'amount.min' => 'El monto debe ser mayor a 0',
            'expense_category_id.exists' => 'La categoría seleccionada no existe',
            'status.in' => 'El estado debe ser "pending" o "paid"',
            'expense_date.date' => 'La fecha del gasto no es válida',
            'payment_date.after_or_equal' => 'La fecha de pago debe ser igual o posterior a la fecha del gasto'
        ]);

        $expense->update($validated);
        $expense->load(['category', 'event']);

        return response()->json($expense);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json(['message' => 'Gasto eliminado exitosamente']);
    }

    /**
     * Marcar gasto como pagado
     */
    public function markAsPaid(string $id): JsonResponse
    {
        $expense = Expense::findOrFail($id);
        $expense->update([
            'status' => 'paid',
            'payment_date' => now()->toDateString()
        ]);

        return response()->json($expense);
    }

    /**
     * Obtener resumen de gastos
     */
    public function summary(Request $request): JsonResponse
    {
        $query = Expense::query();

        // Filtrar por rango de fechas si se proporciona
        if ($request->has('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $summary = [
            'total_amount' => $query->sum('amount'),
            'pending_amount' => $query->where('status', 'pending')->sum('amount'),
            'paid_amount' => $query->where('status', 'paid')->sum('amount'),
            'total_count' => $query->count(),
            'pending_count' => $query->where('status', 'pending')->count(),
            'paid_count' => $query->where('status', 'paid')->count(),
        ];

        return response()->json($summary);
    }
}