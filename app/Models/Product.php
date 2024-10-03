<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name','description', 'category_product_id', 'total_cost'];
    
    public function categoryProducts()
    {
        return $this->belongsTo(CategoryProduct::class, 'category_product_id');
    }

    public function productStocks()
    {
    return $this->hasMany(ProductStock::class);
    }

    public function stocks()
    {
    return $this->belongsToMany(Stock::class);
    }   

    

}
