<?php

namespace Database\Seeders;
use Faker\Factory as Faker;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClothingTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create(); // Create Faker instance

        $combinations = [];

        // Generate all possible unique combinations
        foreach (range(1, 5) as $style_id) {
            foreach (range(1, 5) as $type_id) {
                foreach (range(1, 5) as $temperature_range_id) {
                    foreach (range(1, 5) as $material_id) {
                        $combinations[] = [
                            'style_id' => $style_id,
                            'type_id' => $type_id,
                            'temperature_range_id' => $temperature_range_id,
                            'material_id' => $material_id,
                            'gender' => $faker->randomElement(['male', 'female', 'neutral']), // Use local $faker
                            'color' => $faker->safeColorName(), // Use local $faker
                            'water_resistant' => $faker->boolean(), // Use local $faker
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        // Insert combinations into the database
        foreach ($combinations as $combination) {
            \App\Models\Clothing::create($combination);
        }
    }
}
