<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WardrobesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wardrobes')->insert([
            [
                'user_id' => 1, // User
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Add more wardrobes if needed
        ]);
    }
}
