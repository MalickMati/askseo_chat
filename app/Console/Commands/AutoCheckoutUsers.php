<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use Carbon\Carbon;

class AutoCheckoutUsers extends Command
{
    protected $signature = 'users:auto-checkout';
    protected $description = 'Automatically check out all users at 8 PM and mark as system checkout';

    public function handle()
    {
        $today = Carbon::today();

        $attendances = Attendance::whereDate('date', $today)
            ->whereNull('check_out')
            ->whereNotNull('check_in')
            ->get();

        foreach ($attendances as $attendance) {
            $attendance->check_out = '19:00:00';
            $attendance->hours_worked = round(Carbon::parse($attendance->check_in)->diffInMinutes(Carbon::createFromTime(19, 0)) / 60, 2);
            $attendance->checkout_method = 'System Checkout';
            $attendance->save();
        }

        $users = \App\Models\User::all();

        foreach ($users as $user) {
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first(); // Check for any attendance record, not just 'active'

            if (!$attendance) {
                // No record exists — safe to create new one
                Attendance::create([
                    'user_id' => $user->id,
                    'date' => $today,
                    'check_in' => '00:00:00',
                    'check_out' => '00:00:00',
                    'status' => 'Absent',
                    'hours_worked' => 0,
                    'checkout_method' => 'System Marked Absent',
                    'notes' => 'System Marked Absent',
                ]);
            } elseif (!$attendance->check_in && !$attendance->check_out) {
                // Record exists but is incomplete — mark it properly
                $attendance->update([
                    'status' => 'Absent',
                    'hours_worked' => 0,
                    'checkout_method' => 'System Marked Absent',
                ]);
            }
        }

        $this->info("Auto checkout completed for " . $attendances->count() . " users.");
    }
}
