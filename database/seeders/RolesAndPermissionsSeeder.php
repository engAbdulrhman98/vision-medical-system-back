<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all permissions grouped by component/feature
        $permissions = [
            // Tasks
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'assign tasks',

            // Maintenance Schedules
            'view maintenance_schedules',
            'create maintenance_schedules',
            'edit maintenance_schedules',
            'delete maintenance_schedules',

            // Devices
            'view devices',
            'create devices',
            'edit devices',
            'delete devices',

            // Maintenance Reports
            'view maintenance_reports',
            'create maintenance_reports',
            'edit maintenance_reports',
            'delete maintenance_reports',

            // Stock Items (Inventory)
            'view stock_items',
            'create stock_items',
            'edit stock_items',
            'delete stock_items',

            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',

            // Clients & Field Operations
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            'view external_tasks',

            // Quotations
            'view quotations',
            'create quotations',
            'create_quotation',
            'edit quotations',
            'delete quotations',

            // Invoices & Financials (Accountant specifics)
            'view invoices',
            'view invoice_requests',
            'view financials',
            'manage financials',
            'view_financial_reports',

            // Users & System Settings (Super Admin / CEO only)
            'manage users',
            'manage settings',
        ];

        // Create all permissions in the database
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Admin / Super Admin
        $adminRole = Role::findOrCreate('Admin');
        $adminRole->givePermissionTo(Permission::all());

        // CEO / Super Admin
        $ceoRole = Role::findOrCreate('CEO');
        $ceoRole->givePermissionTo(Permission::all());

        // Operations Manager
        $operationsManagerRole = Role::findOrCreate('Operations Manager');
        $operationsManagerRole->givePermissionTo([
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'assign tasks',
            'view maintenance_schedules',
            'create maintenance_schedules',
            'edit maintenance_schedules',
            'delete maintenance_schedules',
            'view devices',
            'create devices',
            'edit devices',
            'delete devices',
        ]);

        // Service Engineer outdoor (مهندس صيانة خارجي/ميداني)
        $engineerOutdoorRole = Role::findOrCreate('Service Engineer outdoor');
        $engineerOutdoorRole->givePermissionTo([
            'view clients',
            'create clients',
            'edit clients',
            'view tasks',
            'view external_tasks',
            'view devices',
            'create_quotation',
            'view quotations',
            'view maintenance_reports',
            'create maintenance_reports',
            'edit maintenance_reports',
        ]);

        // Service Engineer indoor (مهندس صيانة داخلي/ورشة)
        $engineerIndoorRole = Role::findOrCreate('Service Engineer indoor');
        $engineerIndoorRole->givePermissionTo([
            'view tasks',
            'view devices',
            'view stock_items',
            'edit stock_items',
            'view products',
            'view maintenance_reports',
            'create maintenance_reports',
        ]);

        // Accountant (محاسب)
        $accountantRole = Role::findOrCreate('Accountant');
        $accountantRole->givePermissionTo([
            'view clients',
            'view quotations',
            'create quotations',
            'edit quotations',
            'delete quotations',
            'view invoices',
            'view invoice_requests',
            'view financials',
            'manage financials',
            'view_financial_reports',
        ]);

        // Collector (المحصل)
        $collectorRole = Role::findOrCreate('Collector');
        $collectorRole->givePermissionTo([
            'view clients',
            'view invoices',
            'view invoice_requests',
            'view financials',
        ]);

        // Sale (مسؤول مبيعات)
        $saleRole = Role::findOrCreate('Sale');
        $saleRole->givePermissionTo([
            'view clients',
            'create clients',
            'edit clients',
            'view tasks',
            'view stock_items',
            'view products',
            'create products',
            'edit products',
            'view quotations',
            'create_quotation',
            'create quotations',
        ]);
    }
}
