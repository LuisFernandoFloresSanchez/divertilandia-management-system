<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Información de contacto
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->string('secondary_phone')->nullable();
            
            // Ubicación
            $table->text('address');
            $table->string('google_maps_url')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Fecha y hora del evento
            $table->date('event_date');
            $table->time('start_time');
            
            // Duración y horas extras
            $table->integer('extra_hours')->default(0); // 0, 1, 2, o 3 horas extras
            $table->decimal('extra_hours_cost', 8, 2)->default(0); // Costo total de horas extras
            
            // Paquete y anticipo
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->decimal('advance_payment', 8, 2)->default(300.00); // Anticipo fijo de $300
            
            // Estado del evento
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            
            // Notas adicionales
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn([
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
                'extra_hours_cost',
                'package_id',
                'advance_payment',
                'status',
                'notes'
            ]);
        });
    }
};
