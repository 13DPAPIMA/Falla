<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'email' => 'stylist@example.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'role' => 'stylist',
                'gender' => 'Male',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'user@example.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'role' => 'user',
                'gender' => 'Female',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
