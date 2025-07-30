<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'CEO',
            'email' => 'ceo@askseo.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'type' => 'super_admin',
            'status' => 'active',
            'status_mode' => 'offline'
        ]);
        User::factory()->create([
            'name' => 'Developer',
            'email' => 'developer@askseo.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'type' => 'super_admin',
            'status' => 'active',
            'status_mode' => 'offline'
        ]);

        Group::create(['name' => 'ASK SEO TEAM']);
        GroupMember::create(['group_id' => '1', 'user_id' => '1']);
    }
}
