<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClothingPhotosTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('clothing_photos')->insert([
            ['cloudinary_public_id' => 'pr8apvzfrn8a23xaecns','photo_url' => 'https://res.cloudinary.com/dy55d3tu4/image/upload/v1730098168/pr8apvzfrn8a23xaecns.webp', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['cloudinary_public_id' => 'fq9huralj30wbmltecpm','photo_url' => 'https://res.cloudinary.com/dy55d3tu4/image/upload/v1730098160/fq9huralj30wbmltecpm.webp', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['cloudinary_public_id' => 'gdmmsd7rznwt6tvlsm8l','photo_url' => 'https://res.cloudinary.com/dy55d3tu4/image/upload/v1730101689/gdmmsd7rznwt6tvlsm8l.webp', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Add more photos as needed
        ]);
    }
}
