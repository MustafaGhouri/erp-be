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
                "name" => "all"
            ]);
            ProductCategory::create([
                "name" => "React"
            ]);
            ProductCategory::create([
                "name" => "Angular"
            ]);
            ProductCategory::create([
                "name" => "Vue"
            ]);
            ProductCategory::create([
                "name" => "Svelte"
            ]);
        }
    }
}
