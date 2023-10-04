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
    public function printerDetails()
    {
        return $this->belongsTo(Printer::class,  'printer');
    }
    public function locationDetails()
    {
        return $this->belongsTo(Location::class,  'location');
    }
    public function customerDetails()
    {
        return $this->belongsTo(Customer::class,  'customer');
    }
    public function regionDetails()
    {
        return $this->belongsTo(Region::class,  'region');
    }
    public function complaintDetails()
    {
        return $this->belongsTo(Complaint::class,  'complaint_id');
    }
    public function userDetails()
    {
        return $this->belongsTo(User::class,  'user_id');
    }
}
