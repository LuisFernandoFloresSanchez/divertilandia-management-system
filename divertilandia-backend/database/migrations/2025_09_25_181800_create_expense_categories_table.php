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
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ej: "Compra de Juguetes", "Publicidad", "Gasolina", "Reparaciones"
            $table->string('description')->nullable();
            $table->string('color', 7)->default('#2196F3'); // Color hexadecimal para UI
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insertar categorías por defecto
        DB::table('expense_categories')->insert([
            [
                'name' => 'Compra de Juguetes',
                'description' => 'Compra de nuevos juguetes o equipos',
                'color' => '#4CAF50',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Reparaciones',
                'description' => 'Reparación de juguetes o equipos',
                'color' => '#FF9800',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gasolina',
                'description' => 'Combustible para vehículos',
                'color' => '#F44336',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Publicidad',
                'description' => 'Gastos en publicidad y marketing',
                'color' => '#9C27B0',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carga de Saldo',
                'description' => 'Recarga de saldo para servicios',
                'color' => '#2196F3',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mantenimiento',
                'description' => 'Mantenimiento general de equipos',
                'color' => '#607D8B',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Otros',
                'description' => 'Otros gastos diversos',
                'color' => '#795548',
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
        Schema::dropIfExists('expense_categories');
    }
};