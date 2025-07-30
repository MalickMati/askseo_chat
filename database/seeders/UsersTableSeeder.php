<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $statuses = ['pending', 'verified', 'active', 'inactive'];
        $types = ['super_admin', 'admin', 'moderator', 'general_user'];

        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password123'), // default password
                'status' => $faker->randomElement($statuses),
                'image' => 'https://randomuser.me/api/portraits/' . ($faker->randomElement(['men', 'women'])) . '/' . $faker->numberBetween(10, 90) . '.jpg',
                'email_verified_at' => now(),
                'type' => $faker->randomElement($types),
                'remember_token' => Str::random(10),
                'otp' => rand(100000, 999999),
                'otp_expires_at' => Carbon::now()->addMinutes(15),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}