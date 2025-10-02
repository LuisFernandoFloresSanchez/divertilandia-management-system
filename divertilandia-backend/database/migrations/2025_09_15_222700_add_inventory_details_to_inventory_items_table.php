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
        Schema::table('inventory_items', function (Blueprint $table) {
            // Información básica del juguete
            $table->string('name'); // Nombre del juguete
            $table->text('description')->nullable(); // Descripción del juguete
            
            // Estado y salud del juguete
            $table->enum('health_status', ['excellent', 'good', 'fair', 'poor', 'broken'])->default('good');
            $table->enum('status', ['available', 'in_use', 'maintenance', 'retired'])->default('available');
            
            // Cantidad y precio
            $table->integer('quantity')->default(1); // Cantidad disponible
            $table->decimal('unit_price', 10, 2)->nullable(); // Precio unitario
            
            // Relación con tipo de juguete
            $table->foreignId('toy_type_id')->constrained()->onDelete('cascade');
            
            // Información adicional
            $table->string('brand')->nullable(); // Marca del juguete
            $table->string('model')->nullable(); // Modelo del juguete
            $table->string('serial_number')->nullable(); // Número de serie
            $table->date('purchase_date')->nullable(); // Fecha de compra
            $table->text('notes')->nullable(); // Notas adicionales
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropForeign(['toy_type_id']);
            $table->dropColumn([
                'name',
                'description',
                'health_status',
                'status',
                'quantity',
                'unit_price',
                'toy_type_id',
                'brand',
                'model',
                'serial_number',
                'purchase_date',
                'notes'
            ]);
        });
    }
};
