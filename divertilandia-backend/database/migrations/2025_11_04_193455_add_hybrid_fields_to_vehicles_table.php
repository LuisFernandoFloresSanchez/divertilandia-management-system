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
        Schema::table('vehicles', function (Blueprint $table) {
            // Agregar tipo de vehículo (regular o híbrido)
            $table->enum('vehicle_type', ['regular', 'hybrid'])->default('regular')->after('fuel_type');
            
            // Campos para vehículos híbridos
            $table->decimal('battery_capacity_kwh', 5, 2)->nullable()->after('fuel_efficiency'); // Capacidad de batería en kWh
            $table->decimal('electric_range_km', 5, 2)->nullable()->after('battery_capacity_kwh'); // Kilómetros en modo 100% eléctrico
            $table->decimal('battery_min_percentage', 5, 2)->default(25.00)->after('electric_range_km'); // Porcentaje mínimo de batería (25%)
            $table->decimal('hybrid_efficiency_km_per_liter', 5, 2)->nullable()->after('battery_min_percentage'); // Rendimiento combinado PHEV en km/L
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_type',
                'battery_capacity_kwh',
                'electric_range_km',
                'battery_min_percentage',
                'hybrid_efficiency_km_per_liter'
            ]);
        });
    }
};
