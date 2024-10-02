<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClothingTypesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('clothing_types')->insert([
            ['type' => 'T-Shirt', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type' => 'Jeans', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type' => 'Jacket', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type' => 'Dress', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type' => 'Sweater', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Add more clothing types as needed
        ]);
    }
}