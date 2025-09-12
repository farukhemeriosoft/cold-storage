<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with their roles
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
     * Store a newly created user
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_ids' => 'array',
            'role_ids.*' => 'exists:roles,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign roles if provided
        if ($request->has('role_ids')) {
            $user->syncRoles($request->role_ids);
        }

        $user->load('roles');

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified user
     */
    public function show(User $user): JsonResponse
    {
        $user->load('roles');
        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $user
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:8',
            'role_ids' => 'array',
            'role_ids.*' => 'exists:roles,id'
        ]);

        $updateData = $request->only(['name', 'email']);

        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Update roles if provided
        if ($request->has('role_ids')) {
            $user->syncRoles($request->role_ids);
        }

        $user->load('roles');

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user): JsonResponse
    {
        // Assuming we add an 'is_active' field to users table
        // For now, we'll use a different approach - we can deactivate by removing all roles
        if ($user->roles()->count() > 0) {
            $user->syncRoles([]);
            $message = 'User deactivated successfully';
        } else {
            // Reactivate by assigning a default role
            $defaultRole = Role::where('slug', 'staff')->first();
            if ($defaultRole) {
                $user->assignRole($defaultRole);
                $message = 'User activated successfully';
            } else {
                return response()->json(['message' => 'No default role found'], 400);
            }
        }

        $user->load('roles');
        return response()->json([
            'message' => $message,
            'data' => $user
        ]);
    }

    /**
     * Get all roles for dropdown
     */
    public function getRoles(): JsonResponse
    {
        $roles = Role::active()->get();
        return response()->json([
            'message' => 'Roles retrieved successfully',
            'data' => $roles
        ]);
    }
}
