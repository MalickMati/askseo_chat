<?php

namespace App\Http\Controllers;

use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Message;
use App\Models\Group;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\GroupMessageRead;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChatController extends Controller
{
    public function getMessages($receiver_id)
    {
        $sender = User::where("email", session("user_email"))->first();

        if (!$sender || $sender->status !== 'active') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $messages = Message::where(function ($query) use ($sender, $receiver_id) {
            $query->where('sender_id', $sender->id)
                ->where('receiver_id', $receiver_id);
        })
            ->orWhere(function ($query) use ($sender, $receiver_id) {
                $query->where('sender_id', $receiver_id)
                    ->where('receiver_id', $sender->id);
            })
            ->orderBy('created_at', 'desc') // get latest messages first
            ->paginate(50);

        // Reverse to show oldest first (like a normal chat window)
        $reversed = collect($messages->items())->reverse()->values();

        return response()->json([
            'messages' => $reversed,
            'has_more' => $messages->hasMorePages(),
            'next_page' => $messages->nextPageUrl()
        ]);
    }

    public function getPrivatefilter(Request $request)
    {
        $receiverId = $request->input('receiver_id');
        $filter = $request->input('filter');

        $query = Message::where(function ($query) use ($receiverId) {
            $query->where(function ($q) use ($receiverId) {
                $q->where('sender_id', auth()->id())
                    ->where('receiver_id', $receiverId);
            })->orWhere(function ($q) use ($receiverId) {
                $q->where('sender_id', $receiverId)
                    ->where('receiver_id', auth()->id());
            });
        });

        $this->applyFilter($query, $filter); // query passed by reference

        return response()->json([
            'messages' => $query->latest()->paginate(20)
        ]);
    }

    public function getGroupMessages($groupId)
    {
        $messages = Message::where('group_id', $groupId)
            ->orderBy('sent_at', 'desc') // get latest 100 messages
            ->paginate(100);

        $orderedMessages = collect($messages->items())->reverse()->values(); // reverse for chronological order

        return response()->json([
            'success' => true,
            'messages' => $orderedMessages,
            'has_more' => $messages->hasMorePages(),
            'next_page' => $messages->nextPageUrl()
        ]);
    }

    public function getGroupfilter(Request $request)
    {
        $groupId = $request->input('group_id');
        $filter = $request->input('filter'); // 'media', 'documents', 'links'

        $query = Message::where('group_id', $groupId);

        $this->applyFilter($query, $filter);

        return response()->json([
            'messages' => $query->latest()->paginate(20)
        ]);
    }

    private function applyFilter(&$query, $filter)
    {
        if ($filter === 'media') {
            $query->whereIn('file_extension', ['mp3', 'mp4', 'mkv', 'avi', 'webm', 'wav', 'ogg', 'png', 'jpg', 'jpeg', 'gif', 'webp']);
        } elseif ($filter === 'documents') {
            $query->whereIn('file_extension', ['pdf', 'docx', 'doc', 'txt', 'xls', 'xlsx', 'apk', 'zip', 'rar']);
        } elseif ($filter === 'links') {
            $query->where('message', 'REGEXP', 'https?://|www\\.|\\b[a-z0-9.-]+\\.(com|net|org|pk|me|info)\\b');
        }
    }

    public function sendGroupMessage(Request $request)
    {
        try {
            $request->validate([
                'group_id' => 'required|exists:groups,id',
                'message' => 'nullable|string|max:1000',
                'file' => 'nullable|file|max:153600',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        }

        $verified_user = GroupMember::where('group_id', '=', $request->group_id)->where('user_id', '=', Auth::user()->id)->first();
        if (!$verified_user) {
            return response()->json([
                'success'=> false,
                'message'=> 'You are in the group! Redirecting...',
                'redirect' => '/chat'
            ]);
        }

        try {
            $user = User::where('email', session('user_email'))->firstOrFail();
            $messages = [];

            // Text message
            if ($request->filled('message')) {
                $message = Message::create([
                    'sender_id' => $user->id,
                    'group_id' => $request->group_id,
                    'message' => $request->message,
                    'sent_at' => now(),
                    'type' => 'text',
                ]);

                GroupMessageRead::create([
                    'message_id' => $message->id,
                    'user_id' => $user->id,
                    'read_at' => now(),
                ]);

                $messages[] = $message;
            }

            // Single file message
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $filename = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $uniqueName = $filename . '_' . Str::random(8) . '.' . $extension;

                $path = $file->storeAs('chat_files', $uniqueName, 'public');

                $message = Message::create([
                    'sender_id' => $user->id,
                    'group_id' => $request->group_id,
                    'file_path' => $path,
                    'message' => $originalName,
                    'sent_at' => now(),
                    'type' => 'file',
                    'file_extension' => $extension,
                ]);

                GroupMessageRead::create([
                    'message_id' => $message->id,
                    'user_id' => $user->id,
                    'read_at' => now(),
                ]);

                $messages[] = $message;
            }

            return response()->json([
                'success' => true,
                'messages' => $messages,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong while sending group message.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getChatList(Request $request)
    {
        $currentUser = User::where("email", session("user_email"))->first();

        if (!$currentUser || $currentUser->status !== 'active') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Users list
        $users = User::where('email', '!=', $currentUser->email)->get();
        $userList = $users->map(function ($user) use ($currentUser) {
            $lastMessage = Message::where(function ($query) use ($user, $currentUser) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $currentUser->id);
            })
                ->orWhere(function ($query) use ($user, $currentUser) {
                    $query->where('sender_id', $currentUser->id)
                        ->where('receiver_id', $user->id);
                })
                ->orderByDesc('sent_at')
                ->first();

            $formattedTime = null;
            $messageText = null;

            if ($lastMessage) {
                $sentAt = \Carbon\Carbon::parse($lastMessage->sent_at);
                $now = \Carbon\Carbon::now();

                if ($sentAt->isToday()) {
                    $formattedTime = $sentAt->format('h:i A'); // e.g., 04:35 PM
                } elseif ($sentAt->isYesterday()) {
                    $formattedTime = 'Yesterday';
                } elseif ($sentAt->diffInDays($now) < 7) {
                    $formattedTime = $sentAt->format('l'); // e.g., Monday
                } else {
                    $formattedTime = $sentAt->format('M d'); // e.g., Jul 21
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

        // Groups list (simplified for now)
        $groups = Group::all()->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
            ];
        });

        return response()->json([
            'groups' => $groups,
            'users' => $userList,
        ]);
    }

    public function sendMessage(Request $request)
    {
        try {
            // Validate basic input first
            $validator = Validator::make($request->all(), [
                'receiver_id' => 'required|exists:users,id',
                'message' => 'nullable|string|max:1000',
                'file' => 'nullable|file|max:153600',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $sender = User::where('email', session('user_email'))->first();

            if (!$sender) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sender not found.',
                ], 403);
            }

            $messages = [];

            // Handle text message
            if ($request->filled('message')) {
                try {
                    $textMessage = Message::create([
                        'sender_id' => $sender->id,
                        'receiver_id' => $request->receiver_id,
                        'message' => $request->message,
                        'type' => 'text',
                    ]);
                    $messages[] = $textMessage;
                } catch (\Exception $e) {
                    Log::error('Text message save failed: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to save text message.',
                    ], 500);
                }
            }

            if ($request->hasFile('file')) {
                try {
                    $file = $request->file('file');
                    $originalName = $file->getClientOriginalName();
                    $filename = pathinfo($originalName, PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $uniqueName = $filename . '_' . Str::random(8) . '.' . $extension;

                    $path = $file->storeAs('chat_files', $uniqueName, 'public');

                    $fileMessage = Message::create([
                        'sender_id' => $sender->id,
                        'receiver_id' => $request->receiver_id,
                        'file_path' => $path,
                        'message' => $originalName,
                        'type' => 'file',
                        'file_extension' => $extension,
                    ]);

                    $messages[] = $fileMessage;
                } catch (\Exception $e) {
                    Log::error('File upload or message save failed: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload and save file.',
                    ], 500);
                }
            }

            if (empty($messages)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No message or file provided.',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'messages' => $messages,
            ]);

        } catch (\Exception $e) {
            Log::error('Unexpected error in sendMessage: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    public function getSidebarData()
    {
        if (!session()->has('user_email')) {
            return redirect('/')->with('error', 'Session expired! Login Again');
        }

        $currentUser = User::where('email', session('user_email'))->first();

        // Get individual users (excluding current user)
        $users = User::where('id', '!=', $currentUser->id)
            ->where('status', '=', 'active')
            ->get()
            ->map(function ($user) use ($currentUser) {
                // Last message between current user and this user
                $lastMessage = Message::where(function ($q) use ($user, $currentUser) {
                    $q->where('sender_id', $currentUser->id)->where('receiver_id', $user->id);
                })->orWhere(function ($q) use ($user, $currentUser) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $currentUser->id);
                })->latest()->first();

                // Count unread messages FROM this user TO current user
                $unreadCount = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $currentUser->id)
                    ->whereNull('read_at')
                    ->count();

                // Format timestamp
                $formattedTime = '';
                if ($lastMessage) {
                    $sentAt = \Carbon\Carbon::parse($lastMessage->sent_at ?? $lastMessage->created_at);
                    $now = \Carbon\Carbon::now();

                    if ($sentAt->isToday()) {
                        $formattedTime = $sentAt->format('h:i A');
                    } elseif ($sentAt->isYesterday()) {
                        $formattedTime = 'Yesterday';
                    } elseif ($sentAt->diffInDays($now) < 7) {
                        $formattedTime = $sentAt->format('l');
                    } else {
                        $formattedTime = $sentAt->format('M d');
                    }
                }

                return [
                    'id' => $user->id,
                    'username' => $user->name,
                    'img' => $user->image,
                    'status' => $user->status_mode,
                    'last_message' => $lastMessage ? ($lastMessage->message ?? '[File]') : '',
                    'last_time' => $formattedTime,
                    'unread_count' => $unreadCount,
                    'last_timestamp' => $lastMessage ? ($lastMessage->sent_at ?? $lastMessage->created_at) : null, // for sorting
                ];
            })
            // Sort users by latest message timestamp descending
            ->sortByDesc(function ($user) {
                return $user['last_timestamp'];
            })
            // Re-index to reset numeric keys (important if you're returning JSON)
            ->values();

        // Get groups the current user is in
        $groups = Group::whereHas('members', function ($q) use ($currentUser) {
            $q->where('user_id', $currentUser->id);
        })
            ->with('messages')
            ->get()
            ->map(function ($group) use ($currentUser) {
                $lastMessage = $group->messages()->latest()->first();

                $unreadCount = DB::table('messages')
                    ->leftJoin('group_message_reads', function ($join) use ($currentUser) {
                        $join->on('messages.id', '=', 'group_message_reads.message_id')
                            ->where('group_message_reads.user_id', '=', $currentUser->id);
                    })
                    ->where('messages.group_id', $group->id)
                    ->where('messages.sender_id', '!=', $currentUser->id)
                    ->whereNull('group_message_reads.read_at')
                    ->count();

                $formattedTime = '';
                if ($lastMessage) {
                    $sentAt = \Carbon\Carbon::parse($lastMessage->sent_at ?? $lastMessage->created_at);
                    $now = \Carbon\Carbon::now();

                    if ($sentAt->isToday()) {
                        $formattedTime = $sentAt->format('h:i A');
                    } elseif ($sentAt->isYesterday()) {
                        $formattedTime = 'Yesterday';
                    } elseif ($sentAt->diffInDays($now) < 7) {
                        $formattedTime = $sentAt->format('l');
                    } else {
                        $formattedTime = $sentAt->format('M d');
                    }
                }

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'last_message' => $lastMessage ? ($lastMessage->message ?? '[File]') : 'Group Chat',
                    'last_time' => $formattedTime,
                    'unread_count' => $unreadCount,
                ];
            });

        return response()->json([
            'users' => $users,
            'groups' => $groups
        ]);
    }

    public function markAsRead(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Session Expired! Login Again');
        }
        $senderId = $request->sender_id;
        $currentUser = User::where('email', session('user_email'))->first();

        Message::where('sender_id', $senderId)
            ->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['status' => 'success']);
    }
    public function markGroupMessagesRead($groupId)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Session Expired! Login Again');
        }

        $verified_user = GroupMember::where('group_id', '=', $groupId)->where('user_id', '=', Auth::user()->id)->first();
        if (!$verified_user) {
            return response()->json([
                'success'=> false,
                'message'=> 'You are removed from group! Redirecting...',
                'redirect' => '/chat'
            ]);
        }

        $currentUser = User::where('email', session('user_email'))->first();

        $unreadMessages = Message::where('group_id', $groupId)
            ->where('sender_id', '!=', $currentUser->id)
            ->leftJoin('group_message_reads', function ($join) use ($currentUser) {
                $join->on('messages.id', '=', 'group_message_reads.message_id')
                    ->where('group_message_reads.user_id', '=', $currentUser->id);
            })
            ->whereNull('group_message_reads.read_at')
            ->select('messages.id')
            ->get();

        foreach ($unreadMessages as $msg) {
            GroupMessageRead::updateOrInsert(
                ['message_id' => $msg->id, 'user_id' => $currentUser->id],
                ['read_at' => Carbon::now()]
            );
        }

        return response()->json(['success' => true]);
    }

    public function leavegroup(Request $request, Group $group)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Session Expired! Login Again');
        }
        $user = User::where('email', '=', session('user_email'))->first();

        $group->members()->detach($user->id);

        return response()->json(['success' => true]);
    }

    public function membersList(Group $group)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Session Expired! Login Again');
        }
        $user = User::where('email', '=', session('user_email'))->first();
        if ($user->type === 'super_admin' || $user->type === 'admin' || $user->type === 'moderator') {
            $allUsers = User::select('id', 'name')->get();
            $groupMemberIds = $group->members()->pluck('users.id')->toArray();

            return response()->json([
                'all_users' => $allUsers,
                'group_member_ids' => $groupMemberIds
            ]);
        }
    }

    public function getMembers($id)
    {
        $group = Group::with(['members:id,name,email,image'])->findOrFail($id);

        $members = $group->members->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->image
                    ? asset($user->image)
                    : asset('assets/images/default.png'),
            ];
        });

        return response()->json([
            'members' => $members
        ]);
    }


    public function addMembers(Request $request, Group $group)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Session Expired! Login Again');
        }
        $user = User::where('email', '=', session('user_email'))->first();

        if ($user->type === 'super_admin' || $user->type === 'admin' || $user->type === 'moderator') {
            $userIds = $request->input('users', []);
            foreach ($userIds as $userId) {
                $group->members()->syncWithoutDetaching([$userId]);
            }

            return response()->json(['message' => 'Users added successfully.']);
        } else {
            return response()->json(['message' => 'Error while adding users!']);
        }

    }

    public function removeMembers(Request $request, Group $group)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Session expired!'], 401);
        }

        $user = User::where('email', '=', session('user_email'))->first();

        if (in_array($user->type, ['super_admin', 'admin', 'moderator'])) {
            $userIds = $request->input('users', []);
            $group->members()->detach($userIds);

            return response()->json(['message' => 'Members removed successfully.']);
        }

        return response()->json(['message' => 'You do not have permission.'], 403);
    }
}
