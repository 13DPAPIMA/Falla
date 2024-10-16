<?php

namespace Database\Seeders;
use Faker\Factory as Faker;

use Illuminate\Database\Seeder;

class ClothingTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create(); // Create Faker instance

        $genderMapping = [
            1 => 'neutral', // T-Shirt
            2 => 'neutral', // Jeans
            3 => 'male',    // Jacket
            4 => 'female',  // Dress
            5 => 'neutral', // Sweater
        ];

        // Generate all possible unique combinations
        foreach (range(1, 5) as $type_id) {
            $gender = $genderMapping[$type_id];
            foreach (range(1, 5) as $temperature_range_id) {
                foreach (range(1, 3) as $style_id) {
                    foreach (range(1, 3) as $material_id) {
                        \App\Models\Clothing::create([
                            'style_id' => $style_id,
                            'type_id' => $type_id,
                            'temperature_range_id' => $faker->numberBetween(1, 5),
                            'material_id' => $material_id,
                            'gender' => $gender,
                            'color' => $faker->safeColorName(),
                            'water_resistant' => $faker->boolean(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
