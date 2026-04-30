<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProfileUpdateOtp;
use Carbon\Carbon;

class ProfileService
{
    /**
     * Update user profile with OTP verification for sensitive changes.
     */
    public function updateProfile(User $user, array $data)
    {
        $emailChanged = $user->email !== $data['email'];
        $passwordChanged = !empty($data['password']);

        // If sensitive changes are made, check for OTP
        if ($emailChanged || $passwordChanged) {
            if (empty($data['otp'])) {
                return $this->sendOtp($user);
            }

            if (!$this->verifyOtp($user, $data['otp'])) {
                throw new \Exception('Invalid or expired OTP', 422);
            }

            // Reset OTP after verification
            $user->update(['otp' => null, 'otp_expires_at' => null]);
        }

        // Apply changes
        $user->name = $data['name'];
        $user->email = $data['email'];
        
        if ($passwordChanged) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return [
            'otp_required' => false,
            'user' => $user
        ];
    }

    /**
     * Generate and send OTP to user.
     */
    private function sendOtp(User $user)
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        Mail::to($user->email)->send(new ProfileUpdateOtp($otp));

        return [
            'otp_required' => true,
            'message' => 'OTP sent to your email for verification'
        ];
    }

    /**
     * Verify if the provided OTP is valid and not expired.
     */
    private function verifyOtp(User $user, $otp)
    {
        return $user->otp === $otp && Carbon::now()->isBefore($user->otp_expires_at);
    }
}
