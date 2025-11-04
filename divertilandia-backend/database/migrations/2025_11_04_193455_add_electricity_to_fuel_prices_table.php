<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar el enum para incluir 'electricity'
        // Nota: Laravel no soporta modificar enums directamente, necesitamos hacerlo manualmente
        DB::statement("ALTER TABLE fuel_prices MODIFY COLUMN fuel_type ENUM('regular', 'premium', 'diesel', 'electricity') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el enum a los valores originales
        // Primero eliminar registros de electricidad si existen
        DB::table('fuel_prices')->where('fuel_type', 'electricity')->delete();
        
        // Revertir el enum
        DB::statement("ALTER TABLE fuel_prices MODIFY COLUMN fuel_type ENUM('regular', 'premium', 'diesel') NOT NULL");
    }
};
