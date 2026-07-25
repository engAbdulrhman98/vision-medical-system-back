<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EmployeeController extends Controller
{
    /**
     * Display a listing of all employees/users.
     */
    public function index()
    {
        $users = User::with(['roles', 'tasks.client'])->orderBy('name')->get()->map(function ($user) {
            $totalTasks = $user->tasks->count();
            $completedTasks = $user->tasks->where('status', 'completed')->count();
            $inProgressTasks = $user->tasks->where('status', 'in_progress')->count();
            $pendingTasks = $user->tasks->where('status', 'pending')->count();
            $cancelledTasks = $user->tasks->where('status', 'cancelled')->count();

            // Prepare list of active assignments
            $activeTasksList = $user->tasks->whereIn('status', ['pending', 'in_progress'])->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->getTranslations('title'),
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'scheduled_at' => $task->scheduled_at,
                    'client_name' => $task->client ? $task->client->getTranslations('name') : null,
                ];
            })->values()->all();

            // Prepare list of recently completed tasks
            $recentCompletedTasks = $user->tasks->where('status', 'completed')->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->getTranslations('title'),
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'scheduled_at' => $task->scheduled_at,
                    'completed_at' => $task->completed_at,
                    'client_name' => $task->client ? $task->client->getTranslations('name') : null,
                ];
            })->values()->all();

            return [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->roles->pluck('name')->first() ?? 'No Role',
                'created_at' => $user->created_at?->format('Y-m-d'),
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'in_progress_tasks' => $inProgressTasks,
                'pending_tasks' => $pendingTasks,
                'cancelled_tasks' => $cancelledTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
                'active_tasks' => $activeTasksList,
                'completed_tasks_list' => $recentCompletedTasks,
            ];
        });

        return response()->json($users);
    }

    /**
     * Store a new employee.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'username' => 'nullable|string|unique:users,username|max:255',
            'password' => 'required|string|min:6',
            'role'     => 'required|string|exists:roles,name',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $username = $request->username;
        if (!$username) {
            $baseUsername = strtolower(explode('@', $request->email)[0]);
            $username = \Illuminate\Support\Str::slug($baseUsername, '_');
        }

        $roleName = strtolower($request->role);
        $isExempt = str_contains($roleName, 'admin') || str_contains($roleName, 'ceo') || str_contains($roleName, 'manager');

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'username' => $username,
            'password' => Hash::make($request->password),
            'must_change_password' => $isExempt ? false : true,
            'avatar'   => $avatarPath,
        ]);

        $user->assignRole($request->role);

        return response()->json([
            'message' => 'Employee created successfully',
            'employee' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'username'   => $user->username,
                'role'       => $user->roles->pluck('name')->first(),
                'must_change_password' => (bool) $user->must_change_password,
                'avatar'     => $user->avatar ? asset('storage/' . $user->avatar) : null,
                'created_at' => $user->created_at->format('Y-m-d'),
            ],
        ], 201);
    }

    /**
     * Update an existing employee's info.
     */
    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $employee->id,
            'role'     => 'required|string|exists:roles,name',
            'password' => 'nullable|string|min:8',
        ]);

        $employee->update([
            'name'  => $request->name,
            'email' => $request->email,
            ...(($request->password) ? ['password' => Hash::make($request->password)] : []),
        ]);

        $employee->syncRoles([$request->role]);

        return response()->json([
            'message' => 'Employee updated successfully',
            'employee' => [
                'id'         => $employee->id,
                'name'       => $employee->name,
                'email'      => $employee->email,
                'role'       => $employee->roles->pluck('name')->first(),
                'created_at' => $employee->created_at->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Delete an employee account.
     */
    public function destroy(User $employee)
    {
        // Prevent deleting yourself
        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }

    /**
     * Get direct, role-based, and all permissions for an employee.
     */
    public function getPermissions(User $employee)
    {
        $allPermissions = Permission::orderBy('name')->get()->map(function ($p) {
            return [
                'name' => $p->name,
                'id'   => $p->id
            ];
        });
        
        $directPermissions = $employee->getDirectPermissions()->pluck('name')->toArray();
        $rolePermissions = $employee->getPermissionsViaRoles()->pluck('name')->toArray();

        return response()->json([
            'all'    => $allPermissions,
            'direct' => $directPermissions,
            'role'   => $rolePermissions,
        ]);
    }

    /**
     * Sync direct permissions for an employee.
     */
    public function syncPermissions(Request $request, User $employee)
    {
        $request->validate([
            'permissions' => 'present|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $employee->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Direct permissions updated successfully',
            'direct' => $employee->getDirectPermissions()->pluck('name')->toArray(),
        ]);
    }
}
