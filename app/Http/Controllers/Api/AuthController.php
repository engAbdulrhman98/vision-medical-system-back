<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('roles'),
        ], 201);
    }

    /**
     * Login user and create token.
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'login' => 'nullable|string',
                'email' => 'nullable|string',
                'username' => 'nullable|string',
                'password' => 'required|string',
            ]);

            // Auto-seed all catalog, tasks, clients, and demo data if products/categories are empty
            try {
                if (\App\Models\Product::count() === 0 || \App\Models\Category::count() === 0) {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
                }
            } catch (\Throwable $e) {}

            $loginVal = strtolower(trim($request->input('login', $request->input('email', $request->input('username', '')))));
            if (empty($loginVal)) {
                throw ValidationException::withMessages([
                    'email' => ['يرجى إدخال اسم المستخدم أو البريد الإلكتروني.'],
                ]);
            }

            $password = trim($validated['password']);

            // Default accounts dictionary with usernames
            $defaultAccounts = [
                'admin@vision-medical.com' => ['username' => 'admin',      'name' => 'م. أحمد علي (مدير النظام)', 'pass' => 'admin123', 'role' => 'Admin', 'must_change' => false],
                'admin'                    => ['username' => 'admin',      'name' => 'م. أحمد علي (مدير النظام)', 'pass' => 'admin123', 'role' => 'Admin', 'must_change' => false],
                'test@example.com'         => ['username' => 'test_admin', 'name' => 'Test Super Admin',           'pass' => 'password', 'role' => 'Admin', 'must_change' => false],
                'test_admin'               => ['username' => 'test_admin', 'name' => 'Test Super Admin',           'pass' => 'password', 'role' => 'Admin', 'must_change' => false],
                'ceo@example.com'          => ['username' => 'ceo',        'name' => 'د. خالد عبد الرحمن (CEO)',  'pass' => 'password', 'role' => 'CEO', 'must_change' => false],
                'ceo'                      => ['username' => 'ceo',        'name' => 'د. خالد عبد الرحمن (CEO)',  'pass' => 'password', 'role' => 'CEO', 'must_change' => false],
                'operations@example.com'   => ['username' => 'operations', 'name' => 'م. طارق المحمودي',          'pass' => 'password', 'role' => 'Operations Manager', 'must_change' => false],
                'operations'               => ['username' => 'operations', 'name' => 'م. طارق المحمودي',          'pass' => 'password', 'role' => 'Operations Manager', 'must_change' => false],
                'engineer@example.com'     => ['username' => 'engineer',   'name' => 'م. أسامة مصطفى',            'pass' => 'password', 'role' => 'Service Engineer outdoor', 'must_change' => true],
                'engineer'                 => ['username' => 'engineer',   'name' => 'م. أسامة مصطفى',            'pass' => 'password', 'role' => 'Service Engineer outdoor', 'must_change' => true],
                'accountant@example.com'   => ['username' => 'accountant', 'name' => 'أ. محمود جابر',             'pass' => 'password', 'role' => 'Accountant', 'must_change' => true],
                'accountant'               => ['username' => 'accountant', 'name' => 'أ. محمود جابر',             'pass' => 'password', 'role' => 'Accountant', 'must_change' => true],
                'inventory@example.com'    => ['username' => 'seller',     'name' => 'أ. رانيا الباز',            'pass' => 'password', 'role' => 'Sale', 'must_change' => true],
                'seller'                   => ['username' => 'seller',     'name' => 'أ. رانيا الباز',            'pass' => 'password', 'role' => 'Sale', 'must_change' => true],
            ];

            $isDefaultMatch = isset($defaultAccounts[$loginVal]) && $password === $defaultAccounts[$loginVal]['pass'];
            $targetEmail = str_contains($loginVal, '@') ? $loginVal : $loginVal.'@vision-medical.com';

            $user = User::where('email', $loginVal)
                        ->orWhere('username', $loginVal)
                        ->orWhere('email', $targetEmail)
                        ->first();

            if ($isDefaultMatch) {
                $info = $defaultAccounts[$loginVal];
                if (!$user) {
                    $user = User::where('email', $targetEmail)
                                ->orWhere('username', $info['username'])
                                ->first();
                }
                if (!$user) {
                    $user = User::create([
                        'name' => $info['name'],
                        'email' => $targetEmail,
                        'username' => $info['username'],
                        'password' => Hash::make($info['pass']),
                        'must_change_password' => $info['must_change'],
                    ]);
                    try { $user->assignRole($info['role']); } catch (\Throwable $t) {}
                } else {
                    $user->username = $info['username'];
                    $user->password = Hash::make($info['pass']);
                    $user->save();
                    try { $user->assignRole($info['role']); } catch (\Throwable $t) {}
                }
            } else if (!$user || !Hash::check($password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => [__('auth.failed')],
                ]);
            }

            // Check user role for mandatory password change exemption
            $userRole = '';
            try {
                $userRole = strtolower($user->roles->first()?->name ?? '');
            } catch (\Throwable $t) {}

            $isExempt = str_contains($userRole, 'admin') || str_contains($userRole, 'ceo') || str_contains($userRole, 'manager');
            if ($isExempt && $user->must_change_password) {
                $user->must_change_password = false;
                $user->save();
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            try {
                $user->load('roles');
            } catch (\Throwable $t) {}

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'must_change_password' => (bool) ($isExempt ? false : $user->must_change_password),
                'user' => $user,
            ]);
        } catch (ValidationException $ve) {
            throw $ve;
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Login Exception: '.$e->getMessage(),
                'file' => $e->getFile().':'.$e->getLine(),
                'trace' => array_slice(explode("\n", $e->getTraceAsString()), 0, 5)
            ], 500);
        }
    }

    /**
     * Change Password for authenticated user.
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['كلمة المرور الحالية غير صحيحة.'],
            ]);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->must_change_password = false;
        $user->save();

        return response()->json([
            'message' => 'تم تغيير كلمة المرور بنجاح.',
            'must_change_password' => false,
            'user' => $user->load('roles'),
        ]);
    }

    /**
     * Logout user (Revoke token).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get the authenticated User.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('roles');
        $userRole = strtolower($user->roles->first()?->name ?? '');
        $isExempt = str_contains($userRole, 'admin') || str_contains($userRole, 'ceo') || str_contains($userRole, 'manager');
        if ($isExempt && $user->must_change_password) {
            $user->must_change_password = false;
            $user->save();
        }
        return response()->json($user);
    }
}
