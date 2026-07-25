<?php


use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\MaintenanceReportController;
use App\Http\Controllers\Api\QuotationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\RolePermissionController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/brands', [BrandController::class, 'index']);
Route::get('/areas', [AreaController::class, 'index']);
Route::get('/clients', [ClientController::class, 'index']);
Route::get('/clients/export', [ClientController::class, 'export']);
Route::post('/clients/import', [ClientController::class, 'import']);
Route::post('/clients', [ClientController::class, 'store']);
Route::post('/clients/{client}', [ClientController::class, 'update']);
Route::put('/clients/{client}', [ClientController::class, 'update']);
Route::delete('/clients/{client}', [ClientController::class, 'destroy']);
Route::get('/quotations', [QuotationController::class, 'index']);
Route::get('/invoices', [InvoiceController::class, 'index']);

// Public routes for front store
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::post('/reviews', [ReviewController::class, 'store']);
Route::post('/contacts', [ContactMessageController::class, 'store']);
Route::get('/settings', [SettingController::class, 'index']);
Route::get('/app-download', [\App\Http\Controllers\Api\AppDownloadController::class, 'getAppInfo']);
Route::get('/app/download/apk', [\App\Http\Controllers\Api\AppDownloadController::class, 'downloadApk']);
Route::get('/seed-database', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
        return response()->json(['status' => true, 'message' => 'Database fully refreshed and populated with all products, tasks, clients, and categories!']);
    } catch (\Throwable $e) {
        try {
            (new \Database\Seeders\DatabaseSeeder())->run();
            return response()->json(['status' => true, 'message' => 'Database populated with all products, tasks, clients, and categories!']);
        } catch (\Throwable $t) {
            return response()->json(['status' => false, 'error' => $t->getMessage()]);
        }
    }
});

Route::middleware(['auth:sanctum', 'api.activity'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/users', [RolePermissionController::class, 'getUsers']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Admin, CEO & Manager High-Privilege Endpoints
    Route::middleware('role:Admin,CEO,Manager')->group(function () {
        // Employees Management
        Route::get('/employees', [EmployeeController::class, 'index']);
        Route::post('/employees', [EmployeeController::class, 'store']);
        Route::put('/employees/{employee}', [EmployeeController::class, 'update']);
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy']);
        Route::get('/employees/{employee}/permissions', [EmployeeController::class, 'getPermissions']);
        Route::post('/employees/{employee}/permissions', [EmployeeController::class, 'syncPermissions']);

        // Roles and Permissions Management
        Route::get('/roles', [RolePermissionController::class, 'getRoles']);
        Route::post('/roles', [RolePermissionController::class, 'createRole']);
        Route::post('/users/assign-role', [RolePermissionController::class, 'assignRole']);
        Route::post('/roles/toggle-permission', [RolePermissionController::class, 'togglePermission']);

        // System Settings & Activity Logs
        Route::post('/settings', [SettingController::class, 'update']);
        Route::get('/activity-logs', [ActivityLogController::class, 'index']);
        Route::post('/activity-logs', [ActivityLogController::class, 'store']);
    });

    // Brands
    Route::get('/brands/export', [BrandController::class, 'export']);
    Route::post('/brands/import', [BrandController::class, 'import']);
    Route::post('/brands', [BrandController::class, 'store']);
    Route::post('/brands/{brand}', [BrandController::class, 'update']);
    Route::put('/brands/{brand}', [BrandController::class, 'update']);
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy']);

    // Areas
    Route::get('/areas/export', [AreaController::class, 'export']);
    Route::post('/areas/import', [AreaController::class, 'import']);
    Route::post('/areas', [AreaController::class, 'store']);
    Route::post('/areas/{area}', [AreaController::class, 'update']);
    Route::put('/areas/{area}', [AreaController::class, 'update']);
    Route::delete('/areas/{area}', [AreaController::class, 'destroy']);



    // Quotations
    Route::post('/quotations', [QuotationController::class, 'store']);
    Route::post('/quotations/{quotation}', [QuotationController::class, 'update']);
    Route::put('/quotations/{quotation}', [QuotationController::class, 'update']);
    Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy']);

    // Invoices
    Route::post('/invoices', [InvoiceController::class, 'store']);
    Route::post('/invoices/{invoice}', [InvoiceController::class, 'update']);
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update']);
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy']);

    // Invoice Requests Workflow
    Route::get('/invoice-requests', [\App\Http\Controllers\Api\InvoiceRequestController::class, 'index']);
    Route::post('/invoice-requests', [\App\Http\Controllers\Api\InvoiceRequestController::class, 'store']);
    Route::put('/invoice-requests/{id}/issue', [\App\Http\Controllers\Api\InvoiceRequestController::class, 'issueInvoices']);
    Route::put('/invoice-requests/{id}/client-response', [\App\Http\Controllers\Api\InvoiceRequestController::class, 'respondByClient']);
    Route::put('/invoice-requests/{id}/collect', [\App\Http\Controllers\Api\InvoiceRequestController::class, 'markCollected']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);

    // Maintenance Reports
    Route::get('/maintenance-reports', [MaintenanceReportController::class, 'index']);
    Route::post('/maintenance-reports', [MaintenanceReportController::class, 'store']);

    // Conversations
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
    Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'messages']);
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage']);
    Route::post('/conversations/{conversation}/read', [ConversationController::class, 'markAsRead']);
    Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy']);

    // Category, Product, Review, Settings and Contact endpoints
    Route::get('/categories/export', [CategoryController::class, 'export']);
    Route::post('/categories/import', [CategoryController::class, 'import']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::post('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    Route::get('/products/export', [ProductController::class, 'export']);
    Route::post('/products/import', [ProductController::class, 'import']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    Route::get('/contacts', [ContactMessageController::class, 'index']);
    Route::patch('/contacts/{contact}/read', [ContactMessageController::class, 'markAsRead']);
    Route::delete('/contacts/{contact}', [ContactMessageController::class, 'destroy']);

    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Client Contacts Management
    Route::get('/clients/{client}/contacts', [\App\Http\Controllers\Api\ClientContactController::class, 'index']);
    Route::post('/clients/{client}/contacts', [\App\Http\Controllers\Api\ClientContactController::class, 'store']);
    Route::put('/client-contacts/{contact}', [\App\Http\Controllers\Api\ClientContactController::class, 'update']);
    Route::delete('/client-contacts/{contact}', [\App\Http\Controllers\Api\ClientContactController::class, 'destroy']);

    // Tasks Management
    Route::apiResource('/tasks', TaskController::class);
    Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::post('/tasks/{task}/outcome', [TaskController::class, 'submitVisitOutcome']);
    Route::post('/tasks/{task}/accountant-action', [TaskController::class, 'processAccountantAction']);
    Route::post('/tasks/{task}/updates', [TaskController::class, 'addUpdate']);
    Route::post('/tasks/{task}/generate-otp', [TaskController::class, 'generateOtp']);
    Route::post('/tasks/{task}/verify-otp', [TaskController::class, 'verifyOtp']);
    Route::get('/devices', [DeviceController::class, 'index']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::get('/brands/{brand}', [BrandController::class, 'show']);
Route::get('/areas/{area}', [AreaController::class, 'show']);
Route::get('/clients/{client}', [ClientController::class, 'show']);
Route::get('/quotations/{quotation}', [QuotationController::class, 'show']);
