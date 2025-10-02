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
        // Agregar columnas para contar unidades por estado a la tabla games
        Schema::table('games', function (Blueprint $table) {
            // Contadores por estado de salud
            $table->integer('excellent_count')->default(0)->after('quantity');
            $table->integer('good_count')->default(0)->after('excellent_count');
            $table->integer('fair_count')->default(0)->after('good_count');
            $table->integer('poor_count')->default(0)->after('fair_count');
            $table->integer('broken_count')->default(0)->after('poor_count');
            
            // Contadores por estado de disponibilidad
            $table->integer('available_count')->default(0)->after('broken_count');
            $table->integer('in_use_count')->default(0)->after('available_count');
            $table->integer('maintenance_count')->default(0)->after('in_use_count');
            $table->integer('retired_count')->default(0)->after('maintenance_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'excellent_count',
                'good_count', 
                'fair_count',
                'poor_count',
                'broken_count',
                'available_count',
                'in_use_count',
                'maintenance_count',
                'retired_count'
            ]);
        });
    }
};