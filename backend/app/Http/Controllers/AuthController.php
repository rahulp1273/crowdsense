<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Admin;
use App\Mail\PasswordResetOtp;
use App\Services\AuthService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6'
        ]);

        try {
            $result = $this->authService->registerUser($validated);
            Log::info("Verification OTP for {$result['user']->email} is: {$result['otp']}");
            
            return response()->json([
                'message' => 'Verification code sent to your email.',
                'email' => $result['user']->email
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        try {
            $result = $this->authService->attemptLogin($request->email, $request->password);
            
            if (isset($result['unverified'])) {
                return response()->json($result, 403);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 401);
        }
    }

    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            $otp = $this->authService->resendVerificationOtp($request->email);
            Log::info("New verification OTP for {$request->email} is: {$otp}");
            return response()->json(['message' => 'A new verification code has been sent.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 404);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        try {
            $result = $this->authService->verifyUserOtp($request->email, $request->otp);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower($request->email);

        $user = null;
        if (Schema::hasTable('admins')) {
            $user = Admin::where('email', $email)->first();
        }
        
        if (!$user) {
            $user = User::where('email', $email)->first();
        }

        if (!$user) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        $otp = sprintf("%06d", mt_rand(1, 999999));
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        Mail::to($user->email)->send(new PasswordResetOtp($otp));
        Log::info("Password reset OTP for {$user->email} is: {$otp}");

        return response()->json(['message' => 'Reset code sent to your email.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $email = strtolower($request->email);
        
        $user = null;
        if (Schema::hasTable('admins')) {
            $user = Admin::where('email', $email)->first();
        }
        
        if (!$user) {
            $user = User::where('email', $email)->first();
        }

        if (!$user || $user->otp !== $request->otp || Carbon::now()->isAfter($user->otp_expires_at)) {
            return response()->json(['message' => 'Invalid or expired code.'], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expires_at' => null
        ]);

        return response()->json(['message' => 'Password reset successfully.']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
