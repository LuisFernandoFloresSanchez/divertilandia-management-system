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
        Schema::create('fuel_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('fuel_type', ['regular', 'premium']); // regular = gasolina roja, premium = gasolina verde
            $table->decimal('price_per_liter', 8, 2); // Precio por litro
            $table->date('effective_date'); // Fecha desde cuando es válido este precio
            $table->boolean('is_active')->default(true); // Si este es el precio actual
            $table->text('notes')->nullable(); // Notas sobre el cambio de precio
            $table->timestamps();
            
            // Índice para búsquedas rápidas por tipo de combustible y fecha
            $table->index(['fuel_type', 'effective_date']);
            $table->index(['fuel_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_prices');
    }
};
