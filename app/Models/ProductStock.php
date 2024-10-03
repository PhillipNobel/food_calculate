<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductStock extends Pivot
{
    protected $fillable = ['stock_id', 'quantity', 'unit_price', 'ingredient_price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}