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
        Schema::create('event_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique(); // Clave única para cada configuración
            $table->string('setting_name'); // Nombre descriptivo
            $table->text('setting_value'); // Valor de la configuración
            $table->text('description')->nullable(); // Descripción de qué hace esta configuración
            $table->boolean('is_active')->default(true); // Si la configuración está activa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_settings');
    }
};
