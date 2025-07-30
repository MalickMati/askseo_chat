<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ShowPageController extends Controller
{
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
        if(!$user){
            return redirect('/login')->with('error','Session Expired. Login again');
        } elseif ($user->type !== 'super_admin' 
            && $user->type !== 'admin' 
            // && $user->type !== 'moderator'
        ){
            return redirect('/chat')->with('error','Admin not found!');
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
}
