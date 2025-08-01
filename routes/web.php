<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShowPageController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AttendanceController;

Route::fallback(function () { abort(404);});
Route::fallback(function () { abort(500);});

Route::get('/', [AuthController::class, 'showLogin']);

Route::get('auth/signup', [AuthController::class, 'showSignup'])->name('signup.form');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

Route::get('auth/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::get('login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/verify/otp', [AuthController::class, 'showOtpForm'])->name('verify.form');
Route::post('/verify/otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');

Route::post('/resend/otp', [AuthController::class,'resendOtp'])->name('resend.otp');

Route::get('/admin', [ShowPageController::class, 'showadminuserpage'])->name('admin.users');

Route::get('/fetch-users', [ShowPageController::class,'fetchUsers']);
Route::post('/update-user-status', [ShowPageController::class,'update_user_status']);
Route::post('/delete-user', [ShowPageController::class, 'deleteuser'])->name('user.delete');
Route::post('/users/update', [ShowPageController::class, 'updateuser'])->name('users.update');

Route::get('/chat', [ShowPageController::class, 'showchatpage'])->name('chat');
Route::get('/settings', [ShowPageController::class, 'showsettings']);
Route::post('/update-profile', [ShowPageController::class, 'updateProfile'])->name('profile.update');
Route::get('/add/group', [ShowPageController::class,'showaddgroup']);
Route::post('/create/new/group', [ShowPageController::class,'addnewgroup']);

Route::get('/messages/{receiver_id}', [ChatController::class, 'getMessages']);
Route::get('/group-messages/{group}', [ChatController::class, 'getGroupMessages']);
Route::get('/chat-list', [ChatController::class, 'getChatList']);

Route::post('/messages/send', [ChatController::class, 'sendMessage'])->middleware('auth');
Route::post('/messages/mark-read', [ChatController::class, 'markAsRead']);
Route::post('/group/{groupId}/mark-read', [ChatController::class, 'markGroupMessagesRead']);
Route::post('/group-messages/send', [ChatController::class, 'sendGroupMessage']);
Route::get('/group-messages/{groupId}', [ChatController::class, 'getGroupMessages']);

Route::get('/sidebar/data', [ChatController::class, 'getSidebarData']);

Route::post('/groups/{group}/leave', [ChatController::class, 'leavegroup']);
Route::get('/groups/{group}/members-list', [ChatController::class, 'membersList']);
Route::get('/group/{id}/members', [ChatController::class, 'getMembers']);
Route::post('/groups/{group}/add-members', [ChatController::class, 'addMembers']);

Route::get('/user/attendance', [ShowPageController::class, 'showattendanceuser']);

Route::post('/user-check-in', [ShowPageController::class, 'usercheckin']);
Route::post('/check-out-user', [ShowPageController::class, 'usercheckout']);
Route::post('/get-user-attendance', [ShowPageController::class,'getuserattendance']);