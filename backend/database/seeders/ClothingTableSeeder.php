<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ClothingTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create(); // Create Faker instance

        // Clothing type to gender mapping
        $genderMapping = [
            1 => 'neutral', // T-Shirt
            2 => 'neutral', // Jeans
            3 => 'male',    // Jacket
            4 => 'female',  // Dress
            5 => 'neutral', // Sweater
        ];

        // Clothing type to temperature range mapping
        $temperatureRangeMapping = [
            1 => [3, 4], // T-Shirt: suitable for 11°C - 30°C
            2 => [2, 3], // Jeans: suitable for 0°C - 20°C
            3 => [1, 2], // Jacket: suitable for Below 0°C to 10°C
            4 => [3, 4], // Dress: suitable for 11°C - 30°C
            5 => [1, 2, 3], // Sweater: suitable for Below 0°C to 20°C
        ];

        // Clothing type to material mapping
        $materialMapping = [
            1 => [1, 3], // T-Shirt: Cotton, Denim
            2 => [3],    // Jeans: Denim
            3 => [2, 3], // Jacket: Wool, Denim
            4 => [1, 2], // Dress: Cotton, Wool
            5 => [1, 2], // Sweater: Cotton, Wool
        ];

        // Generate unique combinations
        foreach (range(1, 5) as $type_id) {
            $gender = $genderMapping[$type_id];

            foreach (range(1, 3) as $style_id) {
                // Get valid temperature ranges and materials for the current clothing type
                $temperatureRanges = $temperatureRangeMapping[$type_id];
                $materials = $materialMapping[$type_id];

                foreach ($temperatureRanges as $temperature_range_id) {
                    foreach ($materials as $material_id) {
                        \App\Models\Clothing::create([
                            'style_id' => $style_id,
                            'type_id' => $type_id,
                            'temperature_range_id' => $temperature_range_id, // Use mapped range
                            'material_id' => $material_id, // Use mapped material
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
