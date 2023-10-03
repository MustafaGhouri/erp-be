<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounterLog extends Model
{
    use HasFactory;

    protected $table = 'counter_logs';

    protected $fillable = [
        "printer",
        "user_id",
        "complaint_id",
        "before_counter",
        "counter",
        "counter_file",
        "log_type",
        "region", 
        "customer",
        "location",
        "department", 
    ];
}
