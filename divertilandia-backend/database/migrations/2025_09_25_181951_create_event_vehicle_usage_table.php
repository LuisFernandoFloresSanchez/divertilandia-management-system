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
        Schema::create('event_vehicle_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->decimal('kilometers', 8, 2); // Kilómetros recorridos
            $table->decimal('fuel_cost', 8, 2); // Costo calculado de gasolina
            $table->enum('status', ['pending', 'paid'])->default('pending'); // Estado de pago
            $table->text('notes')->nullable(); // Notas sobre el uso del vehículo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_vehicle_usage');
    }
};