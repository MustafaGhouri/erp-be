<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Claims\Custom;

class Printer extends Model
{
    use HasFactory;

    protected $table = "printers";
    protected $fillable = [
        'name',
        'serial_number',
        'brand',
        'model',
        'region',
        'location',
        'department',
        'customer',
        'counter',
        'user_id',
        'qrCode',
    ];


    public function region_detail()
    {
        return $this->belongsTo(Region::class, 'region', 'id');
    }

    public function customer_detail()
    {
        return $this->belongsTo(Customer::class, 'customer', 'id');
    }

    public function location_detail()
    {
        return $this->belongsTo(Location::class, 'location', 'id');
    }

    public function department_detail()
    {
        return $this->belongsTo(Department::class, 'department', 'id');
    }

    public function brand_detail()
    {
        return $this->belongsTo(Brand::class, 'brand', 'id');
    }

    public function model_detail()
    {
        return $this->belongsTo(ProductModel::class, 'model', 'id');
    }
    public function user_detail()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
