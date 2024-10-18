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

        // Clothing type to style mapping
        $styleMapping = [
            1 => [1, 2], // T-Shirt: Casual, Sport
            2 => [1, 2], // Jeans: Casual, Sport
            3 => [3, 4], // Jacket: Formal, Business
            4 => [3, 5], // Dress: Formal, Vintage
            5 => [1],    // Sweater: Casual
        ];

        // Clothing type to water resistance mapping
        $waterResistantMapping = [
            1 => false, // T-Shirt: not water-resistant
            2 => false, // Jeans: not water-resistant
            3 => true,  // Jacket: water-resistant
            4 => false, // Dress: not water-resistant
            5 => false, // Sweater: not water-resistant
        ];

        // Base and unusual colors mapping
        $colorMapping = [
            'base' => ['black', 'white', 'gray'],
            'unusual' => [
                'red', 'blue', 'green', 'yellow', 'purple', 'pink', 'orange',
                'turquoise', 'burgundy', 'lime', 'teal', 'navy', 'lavender'
            ]
        ];

        // Generate unique combinations
        foreach (range(1, 5) as $type_id) {
            $gender = $genderMapping[$type_id];

            // Get valid styles for the current clothing type
            $styles = $styleMapping[$type_id];

            foreach ($styles as $style_id) {
                // Get valid temperature ranges and materials for the current clothing type
                $temperatureRanges = $temperatureRangeMapping[$type_id];
                $materials = $materialMapping[$type_id];
                $waterResistant = $waterResistantMapping[$type_id];

                foreach ($temperatureRanges as $temperature_range_id) {
                    foreach ($materials as $material_id) {
                        // Combine base and unusual colors
                        $colors = array_merge($colorMapping['base'], array_slice($colorMapping['unusual'], 0, 3)); // Take 3 unusual colors for variety

                        foreach ($colors as $color) {
                            \App\Models\Clothing::create([
                                'style_id' => $style_id,
                                'type_id' => $type_id,
                                'temperature_range_id' => $temperature_range_id,
                                'material_id' => $material_id,
                                'gender' => $gender,
                                'color' => $color,
                                'water_resistant' => $waterResistant,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }
}
