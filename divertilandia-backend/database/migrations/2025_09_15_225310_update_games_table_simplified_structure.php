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
        Schema::table('games', function (Blueprint $table) {
            // Eliminar campos de mantenimiento que no necesitamos
            $table->dropColumn([
                'last_maintenance',
                'next_maintenance'
            ]);
            
            // Agregar llave foránea a toy_types
            $table->unsignedBigInteger('toy_type_id')->after('type');
            $table->foreign('toy_type_id')->references('id')->on('toy_types')->onDelete('cascade');
            
            // Agregar precio unitario
            $table->decimal('unit_price', 10, 2)->default(0)->after('quantity');
            
            // Agregar campo para imagen en base64
            $table->longText('image_base64')->nullable()->after('condition');
            
            // Renombrar is_available a status para ser más claro
            $table->renameColumn('is_available', 'is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Revertir cambios
            $table->dropForeign(['toy_type_id']);
            $table->dropColumn(['toy_type_id', 'unit_price', 'image_base64']);
            $table->renameColumn('is_active', 'is_available');
            
            // Restaurar campos de mantenimiento
            $table->date('last_maintenance');
            $table->date('next_maintenance');
        });
    }
};
