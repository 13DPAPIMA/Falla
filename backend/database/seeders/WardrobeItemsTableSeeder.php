<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WardrobeItemsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wardrobe_items')->insert([
            [
                'wardrobe_id' => 1,
                'clothing_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'wardrobe_id' => 1,
                'clothing_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Add more wardrobe items as needed
        ]);
    }
}
