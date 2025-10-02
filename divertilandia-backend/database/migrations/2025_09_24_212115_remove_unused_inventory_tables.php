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
        // Eliminar tablas que no estamos usando
        Schema::dropIfExists('inventory_units');
        Schema::dropIfExists('inventory_items');
        
        echo "Tablas inventory_units e inventory_items eliminadas exitosamente.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recrear las tablas si es necesario (opcional)
        echo "Rollback no implementado para evitar pérdida de datos.\n";
        echo "Si necesitas las tablas, puedes recrearlas manualmente.\n";
    }
};