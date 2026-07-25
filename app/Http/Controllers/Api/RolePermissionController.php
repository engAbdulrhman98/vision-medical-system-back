<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function getRoles()
    {
        $roles = Role::with('permissions')->get()->map(function($role) {
            $descriptions = [
                'Admin' => [
                    'ar' => 'صلاحيات إدارية كاملة لإدارة النظام العام والمستخدمين والصيانة والمستودعات.',
                    'en' => 'Full administrative access to manage system, users, maintenance, and inventory.'
                ],
                'CEO' => [
                    'ar' => 'صلاحيات كاملة وغير محدودة للنظام المالي والمستودعات والمبيعات والتقارير الاستراتيجية.',
                    'en' => 'Full unrestricted access to financials, inventory, sales, and strategic reports.'
                ],
                'Operations Manager' => [
                    'ar' => 'إدارة جداول الصيانة الدورية وتعيين الفنيين والمهندسين وتتبع التقارير الفنية.',
                    'en' => 'Manage periodic maintenance schedules, assign engineers, and track technical reports.'
                ],
                'Service Engineer outdoor' => [
                    'ar' => 'متابعة وفحص الأجهزة الطبية في الموقع وإدخال تقارير المعايرة والقياس الخارجية.',
                    'en' => 'Perform on-site calibrations, inspect medical devices, and log field/outdoor reports.'
                ],
                'Service Engineer indoor' => [
                    'ar' => 'العمل الفني على الأجهزة الطبية داخل ورشة الصيانة وإدخال قطع الغيار والمستندات الداخلية.',
                    'en' => 'Technical repair of medical equipment inside the workshop and log internal maintenance.'
                ],
                'Accountant' => [
                    'ar' => 'إدارة الحسابات، عرض وطباعة عروض الأسعار الطبية، ومتابعة الفواتير والتحصيل المالي.',
                    'en' => 'Manage accounting, view and print medical quotations, and track invoices & collections.'
                ],
                'Sale' => [
                    'ar' => 'إدارة طلبات المبيعات، التواصل مع العملاء، ومراقبة حركة المنتجات والأجهزة المباعة.',
                    'en' => 'Manage sales orders, communicate with clients, and monitor product sales.'
                ]
            ];

            return [
                'name' => $role->name,
                'description' => $descriptions[$role->name] ?? ['ar' => $role->name, 'en' => $role->name],
                'permissions' => $role->permissions->pluck('name')
            ];
        });

        return response()->json($roles);
    }

    public function getUsers()
    {
        $users = User::with('roles')->get()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->first() ?? 'Service Engineer'
            ];
        });

        return response()->json($users);
    }

    public function assignRole(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|string'
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();
        
        $user->syncRoles([$validated['role']]);

        return response()->json([
            'message' => 'Role assigned successfully',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->first()
            ]
        ]);
    }

    public function togglePermission(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string',
            'permission' => 'required|string'
        ]);

        $role = Role::findByName($validated['role_name']);
        
        $permission = Permission::findOrCreate($validated['permission']);

        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
            $action = 'revoked';
        } else {
            $role->givePermissionTo($permission);
            $action = 'granted';
        }

        return response()->json([
            'message' => 'Permission updated successfully',
            'has_permission' => $role->hasPermissionTo($permission)
        ]);
    }

    public function createRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string'
        ]);

        $role = Role::create([
            'name' => trim($validated['name']),
            'guard_name' => 'web'
        ]);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => [
                'name' => $role->name,
                'description' => [
                    'ar' => $validated['description_ar'] ?? $role->name,
                    'en' => $validated['description_en'] ?? $role->name
                ],
                'permissions' => []
            ]
        ], 201);
    }
}
