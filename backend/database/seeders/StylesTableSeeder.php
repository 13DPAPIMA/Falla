<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StylesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('styles')->insert([
            ['style' => 'Casual', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['style' => 'Sport', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['style' => 'Formal', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['style' => 'Business', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['style' => 'Vintage', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Add more styles as needed
        ]);
    }
}
