<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (count(Region::all()) == 0) {
            Region::create([
                "name" => "Central",
                "location" => 0,
                "user_id" => 1
            ]);
            Region::create([
                "name" => "North",
                "location" => 0,
                "user_id" => 1
            ]);
            Region::create([
                "name" => "South",
                "location" => 0,
                "user_id" => 1
            ]);
        }
    }
}
