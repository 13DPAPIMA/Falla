<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use App\Models\ClothingPhoto;

class ClothingTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create(); // Create Faker instance

        // Clothing type to gender mapping
        $genderMapping = [
            1 => 'neutral', // T-Shirt
            2 => 'neutral', // Jeans
            3 => 'neutral', // Jacket
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
            1 => [3, 4],       // T-Shirt: 11°C - 30°C
            2 => [2, 3],       // Jeans: 0°C - 20°C
            3 => [1, 2, 3],    // Jacket: Below 0°C to 20°C
            4 => [3, 4],       // Dress: 11°C - 30°C
            5 => [1, 2, 3],    // Sweater: Below 0°C to 20°C
            6 => [3, 4],       // Shorts: 11°C - 30°C
            7 => [3, 4],       // Skirt: 11°C - 30°C
            8 => [1, 2],       // Coat: Below 0°C to 10°C
            9 => [2, 3],       // Hoodie: 0°C - 20°C
            10 => [3, 4],      // Tank Top: 11°C - 30°C
            11 => [2, 3],      // Shirt: 0°C - 20°C
            12 => [2, 3],      // Leggings: 0°C - 20°C
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


        $layerMapping = [
            1 => 'base',   // T-Shirt
            2 => 'pants',  // Jeans (изменено на 'pants')
            3 => 'outer',  // Jacket
            4 => 'base',   // Dress
            5 => 'mid',    // Sweater
            6 => 'pants',  // Shorts (изменено на 'pants')
            7 => 'pants',  // Skirt (изменено на 'pants')
            8 => 'outer',  // Coat
            9 => 'mid',    // Hoodie
            10 => 'base',  // Tank Top
            11 => 'base',  // Shirt
            12 => 'pants', // Leggings (изменено на 'pants')
        ];


        // Clothing type to water resistance mapping
        $waterResistantMapping = [
            1 => false, // T-Shirt
            2 => false, // Jeans
            3 => true,  // Jacket
            4 => false, // Dress
            5 => false, // Sweater
            6 => false, // Shorts
            7 => false, // Skirt
            8 => true,  // Coat
            9 => true,  // Hoodie (установлено в true)
            10 => false, // Tank Top
            11 => false, // Shirt
            12 => false, // Leggings
        ];

        // Clothing type to color mapping
        $colorMappingByType = [
            1 => ['black', 'white', 'gray'], // T-Shirt
            2 => ['blue', 'black', 'gray'], // Jeans
            3 => ['navy', 'black', 'gray'], // Jacket
            4 => ['red', 'pink', 'green'], // Dress
            5 => ['beige', 'gray', 'blue', 'maroon'], // Sweater
            6 => ['black', 'blue', 'gray'], // Shorts
            7 => ['black', 'gray', 'red'], // Skirt
            8 => ['black', 'brown', 'gray'], // Coat
            9 => ['blue', 'green', 'gray'], // Hoodie
            10 => ['white', 'black'], // Tank Top
            11 => ['white', 'blue', 'green'], // Shirt
            12 => ['black', 'gray', 'blue'], // Leggings
        ];

        $photoMapping = [
            1 => 1,   // T-Shirt
            2 => 2,   // Jeans
            3 => 1,   // Shirt
            4 => null, // Dress
            5 => 3,   // Sweater
            6 => 2,   // Shorts
            7 => null, // Skirt
            8 => null, // Coat
            9 => 3,   // Hoodie
            10 => null, // Tank Top
            11 => 1,  // Shirt
            12 => 2,  // Leggings
        ];

        // Generate unique combinations
        foreach (range(1, 12) as $type_id) {
            $gender = $genderMapping[$type_id];
            $layer = $layerMapping[$type_id];
            // Получаем допустимые стили для текущего типа одежды
            $styles = $styleMapping[$type_id] ?? NULL;

            foreach ($styles as $style_id) {
                // Получаем допустимые температурные диапазоны, материалы, водонепроницаемость и цвета для текущего типа одежды
                $temperatureRanges = $temperatureRangeMapping[$type_id];
                $materials = $materialMapping[$type_id];
                $waterResistant = $waterResistantMapping[$type_id];
                $colors = $colorMappingByType[$type_id];

                $photo = $photoMapping[$type_id];
                
                foreach ($temperatureRanges as $temperature_range_id) {
                    foreach ($materials as $material_id) {
                        foreach ($colors as $color) {
                            \App\Models\Clothing::create([
                                'style_id' => $style_id,
                                'type_id' => $type_id,
                                'temperature_range_id' => $temperature_range_id,
                                'material_id' => $material_id,
                                'gender' => $gender,
                                'color' => $color,
                                'photo_id' => $photo, 
                                'layer' => $layer,
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
