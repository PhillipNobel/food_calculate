<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'quantity', 'unit_price', 'category_stock_id', 'measurement_unit_id'];

    public function categoryStock()
    {
        return $this->belongsTo(CategoryStock::class, 'category_stock_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function measurementUnit()
    {  
        return $this->belongsTo(StockMeasurementUnit::class, 'measurement_unit_id');
    }

}