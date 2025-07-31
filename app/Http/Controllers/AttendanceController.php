<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function getAttendance(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $parsedDate = Carbon::parse($date);
        
        // Get all users with their attendance for the selected date
        $users = User::with(['attendance' => function($query) use ($parsedDate) {
            $query->whereDate('date', $parsedDate);
        }])->get();
        
        // Format the data for the frontend
        $formattedUsers = $users->map(function($user) {
            $attendance = $user->attendance->first();
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->image,
                'department' => $user->department ?? 'General',
                'status' => $attendance->status ?? 'absent',
                'check_in' => $attendance->check_in ?? null,
                'check_out' => $attendance->check_out ?? null,
                'hours_worked' => $attendance->hours_worked ?? null,
                'notes' => $attendance->notes ?? null
            ];
        });
        
        // Calculate summary
        $summary = [
            'total' => $users->count(),
            'present' => $users->filter(fn($user) => ($user->attendance->first()->status ?? '') === 'present')->count(),
            'absent' => $users->filter(fn($user) => ($user->attendance->first()->status ?? '') === 'absent')->count(),
            'late' => $users->filter(fn($user) => ($user->attendance->first()->status ?? '') === 'late')->count(),
            'leave' => $users->filter(fn($user) => ($user->attendance->first()->status ?? '') === 'leave')->count(),
        ];
        
        return response()->json([
            'users' => $formattedUsers,
            'summary' => $summary
        ]);
    }
    
    public function getUserAttendance(User $user, Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();
            
        return response()->json($attendance ?? [
            'status' => 'absent',
            'check_in' => null,
            'check_out' => null,
            'notes' => null
        ]);
    }
    
    public function updateAttendance(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,leave',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'notes' => 'nullable|string|max:500'
        ]);
        
        // Calculate hours worked if both check-in and check-out are provided
        $hoursWorked = null;
        if ($validated['check_in'] && $validated['check_out']) {
            $start = Carbon::parse($validated['check_in']);
            $end = Carbon::parse($validated['check_out']);
            $hoursWorked = $end->diffInHours($start);
        }
        
        // Update or create attendance record
        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'date' => $validated['date']
            ],
            [
                'status' => $validated['status'],
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'hours_worked' => $hoursWorked,
                'notes' => $validated['notes']
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully'
        ]);
    }
    
    public function markAllPresent(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        
        // Get all user IDs
        $userIds = User::pluck('id');
        
        // Mark all as present
        foreach ($userIds as $userId) {
            Attendance::updateOrCreate(
                [
                    'user_id' => $userId,
                    'date' => $date
                ],
                [
                    'status' => 'present'
                ]
            );
        }
        
        return response()->json([
            'success' => true,
            'message' => 'All users marked as present'
        ]);
    }
}