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
        // Renomeia a tabela 'stock_measurement_unit' para 'stock_measurement_units'
        Schema::rename('stock_measurement_unit', 'stock_measurement_units');

        // Atualiza a tabela 'stocks' para ajustar a chave estrangeira
        Schema::table('stocks', function (Blueprint $table) {
            // Remove a chave estrangeira antiga
            $table->dropForeign(['measurement_unit_id']);

            // Adiciona a nova chave estrangeira
            $table->foreign('measurement_unit_id')
                  ->references('id')
                  ->on('stock_measurement_units')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('stock_measurement_units', 'stock_measurement_unit');

        // Reverte a chave estrangeira na tabela 'stocks'
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign(['measurement_unit_id']);

            $table->foreign('measurement_unit_id')
                  ->references('id')
                  ->on('stock_measurement_unit')
                  ->onDelete('set null');
        });
    }
};
