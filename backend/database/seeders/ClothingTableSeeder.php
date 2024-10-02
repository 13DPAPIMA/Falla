<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClothingTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('clothing')->insert([
            [
                'style_id' => 1, // Casual
                'photo_id' => 3, // T-Shirt photo
                'type_id' => 1,  // T-Shirt
                'temperature_range_id' => 3, // 11°C - 20°C
                'material_id' => 1, // Cotton
                'gender' => 'neutral',
                'color' => 'White',
                'water_resistant' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'style_id' => 2, // Sport
                'photo_id' => 2, // Jeans photo
                'type_id' => 2,  // Jeans
                'temperature_range_id' => 2, // 0°C - 10°C
                'material_id' => 5, // Denim
                'gender' => 'neutral',
                'color' => 'Blue',
                'water_resistant' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'style_id' => 3, // Formal
                'photo_id' => 1, // Jacket photo
                'type_id' => 3,  // Jacket
                'temperature_range_id' => 2, // 0°C - 10°C
                'material_id' => 4, // Leather
                'gender' => 'male',
                'color' => 'Black',
                'water_resistant' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Add more clothing items as needed
        ]);
    }
}
