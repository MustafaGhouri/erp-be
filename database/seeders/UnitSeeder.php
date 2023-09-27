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
                "name" => "Pieces",
                "user_id" => 1
            ]);
            Unit::create([
                "name" => "Kilo",
                "user_id" => 1
            ]);
            Unit::create([
                "name" => "Grams",
                "user_id" => 1
            ]);
            Unit::create([
                "name" => "Dozen",
                "user_id" => 1
            ]);
        }
    }
}
