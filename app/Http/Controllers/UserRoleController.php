<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserRoleController extends Controller
{
    /**
     * Display a listing of users with their roles.
     */
    public function index(): JsonResponse
    {
        $users = User::with('roles')->get();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users
        ]);
    }

    /**
     * Get roles for a specific user.
     */
    public function getUserRoles(User $user): JsonResponse
    {
        $roles = $user->roles()->active()->get();

        return response()->json([
            'message' => 'User roles retrieved successfully',
            'data' => [
                'user' => $user,
                'roles' => $roles
            ]
        ]);
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $user->assignRole($role);

        return response()->json([
            'message' => 'Role assigned successfully',
            'data' => [
                'user' => $user,
                'role' => $role
            ]
        ]);
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole(User $user, Role $role): JsonResponse
    {
        $user->removeRole($role);

        return response()->json([
            'message' => 'Role removed successfully'
        ]);
    }

    /**
     * Sync roles for a user (replace all existing roles).
     */
    public function syncRoles(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id'
        ]);

        $user->syncRoles($validated['role_ids']);
        $user->load('roles');

        return response()->json([
            'message' => 'User roles synchronized successfully',
            'data' => [
                'user' => $user,
                'roles' => $user->roles
            ]
        ]);
    }
}
