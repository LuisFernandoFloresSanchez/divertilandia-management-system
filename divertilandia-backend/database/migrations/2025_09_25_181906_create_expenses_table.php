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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('concept'); // Concepto del gasto
            $table->text('description')->nullable(); // Descripción detallada
            $table->decimal('amount', 10, 2); // Cantidad de dinero
            $table->string('receipt_image')->nullable(); // Imagen del recibo/ticket en base64
            $table->foreignId('expense_category_id')->constrained('expense_categories');
            $table->foreignId('event_id')->nullable()->constrained('events'); // Si está relacionado con un evento
            $table->enum('status', ['pending', 'paid'])->default('pending'); // Estado de pago
            $table->date('expense_date'); // Fecha del gasto
            $table->date('payment_date')->nullable(); // Fecha de pago
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};