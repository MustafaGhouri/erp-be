<?php
namespace Database\Seeders;
use App\Models\Roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class RoleSeedeer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (count(Roles::all()) == 0) {
            Roles::create([
                "name" => "admin"
            ]);
            Roles::create([
                "name" => "technician"
            ]);
            Roles::create([
                "name" => "requester"
            ]);
            Roles::create([
                "name" => "supervisor"
            ]);
            Roles::create([
                "name" => "inventory-manager"
            ]);
        }
    }
}
