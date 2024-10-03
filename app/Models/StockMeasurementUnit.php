<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMeasurementUnit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'symbol'];

    public function stocksMeasurement()
    {
        return $this->hasMany(Stock::class, 'measurement_unit_id');
    }
}
