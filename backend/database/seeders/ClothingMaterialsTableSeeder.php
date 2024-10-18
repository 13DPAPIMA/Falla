<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClothingMaterialsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('clothing_materials')->insert([
            ['material' => 'Cotton', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['material' => 'Wool', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['material' => 'Denim', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Add more materials as needed
        ]);
    }
}
