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
        // Desactivar la categoría "Gasolina" para que no aparezca en gastos regulares
        DB::table('expense_categories')
            ->where('name', 'Gasolina')
            ->update(['is_active' => false]);

        // Agregar la nueva categoría "BNI"
        DB::table('expense_categories')->insert([
            'name' => 'BNI',
            'description' => 'Gastos relacionados con BNI (Business Network International)',
            'color' => '#1976D2', // Azul
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reactivar la categoría "Gasolina"
        DB::table('expense_categories')
            ->where('name', 'Gasolina')
            ->update(['is_active' => true]);

        // Eliminar la categoría "BNI"
        DB::table('expense_categories')
            ->where('name', 'BNI')
            ->delete();
    }
};
