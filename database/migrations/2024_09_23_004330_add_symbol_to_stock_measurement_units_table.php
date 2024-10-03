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
        Schema::table('stock_measurement_units', function (Blueprint $table) {
            // Adiciona a coluna 'symbol' para armazenar o símbolo da unidade de medida
            $table->string('symbol')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_measurement_units', function (Blueprint $table) {
            // Remove a coluna 'symbol' caso seja necessário fazer um rollback
            $table->dropColumn('symbol');
        });
    }
};
