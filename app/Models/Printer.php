<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
