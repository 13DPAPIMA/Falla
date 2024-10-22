<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TemperatureRangesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('temperature_ranges')->insert([
            ['temperature_range' => 'Above 30°C', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['temperature_range' => '21°C - 30°C', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['temperature_range' => '11°C - 20°C', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['temperature_range' => '0°C - 10°C', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['temperature_range' => 'Below 0°C', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
