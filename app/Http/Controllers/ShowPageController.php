<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use App\Models\GroupMember;
use App\Models\Attandance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ShowPageController extends Controller
{
    protected $officeips = ['154.192.222.75', '58.65.223.213'];

    public function showchatpage(Request $request)
    {
        if (!session("user_type")) {
            return redirect("/login");
        }

        $varify_user = User::where("email", session("user_email"))->first();

        if (!$varify_user || $varify_user->status !== 'active') {
            return redirect("/login");
        }

        $users = User::where('email', '!=', session('user_email'))->get();

        $userList = $users->map(function ($user) use ($varify_user) {
            $lastMessage = Message::where(function ($query) use ($user, $varify_user) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $varify_user->id);
            })
                ->orWhere(function ($query) use ($user, $varify_user) {
                    $query->where('sender_id', $varify_user->id)
                        ->where('receiver_id', $user->id);
                })
                ->orderByDesc('sent_at')
                ->first();

            $formattedTime = null;
            $messageText = null;

            if ($lastMessage) {
                $sentAt = Carbon::parse($lastMessage->sent_at);
                $now = Carbon::now();

                if ($sentAt->isToday()) {
                    $formattedTime = $sentAt->format('h:i A');
                } elseif ($sentAt->isYesterday()) {
                    $formattedTime = 'Yesterday';
                } elseif ($sentAt->diffInDays($now) < 7) {
                    $formattedTime = $sentAt->format('l'); // e.g. Monday, Tuesday
                } else {
                    $formattedTime = $sentAt->format('M d'); // e.g. Jul 21
                }

                $messageText = $lastMessage->message ?? '[File]';
            }

            return [
                'id' => $user->id,
                'username' => $user->name,
                'img' => $user->image ?? asset('assets/images/default.png'),
                'status' => $user->status_mode ?? 'offline',
                'last_message' => $messageText ?? 'Type a message to get started',
                'last_time' => $formattedTime,
            ];
        });

        $allgroups = \App\Models\Group::all();

        return view("chat.index", [
            'allgroups' => $allgroups,
            'allusers' => $userList,
            'currentUser' => [
                'id' => $varify_user->id,
                'username' => $varify_user->name,
                'img' => $varify_user->image ?? asset('assets/images/default.png'),
                'status' => $varify_user->status_mode ?? 'offline',
            ],
        ]);
    }

    public function showadminuserpage(Request $request)
    {
        if (!session()->has('super_admin_loged')) {
            return redirect('/login');
        }

        $user = User::where('email', session('user_email'))->first();

        return view('chat.user_management', [
            'activePage' => 'user_management',
            'name' => $user->name,
            'email' => $user->email,
            'img' => $user->image ?? 'assets/images/default.png'
        ]);
    }

    public function fetchUsers()
    {
        return response()->json(User::orderBy('name', 'asc')->get());
    }

    public function update_user_status(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:users,id',
            'status' => 'required|string|in:active,inactive',
        ]);

        $user = User::find($validated['id']);

        if (!session()->has('super_admin_loged')) {
            return response()->json(['success' => false, 'message' => 'Super Admin not detected!']);
        }

        $user->status = $validated['status'];
        $user->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function deleteuser(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        $name = $user->name;

        $user->delete();

        return response()->json(['success' => true, 'message' => $name . ' was deleted successfully!']);
    }

    public function updateuser(Request $request)
    {
        $user = User::findOrFail($request->id);

        // Validate input
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'nullable|in:admin,moderator,general_user',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update fields
        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = 'active';

        // Only update password if filled
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Only update role if provided
        if (!is_null($request->role)) {
            $user->type = $request->role;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            if ($user->image && file_exists(public_path($user->image))) {
                unlink(public_path($user->image));
            }
            $image->move(public_path('assets/users'), $imageName);
            $user->image = 'assets/users/' . $imageName;
        }
        $user->save();

        return response()->json(['message' => 'User updated successfully']);
    }

    public function showsettings()
    {
        if (!session()->has('user_email')) {
            return redirect('auth/login');
        }

        $email = session('user_email');

        $user = User::where('email', '=', $email)->first();

        if (!$user) {
            return redirect('auth/login');
        }

        return view('chat.settings', [
            'name' => $user->name,
            'email' => $user->email,
            'img' => $user->image ?? 'assets/images/default.png',
            'status' => $user->status_mode
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|string|min:8',
            'new_password' => 'nullable|string|min:8',
            'status' => 'nullable|in:online,offline,away,do_not_disturb,be_right_back',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        // Update basic fields
        $user->name = $request->name;
        $user->email = $request->email;

        // Handle password change
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Current password is incorrect.'], 403);
            }
            $user->password = Hash::make($request->new_password);
        }

        // Update status if provided
        if ($request->filled('status')) {
            $user->status_mode = $request->status;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->move(public_path('uploads/users'), $filename);

            if ($user->image && file_exists(public_path($user->image))) {
                unlink(public_path($user->image));
            }

            $user->image = 'uploads/users/' . $filename;
        }

        $user->save();

        return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
    }

    public function showaddgroup()
    {
        $user = User::where('email', '=', session('user_email'))->first();
        if (!$user) {
            return redirect('/login')->with('error', 'Session Expired. Login again');
        } elseif (
            $user->type !== 'super_admin'
            && $user->type !== 'admin'
            // && $user->type !== 'moderator'
        ) {
            return redirect('/chat')->with('error', 'Admin not found!');
        }
        $users = User::where('email', '!=', session('user_email'))->get()->map(function ($user, $index) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->image ? asset($user->image) : asset('assets/images/default.png'),
                'status' => $user->status_mode,
            ];
        });

        return view('chat.add_group', ['users' => $users]);
    }

    public function addnewgroup(Request $request)
    {
        $user = User::where('email', '=', session('user_email'))->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid Session']);
        }

        $request->validate([
            'groupname' => 'required|string|max:255',
            'members' => 'required|array|min:1',
            'members.*' => 'exists:users,id'
        ]);
        try {
            // Create new group
            $group = Group::create([
                'name' => $request->groupname,
            ]);

            // Add creator to the group
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $user->id,
            ]);

            // Add other members
            foreach ($request->members as $memberId) {
                if ($memberId != $user->id) {
                    GroupMember::create([
                        'group_id' => $group->id,
                        'user_id' => $memberId,
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Group created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function showattendanceuser()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Session not found! Login again');
        }

        $checkindetails = Attendance::where('user_id', '=', Auth::id())->latest()->first();

        $checkin_time = optional($checkindetails?->check_in)->format('H:i');
        $checkin_date = optional($checkindetails?->date)->format('Y-m-d');
        $checkout_time = optional($checkindetails?->check_out)->format('H:i');
        $worked_today = optional($checkindetails)->hours_worked;

        if ($checkout_time) {
            $checkout_date = $checkin_date;
        } else {
            $checkout_date = null;
        }
        $today = Carbon::today();

        $hasAttendance = Attendance::where('user_id', Auth::id())
            ->where('date', '=', $today)
            ->exists();
        
        $user_id = auth()->user()->id;

        $records = Attendance::select([
            DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as formatted_date"),
            'status',
            'check_in',
            'check_out',
            'hours_worked',
        ])
            ->where('user_id', $user_id)
            ->whereMonth('date', Carbon::now()->month)
            ->orderBy('date', 'asc')
            ->get();

        return view('chat.user_checkin_settings', [
            'username' => Auth::user()->name,
            'checkin_time' => $checkin_time,
            'checkin_date' => $checkin_date,
            'checkout_time' => $checkout_time,
            'worked_today' => $worked_today,
            'checkout_date' => $checkout_date,
            'has_attendance_today' => $hasAttendance,
            'table_records' => $records,
        ]);
    }

    public function usercheckin(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Sesison Error! Login Again',
                'redirect' => '/login',
            ]);
        }
        $request->validate([
            'note' => 'nullable|string|max:255',
        ]);

        $today = Carbon::today();

        $hasAttendance = Attendance::where('user_id', Auth::id())
            ->where('date', '=', $today)
            ->exists();

        if ($hasAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'User already checked in!',
            ]);
        }

        $checkin_time = Carbon::now();
        $eightff = Carbon::createFromTime(8, 50, 0);
        $nine_am = Carbon::createFromTime(9, 20, 0);
        $offtime = Carbon::createFromTime(18, 0, 0);

        $status = null;

        if ($checkin_time->greaterThan($offtime)) {
            return response()->json([
                'success' => false,
                'message' => 'You are already marked absent today!',
            ]);
        } elseif ($checkin_time->lessThan($eightff)) {
            return response()->json([
                'success' => false,
                'message' => 'This is earlier than office time!',
            ]);
        } elseif ($checkin_time->greaterThan($nine_am)) {
            $status = 'Late';
        } elseif ($checkin_time->greaterThanOrEqualTo($eightff)) {
            $status = 'Present';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You can not checkin at this time!',
            ]);
        }

        $note = $request->note;

        $userIp = $request->ip();
        if (!in_array($userIp, $this->officeips)) {
            $note = 'User is verified using ip address of office.';
        }

        Attendance::create([
            'user_id' => Auth::id(),
            'date' => Carbon::now(),
            'check_in' => Carbon::now()->format('H:i:s'),
            'status' => $status,
            'notes' => $note,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'You have successfully checked in!',
        ]);
    }

    public function usercheckout(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Session Expired! Login again...',
                'redirect' => '/logout'
            ]);
        }

        $request->validate([
            'out_method' => 'nullable|string|max:255',
        ]);

        $checkin_data = Attendance::where('user_id', Auth::id())->where('date', '=', Carbon::today())->first();

        if (!$checkin_data) {
            return response()->json([
                'success' => false,
                'message' => 'No checkin data found! Login again...',
                'redirect' => '/logout'
            ]);
        }

        $checkIn_time = Carbon::parse($checkin_data->check_in);
        $checkout_time = Carbon::now();

        $workedHours = $checkIn_time->diffInHours($checkout_time);

        if ($workedHours < 8) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to checkout before 8 hours!'
            ]);
        }
        $offtime = Carbon::createFromTime(6, 0, 0);
        if ($checkout_time->lessThan($offtime)) {
            return response()->json([
                'success' => false,
                'message' => 'Office off time is 6 pm. You can not checkout before that!'
            ]);
        }

        $checkin_data->check_out = $checkout_time;
        $checkin_data->hours_worked = $workedHours;
        $checkin_data->checkout_method = $request->out_method;

        if ($checkin_data->save()) {
            return response()->json([
                'success' => true,
                'message' => 'User checked out! Logging out'
            ]);
        }
    }

    public function getuserattendance()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Session Error! Login Again...',
            ]);
        }

        $user_id = auth()->user()->id;

        $records = Attendance::select([
            DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as formatted_date"),
            'status',
            'check_in',
            'check_out',
            'hours_worked',
        ])
            ->where('user_id', $user_id)
            ->whereMonth('date', Carbon::now()->month)
            ->orderBy('date', 'asc')
            ->get();

        if ($records->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No record found for this user!'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Table updated successfully!',
            'records' => $records
        ]);
    }

    public function showadminuserattendance()
    {
        if (!session()->has('super_admin_loged')) {
            return redirect('/')->with('error', 'Only Super Admins are allowed!');
        }

        $all_users = User::get()->count();
        $present_users = Attendance::where('status', '=', 'Present')->whereDate('date', Carbon::today())->count();
        $absent_users = Attendance::where('status', '=', 'Absent')->whereDate('date', Carbon::today())->count();
        $late_users = Attendance::where('status', '=', 'Late')->whereDate('date', Carbon::today())->count();

        return view('chat.user_attendance', [
            'name' => Auth::user()->name,
            'img' => Auth::user()->image,
            'email' => Auth::user()->email,
            'total_users' => $all_users,
            'present_users' => $present_users,
            'absent_users' => $absent_users,
            'late_users' => $late_users,
        ]);
    }

    public function attendancetabledata(Request $request)
    {
        if (!Auth::user()->type === 'super_admin') {
            return redirect('/')->with('error', 'Super Admin Session not found!');
        }

        $date = $request->input('date') ?? Carbon::today()->toDateString();

        $records = Attendance::with('user')
            ->whereDate('date', $date)
            ->get();

        $records = $records->map(function ($record) {
            return [
                'status' => $record->status,
                'check_in' => $record->check_in ? Carbon::parse($record->check_in)->format('H:i') : 'Pending...',
                'check_out' => $record->check_out ? Carbon::parse($record->check_out)->format('H:i') : 'Pending...',
                'hours_worked' => $record->hours_worked ? $record->hours_worked . ' hours' : 'Pending...',
                'username' => $record->user->name ?? 'Unknown User',
                'useremail' => $record->user->email ?? 'Unknown Email',
                'user_image' => $record->user->image
                    ? asset($record->user->image)
                    : asset('assets/images/default.png'),
                'date' => Carbon::parse($record->date)->toDateString(),
            ];
        });

        $total_present = $records->where('status', 'Present')->count();
        $total_late = $records->where('status', 'Late')->count();
        $total_absent = $records->where('status', 'Absent')->count();

        return response()->json([
            'success' => true,
            'message' => 'Table updated successfully!',
            'records' => $records,
            'present_users' => $total_present,
            'late_users'=> $total_late,
            'absent_users'=> $total_absent,
        ]);
    }

    public function usertaskspage() 
    {
        if(!Auth::check()){
            return redirect('/')->with('error', 'Session not found!');
        }

        
    }
}
