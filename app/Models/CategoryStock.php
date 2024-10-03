<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryStock extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function parent()
    {
        return $this->belongsTo(CategoryStock::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CategoryStock::class, 'parent_id');
    }
    
    public function canDelete(): bool
    {
        return !$this->stocks()->exists();
    }
}


