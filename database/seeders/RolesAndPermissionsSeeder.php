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

            // Quotations
            'view quotations',
            'create quotations',
            'edit quotations',
            'delete quotations',

            // Financials (Accountant specifics)
            'view financials',
            'manage financials',

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

        // Service Engineer outdoor
        $engineerOutdoorRole = Role::findOrCreate('Service Engineer outdoor');
        $engineerOutdoorRole->givePermissionTo([
            'view tasks',
            'view devices',
            'view maintenance_reports',
            'create maintenance_reports',
            'edit maintenance_reports',
            'delete maintenance_reports',
        ]);

        // Service Engineer indoor
        $engineerIndoorRole = Role::findOrCreate('Service Engineer indoor');
        $engineerIndoorRole->givePermissionTo([
            'view tasks',
            'view stock_items',
            'edit stock_items', // to deduct/use spare parts
            'view products',
        ]);

        // Accountant
        $accountantRole = Role::findOrCreate('Accountant');
        $accountantRole->givePermissionTo([
            'view quotations',
            'create quotations',
            'edit quotations',
            'delete quotations',
            'view products',
            'view financials',
            'manage financials',
        ]);

        // Sale
        $saleRole = Role::findOrCreate('Sale');
        $saleRole->givePermissionTo([
            'view stock_items',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view quotations',
            'create quotations',
        ]);
    }
}
