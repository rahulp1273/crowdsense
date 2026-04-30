<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminManagementService
{
    /**
     * Create a new administrator account with specific permissions.
     */
    public function createAdmin(array $data)
    {
        $email = strtolower($data['email']);

        // Check for duplicates
        if (Admin::where('email', $email)->exists() || User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This email is already registered in the system.'
            ]);
        }

        $tempPassword = Str::random(12);

        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($tempPassword),
            'email_verified_at' => now(),
            'is_super_admin' => $data['is_super_admin'] ?? false,
            'permissions' => $data['permissions'] ?? [],
        ]);

        Log::info("New Admin Created: {$admin->email} | Temporary Password: {$tempPassword}");

        return [
            'admin' => $admin,
            'temporary_password' => $tempPassword
        ];
    }

    /**
     * Update an administrator's details and permissions.
     */
    public function updateAdmin($id, array $data)
    {
        $admin = Admin::findOrFail($id);
        
        // Prevent editing super admin status if you are not a super admin (handled in controller)
        $admin->update($data);
        
        return $admin;
    }

    /**
     * List all administrators.
     */
    public function getAllAdmins()
    {
        return Admin::all();
    }

    /**
     * Delete an administrator account.
     */
    public function deleteAdmin($id, $currentAdminId)
    {
        $admin = Admin::findOrFail($id);

        if ($id == $currentAdminId) {
            throw new \Exception('You cannot delete your own account.', 422);
        }

        // Prevent deleting the last super admin
        if ($admin->is_super_admin && Admin::where('is_super_admin', true)->count() <= 1) {
            throw new \Exception('You cannot delete the last super administrator.', 422);
        }

        $admin->delete();

        return true;
    }
}
