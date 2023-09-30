<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;
    protected $table = "product_models";
    protected $fillable = [
        "name",
        "brand",
        "user_id",
    ];
    public function brandDetails()
    {
        return $this->belongsTo(Brand::class,  'brand');
    }
}
