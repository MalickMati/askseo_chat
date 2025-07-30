<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showSignup()
    {
        return view('auth.signup');
    }

    public function showLogin()
    {
        if(session()->has('user_type') && session()->has('user_email') && Auth::check()){
            return redirect('/chat')->with('message', 'Already Logged session found!');
        }
        return view('auth.login');
    }

    public function signup(Request $request)
    {
        try {
            Log::info('Signup Request Recieved');
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:8',
            ]);

            $otp = rand(100000, 999999);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => 'pending',
                'is_admin' => false,
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new SendOtpMail($otp));
            session(['user_email' => $user->email]);

            return response()->json(['success' => true, 'message' => 'Account created! OTP sent to your email.', 'redirect' => route('verify.form')]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()]);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->filled('remember');
        $email = $request->input('email');

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with this mail!'
            ]);
        }

        if ($user->status === 'pending' && $user->otp !== null) {
            session()->put('user_email', $email);
            return response()->json([
                'success' => false,
                'message' => 'User not verified! Verify First',
                'redirect' => route('verify.form')
            ]);
        }

        if($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Account de-actived by admin!'
            ]);
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            session()->put('user_email', $user->email);
            session()->put('user_type', $user->type);
            if(!$remember) {
                $user->remember_token = null;
            }

            $user->status_mode = 'online';
            $user->save();

            if ($user->type === 'super_admin') {
                Log::info('Super Admin Logged in!');
                session()->put('super_admin_loged', true);
                return response()->json([
                    'success' => true,
                    'message' => 'Super Admin Login Detected!',
                    'redirect' => route('admin.users')
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => route('chat')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials.',
        ], 401);
    }

    public function showOtpForm()
    {
        if (!session()->has('user_email')) {
            return redirect('/auth/login')->with('error', 'Session expired. Please login again.');
        }

        $user = User::where('email', session('user_email'))->first();

        if (!$user || !$user->otp_expires_at) {
            return redirect('/auth/login')->with('error', 'OTP not generated. Please login again.');
        }

        // Calculate time left
        $secondsRemaining = now()->diffInSeconds($user->otp_expires_at, false);

        if ($secondsRemaining <= 0) {
            $this->generateAndSendOtp($user);
        }

        return view('auth.verify', [
            'email' => $user->email,
            'secondsRemaining' => $secondsRemaining
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        if (!session()->has('user_email')) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.',
                'redirect' => route('login.form')
            ]);
        }

        $email = session('user_email');
        $otp = $request->input('otp');

        Log::info('Verifying OTP for email: ' . $email . ', OTP: ' . $otp);

        $user = User::where('email', $email)
            ->where('otp', $otp)
            ->where('otp_expires_at', '>', now())
            ->first();

        if (!$user) {
            Log::warning('OTP verification failed for email: ' . $email);
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP!'
            ]);
        }

        Log::info('OTP Varified for: ' . $email);
        // OTP verified
        $user->email_verified_at = now();
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // session()->forget('user_email');
        session()->put('user_type', $user->type);
        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'redirect' => url('/chat')
        ]);
    }

    private function generateAndSendOtp(User $user)
    {
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new SendOtpMail($otp));
    }

    public function resendOtp(Request $request)
    {
        if (!session()->has('user_email')) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.',
                'redirect' => route('login.form')
            ]);
        }

        $user = User::where('email', session('user_email'))->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'redirect' => route('login.form')
            ]);
        }

        $this->generateAndSendOtp($user); // ✅ reuse helper

        return response()->json([
            'success' => true,
            'message' => 'A new OTP has been sent to your email.'
        ]);
    }

    public function logout()
    {
        $user = User::where('email', '=', session('user_email'))->first();
        $user->status_mode = 'offline';
        $user->save();

        Auth::logout();

        Session::flush();

        return redirect('/login')->with('message', 'You have been logged out.');
    }
}
