<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = "products";
    protected $fillable = [
        "title",
        "serial_number",
        "model_number",
        "slug",
        "category",
        "unit",
        "alert_qty",
        "user_id",
        "qrcode",
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand', 'id');
    }
}
