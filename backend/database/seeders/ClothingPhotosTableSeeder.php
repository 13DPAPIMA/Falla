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
            ['cloudinary_public_id' => 'dfgoihjsdfoijsfoij','photo_url' => 'photos/tshirt.jpg', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['cloudinary_public_id' => 'dfgoihjsdfoijsfoij','photo_url' => 'photos/jeans.jpg', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['cloudinary_public_id' => 'dfgoihjsdfoijsfoij','photo_url' => 'photos/jacket.jpg', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['cloudinary_public_id' => 'dfgoihjsdfoijsfoij','photo_url' => 'photos/dress.jpg', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['cloudinary_public_id' => 'dfgoihjsdfoijsfoij','photo_url' => 'photos/sweater.jpg', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Add more photos as needed
        ]);
    }
}
