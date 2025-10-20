<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Log para debugging
        \Log::info('EventController@index - Parámetros recibidos:', [
            'calendar_mode' => $request->get('calendar_mode'),
            'has_calendar_mode' => $request->has('calendar_mode'),
            'all_params' => $request->all()
        ]);

        // Modo ligero para calendario - sin imágenes
        if ($request->has('calendar_mode') && $request->calendar_mode == 'true') {
            \Log::info('Usando modo calendario optimizado');
            return $this->getCalendarEvents($request);
        }

        \Log::warning('Usando modo completo (con imágenes) - NO RECOMENDADO');
        $query = Event::with('package');

        // Filtrar por fecha si se proporciona
        if ($request->has('date')) {
            $query->whereDate('event_date', $request->date);
        }

        // Filtrar por rango de fechas
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('event_date', [$request->start_date, $request->end_date]);
        }

        // Filtrar por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->orderBy('event_date')->orderBy('start_time')->get();

        return response()->json($events);
    }

    /**
     * Obtener eventos optimizados para el calendario (sin imágenes pesadas)
     */
    private function getCalendarEvents(Request $request): JsonResponse
    {
        $query = Event::with(['package' => function ($query) {
            // Solo cargar datos básicos del paquete, sin juegos ni imágenes
            $query->select('id', 'name', 'price', 'max_age', 'is_active');
        }]);

        // Filtrar por fecha si se proporciona
        if ($request->has('date')) {
            $query->whereDate('event_date', $request->date);
        }

        // Filtrar por rango de fechas
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('event_date', [$request->start_date, $request->end_date]);
        }

        // Filtrar por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->orderBy('event_date')->orderBy('start_time')->get();

        \Log::info('getCalendarEvents - Eventos obtenidos:', [
            'total_events' => $events->count(),
            'event_ids' => $events->pluck('id')->toArray()
        ]);

        // Mapear eventos para quitar campos pesados innecesarios
        $lightEvents = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'contact_name' => $event->contact_name,
                'contact_phone' => $event->contact_phone,
                'secondary_phone' => $event->secondary_phone,
                'address' => $event->address,
                'google_maps_url' => $event->google_maps_url,
                'event_date' => $event->event_date ? $event->event_date->format('Y-m-d') : null,
                'start_time' => $event->start_time ? substr($event->start_time, 0, 5) : null, // Solo HH:MM
                'end_time' => $event->end_time ? substr($event->end_time, 0, 5) : null, // Solo HH:MM
                'extra_hours' => $event->extra_hours,
                'package_id' => $event->package_id,
                'package' => $event->package ? [
                    'id' => $event->package->id,
                    'name' => $event->package->name,
                    'price' => $event->package->price,
                ] : null,
                'discount_percentage' => $event->discount_percentage,
                'package_discount_amount' => $event->package_discount_amount,
                'advance_payment' => $event->advance_payment,
                'status' => $event->status,
                'child_gender' => $event->child_gender,
                'notes' => $event->notes,
                'has_advance_payment' => $event->has_advance_payment,
                'advance_payment_amount' => $event->advance_payment_amount,
                'extra_hours_count' => $event->extra_hours_count,
                'extra_tables' => $event->extra_tables,
                'extra_chairs' => $event->extra_chairs,
                'extra_playpens' => $event->extra_playpens,
                'extra_toys' => $event->extra_toys,
                'extra_services' => $event->extra_services,
                'extra_services_cost' => $event->extra_services_cost,
                'tables_cost' => $event->tables_cost,
                'chairs_cost' => $event->chairs_cost,
                'playpens_cost' => $event->playpens_cost,
                'toys_cost' => $event->toys_cost,
                'total_extras_cost' => $event->total_extras_cost,
                'created_at' => $event->created_at ? $event->created_at->toISOString() : null,
                'updated_at' => $event->updated_at ? $event->updated_at->toISOString() : null,
            ];
        });

        \Log::info('getCalendarEvents - Eventos devueltos:', [
            'total_returned' => $lightEvents->count(),
            'returned_ids' => $lightEvents->pluck('id')->toArray()
        ]);

        return response()->json($lightEvents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'google_maps_url' => 'nullable|url',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'event_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'extra_hours' => 'integer|min:0',
            'package_id' => 'required|exists:packages,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'in:pending,confirmed,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'child_gender' => 'nullable|in:male,female',
            
            // Campos de extras
            'has_advance_payment' => 'boolean',
            'advance_payment_amount' => 'nullable|numeric|min:0',
            'extra_hours_count' => 'integer|min:0',
            'extra_tables' => 'integer|min:0',
            'extra_chairs' => 'integer|min:0',
            'extra_playpens' => 'integer|min:0',
            'extra_toys' => 'integer|min:0',
            'extra_services' => 'nullable|string',
            'extra_services_cost' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Debug: Log de la request
            \Log::info('=== DEBUG STORE EVENT ===');
            \Log::info('Request data:', $request->all());
            \Log::info('child_gender: ' . $request->input('child_gender'));
            \Log::info('========================');

            // Calcular costos de extras
            $extrasCosts = $this->calculateExtrasCost($request);
            
            $eventData = $request->only([
                'contact_name',
                'contact_phone',
                'secondary_phone',
                'address',
                'google_maps_url',
                'latitude',
                'longitude',
                'event_date',
                'start_time',
                'extra_hours',
                'package_id',
                'discount_percentage',
                'status',
                'notes',
                'child_gender',
                'has_advance_payment',
                'advance_payment_amount',
                'extra_hours_count',
                'extra_tables',
                'extra_chairs',
                'extra_playpens',
                'extra_toys',
                'extra_services',
                'extra_services_cost',
            ]);

            // Agregar costos calculados
            $eventData = array_merge($eventData, $extrasCosts);

            // Calcular costo de horas extras (usando el campo extra_hours_count)
            $eventData['extra_hours_cost'] = $eventData['extra_hours_count'] * 100;

            // Anticipo por defecto
            $eventData['advance_payment'] = $eventData['advance_payment_amount'] ?? 300;

            // Calcular descuento del paquete
            $package = Package::find($eventData['package_id']);
            $discountPercentage = $eventData['discount_percentage'] ?? 0;
            $eventData['package_discount_amount'] = $package ? ($package->price * $discountPercentage / 100) : 0;

            // Calcular end_time automáticamente
            $eventData['end_time'] = $this->calculateEndTime($eventData['start_time'], $eventData['extra_hours_count'] ?? 0);

            $event = Event::create($eventData);
            $event->load('package');

            DB::commit();

            return response()->json($event, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear el evento: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $event = Event::with('package')->findOrFail($id);
        return response()->json($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'contact_name' => 'sometimes|string|max:255',
            'contact_phone' => 'sometimes|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'address' => 'sometimes|string',
            'google_maps_url' => 'nullable|url',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'event_date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'extra_hours' => 'sometimes|integer|min:0',
            'package_id' => 'sometimes|exists:packages,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'sometimes|in:pending,confirmed,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'child_gender' => 'nullable|in:male,female',
            
            // Campos de extras
            'has_advance_payment' => 'sometimes|boolean',
            'advance_payment_amount' => 'nullable|numeric|min:0',
            'extra_hours_count' => 'sometimes|integer|min:0',
            'extra_tables' => 'sometimes|integer|min:0',
            'extra_chairs' => 'sometimes|integer|min:0',
            'extra_playpens' => 'sometimes|integer|min:0',
            'extra_toys' => 'sometimes|integer|min:0',
            'extra_services' => 'nullable|string',
            'extra_services_cost' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Debug: Log de la request
            \Log::info('=== DEBUG UPDATE EVENT ===');
            \Log::info('Event ID: ' . $id);
            \Log::info('Request data:', $request->all());
            \Log::info('child_gender: ' . $request->input('child_gender'));
            \Log::info('========================');

            // Calcular costos de extras si se proporcionan
            if ($request->hasAny(['extra_hours_count', 'extra_tables', 'extra_chairs', 'extra_playpens', 'extra_toys', 'extra_services_cost'])) {
                $extrasCosts = $this->calculateExtrasCost($request);
                
                $updateData = $request->only([
                    'contact_name',
                    'contact_phone',
                    'secondary_phone',
                    'address',
                    'google_maps_url',
                    'latitude',
                    'longitude',
                    'event_date',
                    'start_time',
                    'extra_hours',
                    'package_id',
                    'discount_percentage',
                    'status',
                    'notes',
                    'child_gender',
                    'has_advance_payment',
                    'advance_payment_amount',
                    'extra_hours_count',
                    'extra_tables',
                    'extra_chairs',
                    'extra_playpens',
                    'extra_toys',
                    'extra_services',
                    'extra_services_cost',
                ]);

                // Agregar costos calculados
                $updateData = array_merge($updateData, $extrasCosts);

                // Calcular costo de horas extras
                $updateData['extra_hours_cost'] = $updateData['extra_hours_count'] * 100;

                // Actualizar anticipo si se proporciona
                if (isset($updateData['advance_payment_amount'])) {
                    $updateData['advance_payment'] = $updateData['advance_payment_amount'];
                }

                // Calcular descuento del paquete si se proporciona
                if (isset($updateData['package_id']) || isset($updateData['discount_percentage'])) {
                    $packageId = $updateData['package_id'] ?? $event->package_id;
                    $package = Package::find($packageId);
                    $discountPercentage = $updateData['discount_percentage'] ?? $event->discount_percentage ?? 0;
                    $updateData['package_discount_amount'] = $package ? ($package->price * $discountPercentage / 100) : 0;
                }

                // Calcular end_time automáticamente siempre
                $startTime = $updateData['start_time'] ?? $event->start_time;
                $extraHours = $updateData['extra_hours_count'] ?? $event->extra_hours_count ?? 0;
                $updateData['end_time'] = $this->calculateEndTime($startTime, $extraHours);

                $event->update($updateData);
            } else {
                $updateData = $request->only([
                    'contact_name',
                    'contact_phone',
                    'secondary_phone',
                    'address',
                    'google_maps_url',
                    'latitude',
                    'longitude',
                    'event_date',
                    'start_time',
                    'extra_hours',
                    'package_id',
                    'discount_percentage',
                    'status',
                    'notes',
                    'child_gender',
                    'has_advance_payment',
                    'advance_payment_amount',
                ]);

                // Calcular descuento del paquete si se proporciona
                if (isset($updateData['package_id']) || isset($updateData['discount_percentage'])) {
                    $packageId = $updateData['package_id'] ?? $event->package_id;
                    $package = Package::find($packageId);
                    $discountPercentage = $updateData['discount_percentage'] ?? $event->discount_percentage ?? 0;
                    $updateData['package_discount_amount'] = $package ? ($package->price * $discountPercentage / 100) : 0;
                }

                // Calcular end_time automáticamente siempre
                $startTime = $updateData['start_time'] ?? $event->start_time;
                $extraHours = $updateData['extra_hours_count'] ?? $event->extra_hours_count ?? 0;
                $updateData['end_time'] = $this->calculateEndTime($startTime, $extraHours);

                $event->update($updateData);
            }

            $event->load('package');

            DB::commit();

            return response()->json($event);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar el evento: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Evento eliminado exitosamente']);
    }

    /**
     * Get events by date
     */
    public function getByDate(string $date): JsonResponse
    {
        $events = Event::with('package')
            ->whereDate('event_date', $date)
            ->orderBy('start_time')
            ->get();

        return response()->json($events);
    }

    /**
     * Get events by date range
     */
    public function getByDateRange(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $events = Event::with('package')
            ->whereBetween('event_date', [$request->start_date, $request->end_date])
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->get();

        return response()->json($events);
    }

    /**
     * Get events by status
     */
    public function getByStatus(string $status): JsonResponse
    {
        $events = Event::with('package')
            ->where('status', $status)
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->get();

        return response()->json($events);
    }

    /**
     * Calculate extras costs
     */
    private function calculateExtrasCost(Request $request): array
    {
        $extraHourCost = 100;
        $tableCost = 100;
        $chairCost = 100;
        $playpenCost = 200; // por cada 8 corralitos
        $toyCost = 100; // por juguete extra

        $extraHoursCount = $request->get('extra_hours_count', 0);
        $extraTables = $request->get('extra_tables', 0);
        $extraChairs = $request->get('extra_chairs', 0);
        $extraPlaypens = $request->get('extra_playpens', 0);
        $extraToys = $request->get('extra_toys', 0);
        $extraServicesCost = $request->get('extra_services_cost', 0);

        $hoursCost = $extraHoursCount * $extraHourCost;
        $tablesCost = $extraTables * $tableCost;
        $chairsCost = $extraChairs * $chairCost;
        $playpensCost = ceil($extraPlaypens / 8) * $playpenCost;
        $toysCost = $extraToys * $toyCost;

        $totalExtrasCost = $hoursCost + $tablesCost + $chairsCost + $playpensCost + $toysCost + $extraServicesCost;

        return [
            'tables_cost' => $tablesCost,
            'chairs_cost' => $chairsCost,
            'playpens_cost' => $playpensCost,
            'toys_cost' => $toysCost,
            'total_extras_cost' => $totalExtrasCost,
        ];
    }

    /**
     * Calcular la hora de finalización del evento
     * Base: 4 horas + horas extras
     */
    private function calculateEndTime($startTime, $extraHours = 0)
    {
        // Usar Carbon para parsear cualquier formato de fecha/hora
        $start = \Carbon\Carbon::parse($startTime);
        
        // Agregar 4 horas base + horas extras
        $totalHours = 4 + $extraHours;
        $end = $start->addHours($totalHours);
        
        // Retornar en formato H:i
        return $end->format('H:i');
    }
}
