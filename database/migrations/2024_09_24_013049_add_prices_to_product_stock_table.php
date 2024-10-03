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
            $table->decimal('unit_price', 8, 2)->nullable();
            $table->decimal('ingredient_price', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_stock', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'ingredient_price']);
        });
    }
};
