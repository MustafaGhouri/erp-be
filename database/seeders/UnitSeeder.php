<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (count(Unit::all()) == 0) {
            Unit::create([
                "name" => "Pieces"
            ]);
            Unit::create([
                "name" => "Kilo"
            ]);
            Unit::create([
                "name" => "Grams"
            ]);
            Unit::create([
                "name" => "Dozen"
            ]);
        }
    }
}
