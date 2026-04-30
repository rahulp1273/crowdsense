<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AuthService
{
    /**
     * Handle user registration logic.
     */
    public function registerUser(array $data)
    {
        $email = strtolower($data['email']);

        // Check if exists in EITHER table
        if ((Schema::hasTable('admins') && Admin::where('email', $email)->exists()) || User::where('email', $email)->exists()) {
            throw new \Exception('This email is already registered. Please sign in.', 422);
        }

        $otp = sprintf("%06d", mt_rand(1, 999999));
        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($data['password']),
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        return ['user' => $user, 'otp' => $otp];
    }

    /**
     * Handle unified login logic.
     */
    public function attemptLogin($email, $password)
    {
        $email = strtolower($email);

        // 1. Check Admin
        if (Schema::hasTable('admins')) {
            $admin = Admin::where('email', $email)->first();
            if ($admin && Hash::check($password, $admin->password)) {
                $token = $admin->createToken('admin_token')->plainTextToken;
                return [
                    'user' => array_merge($admin->toArray(), ['role' => 'admin']),
                    'token' => $token
                ];
            }
        }

        // 2. Check User
        $user = User::where('email', $email)->first();
        if ($user) {
            if (!Hash::check($password, $user->password)) {
                throw new \Exception('Invalid credentials.', 401);
            }

            if (!$user->email_verified_at) {
                return [
                    'unverified' => true,
                    'email' => $user->email,
                    'message' => 'Your account is not verified. Please verify your email.'
                ];
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            return [
                'user' => array_merge($user->toArray(), ['role' => 'user']),
                'token' => $token
            ];
        }

        throw new \Exception('No account found with this email.', 404);
    }

    /**
     * Resend verification OTP.
     */
    public function resendVerificationOtp($email)
    {
        $email = strtolower($email);
        $user = User::where('email', $email)->whereNull('email_verified_at')->first();
        
        if (!$user) {
            throw new \Exception('User not found or already verified.', 404);
        }

        $otp = sprintf("%06d", mt_rand(1, 999999));
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        return $otp;
    }

    /**
     * Verify account via OTP.
     */
    public function verifyUserOtp($email, $otp)
    {
        $email = strtolower($email);
        $user = User::where('email', $email)->first();

        if (!$user || $user->otp !== $otp || Carbon::now()->isAfter($user->otp_expires_at)) {
            throw new \Exception('Invalid or expired code.', 400);
        }

        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'email_verified_at' => Carbon::now()
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'user' => array_merge($user->toArray(), ['role' => 'user']),
            'token' => $token
        ];
    }
    /**
     * Send password reset OTP to either Admin or User.
     */
    public function sendPasswordResetOtp($email)
    {
        $email = strtolower($email);
        $user = null;

        if (Schema::hasTable('admins')) {
            $user = Admin::where('email', $email)->first();
        }
        
        if (!$user) {
            $user = User::where('email', $email)->first();
        }

        if (!$user) {
            throw new \Exception('Account not found.', 404);
        }

        $otp = sprintf("%06d", mt_rand(1, 999999));
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\PasswordResetOtp($otp));
        \Illuminate\Support\Facades\Log::info("Password reset OTP for {$user->email} is: {$otp}");

        return true;
    }

    /**
     * Reset password using OTP.
     */
    public function resetPassword(array $data)
    {
        $email = strtolower($data['email']);
        $user = null;

        if (Schema::hasTable('admins')) {
            $user = Admin::where('email', $email)->first();
        }
        
        if (!$user) {
            $user = User::where('email', $email)->first();
        }

        if (!$user || $user->otp !== $data['otp'] || Carbon::now()->isAfter($user->otp_expires_at)) {
            throw new \Exception('Invalid or expired code.', 400);
        }

        $user->update([
            'password' => Hash::make($data['password']),
            'otp' => null,
            'otp_expires_at' => null
        ]);

        return true;
    }
}
