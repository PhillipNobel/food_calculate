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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price');
        
            // Se havia uma relação direta com estoque, remova a foreign key
            $table->dropForeign(['stock_id']); // Se for o caso
            $table->dropColumn('stock_id');    // Se for o caso
    
            // Adicionar a coluna de descrição
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable();
            $table->foreignId('stock_id')->constrained(); // Se havia a foreign key

            // Remover a coluna de descrição
            $table->dropColumn('description');
        });
    }
};
