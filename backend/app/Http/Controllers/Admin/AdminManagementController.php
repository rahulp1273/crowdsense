<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Services\Admin\AdminManagementService;

class AdminManagementController extends Controller
{
    protected $adminService;

    public function __construct(AdminManagementService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * List all administrators.
     */
    public function index(Request $request)
    {
        if (!($request->user() instanceof Admin) || !$request->user()->hasPermission('manage_admins')) {
            return response()->json(['message' => 'Unauthorized. You do not have permission to manage other admins.'], 403);
        }

        return response()->json($this->adminService->getAllAdmins());
    }

    /**
     * Create a new administrator account.
     */
    public function store(Request $request)
    {
        if (!($request->user() instanceof Admin) || !$request->user()->hasPermission('manage_admins')) {
            return response()->json(['message' => 'Unauthorized. You do not have permission to add admins.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'is_super_admin' => 'boolean',
            'permissions' => 'array',
        ]);

        // Only super admins can grant super admin status
        if (isset($validated['is_super_admin']) && $validated['is_super_admin'] && !$request->user()->is_super_admin) {
            return response()->json(['message' => 'Only Super Admins can create other Super Admins.'], 403);
        }

        try {
            $result = $this->adminService->createAdmin($validated);
            return response()->json([
                'message' => 'Administrator account created successfully.',
                'admin' => $result['admin'],
                'temporary_password' => $result['temporary_password']
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update an administrator's permissions.
     */
    public function update(Request $request, $id)
    {
        if (!($request->user() instanceof Admin) || !$request->user()->hasPermission('manage_admins')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'is_super_admin' => 'sometimes|boolean',
            'permissions' => 'sometimes|array',
        ]);

        // Safety: Non-super admins cannot grant super admin status
        if (isset($validated['is_super_admin']) && $validated['is_super_admin'] && !$request->user()->is_super_admin) {
            return response()->json(['message' => 'Only Super Admins can promote others to Super Admin.'], 403);
        }

        try {
            $admin = $this->adminService->updateAdmin($id, $validated);
            return response()->json([
                'message' => 'Administrator updated successfully.',
                'admin' => $admin
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Delete an administrator.
     */
    public function destroy(Request $request, $id)
    {
        if (!($request->user() instanceof Admin) || !$request->user()->hasPermission('manage_admins')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        try {
            $this->adminService->deleteAdmin($id, $request->user()->id);
            return response()->json(['message' => 'Administrator deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}
