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
            6 => 'neutral', // Shorts
            7 => 'female',  // Skirt
            8 => 'neutral', // Coat
            9 => 'neutral', // Hoodie
            10 => 'neutral', // Tank Top
            11 => 'neutral', // Shirt
            12 => 'neutral', // Leggings
        ];

        // Clothing type to temperature range mapping
        $temperatureRangeMapping = [
            1 => [3, 4], // T-Shirt: suitable for 11°C - 30°C
            2 => [2, 3], // Jeans: suitable for 0°C - 20°C
            3 => [1, 2], // Jacket: suitable for Below 0°C to 10°C
            4 => [3, 4], // Dress: suitable for 11°C - 30°C
            5 => [1, 2, 3], // Sweater: suitable for Below 0°C to 20°C
            6 => [3, 4], // Shorts: suitable for 11°C - 30°C
            7 => [3, 4], // Skirt: suitable for 11°C - 30°C
            8 => [1, 2], // Coat: suitable for Below 0°C to 10°C
            9 => [1, 2, 3], // Hoodie: suitable for Below 0°C to 20°C
            10 => [3, 4], // Tank Top: suitable for 11°C - 30°C
            11 => [2, 3], // Shirt: suitable for 0°C - 20°C
            12 => [2, 3], // Leggings: suitable for 0°C - 20°C
        ];

        // Clothing type to material mapping
        $materialMapping = [
            1 => [1, 3], // T-Shirt: Cotton, Denim
            2 => [3],    // Jeans: Denim
            3 => [2, 3], // Jacket: Wool, Denim
            4 => [1, 2], // Dress: Cotton, Wool
            5 => [1, 2], // Sweater: Cotton, Wool
            6 => [1],    // Shorts: Cotton
            7 => [1],    // Skirt: Cotton
            8 => [2],    // Coat: Wool
            9 => [1],    // Hoodie: Cotton
            10 => [1],   // Tank Top: Cotton
            11 => [1],   // Shirt: Cotton
            12 => [1],   // Leggings: Cotton
        ];

        // Clothing type to style mapping
        $styleMapping = [
            1 => [1, 2], // T-Shirt: Casual, Sport
            2 => [1, 2], // Jeans: Casual, Sport
            3 => [3, 4], // Jacket: Formal, Business
            4 => [3, 5], // Dress: Formal, Vintage
            5 => [1],    // Sweater: Casual
            6 => [1],    // Shorts: Casual
            7 => [3],    // Skirt: Formal
            8 => [3],    // Coat: Formal
            9 => [2],    // Hoodie: Sport
            10 => [1, 2], // Tank Top: Casual, Sport
            11 => [3, 4], // Shirt: Formal, Business
            12 => [1, 2], // Leggings: Casual, Sport
        ];

        // Clothing type to water resistance mapping
        $waterResistantMapping = [
            1 => false, // T-Shirt: not water-resistant
            2 => false, // Jeans: not water-resistant
            3 => true,  // Jacket: water-resistant
            4 => false, // Dress: not water-resistant
            5 => false, // Sweater: not water-resistant
            6 => false, // Shorts: not water-resistant
            7 => false, // Skirt: not water-resistant
            8 => true,  // Coat: water-resistant
            9 => false, // Hoodie: not water-resistant
            10 => false, // Tank Top: not water-resistant
            11 => false, // Shirt: not water-resistant
            12 => false, // Leggings: not water-resistant
        ];

        // Base and unusual colors mapping
        $colorMapping = [
            'base' => ['black', 'white', 'gray'],
            'unusual' => [
                'maroon', 'blue', 'green', 'yellow', 'purple', 'pink', 'orange',
                'turquoise', 'burgundy', 'lime', 'teal', 'navy', 'lavender'
            ]
        ];

        // Generate unique combinations
        foreach (range(1, 12) as $type_id) {
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
                        $colors = array_merge(
                            $colorMapping['base'],
                            [ $colorMapping['unusual'][array_rand($colorMapping['unusual'])] ] // Take 1 random unusual color for variety
                        );

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
