<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
 
class UserSeedeer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::truncate();
        if (count(User::all()) == 0) {
            User::create([
                "first_name" => "Mustafa",
                "last_name" => "Ghouri",
                "email" => "admin@gmail.com",
                "password" => Hash::make("admin123"),
                "role_id" => 1,
                "status" => "1", 
            ]);
            User::create([
                "first_name" => "Shahrukh",
                "last_name" => "Khan",
                "email" => "tech@gmail.com",
                "password" => Hash::make("tech123"),
                "role_id" => 2,
                "status" => "1", 
            ]);
            User::create([
                "first_name" => "Ibrahim",
                "last_name" => "Nawab",
                "email" => "supervisor@gmail.com",
                "password" => Hash::make("supervisor123"),
                "role_id" => 3,
                "status" => "1", 
            ]);
            User::create([
                "first_name" => "Usman",
                "last_name" => "Amjad",
                "email" => "requester@gmail.com",
                "password" => Hash::make("requester123"),
                "role_id" => 4,
                "status" => "1", 
            ]);
            User::create([
                "first_name" => "Rashid",
                "last_name" => "Rashid",
                "email" => "inventory@gmail.com",
                "password" => Hash::make("inventory123"),
                "role_id" => 5,
                "status" => "1", 
            ]);
        }
    }
}
