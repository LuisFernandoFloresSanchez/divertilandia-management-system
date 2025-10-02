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
        Schema::table('events', function (Blueprint $table) {
            // Campos de extras y servicios adicionales
            $table->boolean('has_advance_payment')->default(false);
            $table->decimal('advance_payment_amount', 8, 2)->nullable();
            $table->integer('extra_hours_count')->default(0);
            $table->integer('extra_tables')->default(0);
            $table->integer('extra_chairs')->default(0);
            $table->integer('extra_playpens')->default(0);
            $table->integer('extra_toys')->default(0);
            $table->text('extra_services')->nullable();
            $table->decimal('extra_services_cost', 8, 2)->nullable();
            
            // Costos de extras
            $table->decimal('tables_cost', 8, 2)->default(0);
            $table->decimal('chairs_cost', 8, 2)->default(0);
            $table->decimal('playpens_cost', 8, 2)->default(0);
            $table->decimal('toys_cost', 8, 2)->default(0);
            $table->decimal('total_extras_cost', 8, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'has_advance_payment',
                'advance_payment_amount',
                'extra_hours_count',
                'extra_tables',
                'extra_chairs',
                'extra_playpens',
                'extra_toys',
                'extra_services',
                'extra_services_cost',
                'tables_cost',
                'chairs_cost',
                'playpens_cost',
                'toys_cost',
                'total_extras_cost',
            ]);
        });
    }
};
