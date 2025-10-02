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
        Schema::create('toy_clauses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre de la cláusula (ej: "Daño a bomba de aire")
            $table->text('description'); // Descripción de la cláusula (ej: "Si se daña la bomba de aire del brinca-brinca el costo a pagar por la misma será de $1,500 pesos.")
            $table->boolean('is_active')->default(true); // Si la cláusula está activa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toy_clauses');
    }
};
