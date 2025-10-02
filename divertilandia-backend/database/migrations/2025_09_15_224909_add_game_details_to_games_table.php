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
            $table->string('name');
            $table->string('type');
            $table->integer('quantity')->default(0);
            $table->string('condition');
            $table->date('last_maintenance');
            $table->date('next_maintenance');
            $table->boolean('is_available')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'type',
                'quantity',
                'condition',
                'last_maintenance',
                'next_maintenance',
                'is_available'
            ]);
        });
    }
};
