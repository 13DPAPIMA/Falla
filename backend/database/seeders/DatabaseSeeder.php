<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            TemperatureRangesTableSeeder::class,
            ClothingTypesTableSeeder::class,
            ClothingMaterialsTableSeeder::class,
            StylesTableSeeder::class,
            ClothingPhotosTableSeeder::class,
            ClothingTableSeeder::class,
            WardrobesTableSeeder::class,
            WardrobeItemsTableSeeder::class,
        ]);
    }
}
