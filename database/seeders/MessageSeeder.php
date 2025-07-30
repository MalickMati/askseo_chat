<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id')->toArray();

        if (count($userIds) < 2) {
            $this->command->warn("Not enough users to seed messages. At least 2 users are needed.");
            return;
        }

        $messages = [];

        for ($i = 0; $i < 50; $i++) {
            $sender = $userIds[array_rand($userIds)];
            do {
                $receiver = $userIds[array_rand($userIds)];
            } while ($receiver === $sender); // Ensure sender ≠ receiver

            $isFile = rand(0, 1); // 50% chance message is a file
            $fileOptions = [
                'uploads/chat/voice123.mp3',
                'uploads/chat/image456.png',
                'uploads/chat/document789.pdf',
                'uploads/chat/video999.mp4'
            ];

            $messages[] = [
                'sender_id'   => $sender,
                'receiver_id' => $receiver,
                'message'     => $isFile ? null : Str::random(rand(20, 100)),
                'file_path'   => $isFile ? $fileOptions[array_rand($fileOptions)] : null,
                'read_at'     => rand(0, 1) ? Carbon::now()->subMinutes(rand(1, 100)) : null,
                'sent_at'     => Carbon::now()->subMinutes(rand(1, 500)),
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        DB::table('messages')->insert($messages);
    }
}
