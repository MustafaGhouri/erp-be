<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (count(ProductCategory::all()) == 0) {
            ProductCategory::create([
                "name" => "all",
                "user_id" => 1
            ]);
            ProductCategory::create([
                "name" => "React",
                "user_id" => 1
            ]);
            ProductCategory::create([
                "name" => "Angular",
                "user_id" => 1
            ]);
            ProductCategory::create([
                "name" => "Vue",
                "user_id" => 1
            ]);
            ProductCategory::create([
                "name" => "Svelte",
                "user_id" => 1
            ]);
        }
    }
}
