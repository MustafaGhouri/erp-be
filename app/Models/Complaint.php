<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;
    protected $table = "complaints";
    protected $fillable = [
        "complain_category",
        "problem",
        "screenshot",
        "priority",
        "printer",
        "description",
        "remarks",
        "region",
        "customer",
        "location",
        "department",
        "requester",
        "tech",
        "complete_date",
        "status",
        "counter",
        "counter_file",
    ];

    public function printer_detail()
    {
        return $this->belongsTo(Printer::class, 'printer', 'id');
    }

    public function tech_detail()
    {
        return $this->belongsTo(User::class, 'tech', 'id');
    }

    public function requester_detail()
    {
        return $this->belongsTo(User::class, 'requester', 'id');
    }
}
