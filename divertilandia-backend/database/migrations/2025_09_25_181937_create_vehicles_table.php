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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del vehículo (ej: "Van Roja", "Pickup Verde")
            $table->string('model')->nullable(); // Modelo del vehículo
            $table->string('year')->nullable(); // Año
            $table->string('plate_number')->nullable(); // Número de placa
            $table->enum('fuel_type', ['regular', 'premium', 'diesel'])->default('regular'); // Tipo de gasolina
            $table->decimal('fuel_efficiency', 5, 2); // Rendimiento en km/litro
            $table->decimal('current_fuel_price', 6, 2); // Precio actual del combustible por litro
            $table->string('color', 7)->default('#000000'); // Color del vehículo (hex)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insertar vehículos por defecto (ejemplo)
        DB::table('vehicles')->insert([
            [
                'name' => 'Van Roja',
                'model' => 'Chevrolet Express',
                'year' => '2020',
                'plate_number' => 'ABC-123',
                'fuel_type' => 'regular',
                'fuel_efficiency' => 8.5, // 8.5 km/litro
                'current_fuel_price' => 25.50,
                'color' => '#F44336',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pickup Verde',
                'model' => 'Ford Ranger',
                'year' => '2019',
                'plate_number' => 'XYZ-789',
                'fuel_type' => 'diesel',
                'fuel_efficiency' => 12.0, // 12 km/litro
                'current_fuel_price' => 26.80,
                'color' => '#4CAF50',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};