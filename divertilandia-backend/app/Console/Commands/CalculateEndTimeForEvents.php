<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Carbon\Carbon;

class CalculateEndTimeForEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:calculate-end-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate end_time for all existing events that don\'t have it';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Calculating end_time for existing events...');
        
        // Obtener todos los eventos que no tienen end_time o tienen end_time null
        $events = Event::whereNull('end_time')->orWhere('end_time', '')->get();
        
        $this->info("Found {$events->count()} events without end_time");
        
        $updated = 0;
        
        foreach ($events as $event) {
            if ($event->start_time) {
                $endTime = $this->calculateEndTime($event->start_time, $event->extra_hours_count ?? 0);
                $event->end_time = $endTime;
                $event->save();
                $updated++;
                
                $this->line("Event ID {$event->id}: {$event->start_time} -> {$endTime} (extra hours: {$event->extra_hours_count})");
            }
        }
        
        $this->info("Updated {$updated} events with calculated end_time");
    }
    
    /**
     * Calculate end time based on start time and extra hours
     */
    private function calculateEndTime($startTime, $extraHours = 0)
    {
        // Usar Carbon para parsear cualquier formato de fecha/hora
        $start = Carbon::parse($startTime);
        
        // Agregar 4 horas base + horas extras
        $totalHours = 4 + $extraHours;
        $end = $start->addHours($totalHours);
        
        // Retornar en formato H:i
        return $end->format('H:i');
    }
}
