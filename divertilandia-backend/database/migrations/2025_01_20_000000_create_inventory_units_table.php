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
        Schema::create('inventory_units', function (Blueprint $table) {
            $table->id();
            
            // Relación con el item de inventario
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            
            // Identificación individual de la unidad
            $table->string('unit_identifier')->nullable(); // Ej: "Alberca-001", "Inflable-A", etc.
            $table->string('serial_number')->nullable(); // Número de serie específico
            
            // Estado individual de la unidad
            $table->enum('health_status', ['excellent', 'good', 'fair', 'poor', 'broken'])->default('good');
            $table->enum('status', ['available', 'in_use', 'maintenance', 'retired'])->default('available');
            
            // Información específica de la unidad
            $table->string('location')->nullable(); // Ubicación específica (Almacén A, Estante 3, etc.)
            $table->text('notes')->nullable(); // Notas específicas de esta unidad
            
            // Fechas importantes
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['inventory_item_id', 'status']);
            $table->index(['health_status', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_units');
    }
};
