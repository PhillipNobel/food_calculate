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
        Schema::table('product_stock', function (Blueprint $table) {
            // Altera a coluna 'quantity' para aceitar valores decimais
            $table->decimal('quantity', 8, 2)->change(); // 8 dígitos no total, 2 casas decimais
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_stock', function (Blueprint $table) {
            // Reverte para inteiro se necessário
            $table->integer('quantity')->change();
        });
    }
};
