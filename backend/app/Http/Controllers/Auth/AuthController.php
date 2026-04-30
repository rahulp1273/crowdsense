<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        try {
            $this->authService->sendPasswordResetOtp($request->email);
            return response()->json(['message' => 'Reset code sent to your email.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 404);
        }
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed'
        ]);

        try {
            $this->authService->resetPassword($validated);
            return response()->json(['message' => 'Password reset successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
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
