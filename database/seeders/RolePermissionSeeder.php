<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Customer Management
            ['name' => 'View Customers', 'slug' => 'customers.view', 'module' => 'customers'],
            ['name' => 'Create Customers', 'slug' => 'customers.create', 'module' => 'customers'],
            ['name' => 'Edit Customers', 'slug' => 'customers.edit', 'module' => 'customers'],
            ['name' => 'Delete Customers', 'slug' => 'customers.delete', 'module' => 'customers'],
            ['name' => 'Activate/Deactivate Customers', 'slug' => 'customers.toggle', 'module' => 'customers'],

            // Basket Management
            ['name' => 'View Baskets', 'slug' => 'baskets.view', 'module' => 'baskets'],
            ['name' => 'Create Baskets', 'slug' => 'baskets.create', 'module' => 'baskets'],
            ['name' => 'Edit Baskets', 'slug' => 'baskets.edit', 'module' => 'baskets'],
            ['name' => 'Delete Baskets', 'slug' => 'baskets.delete', 'module' => 'baskets'],
            ['name' => 'Scan Baskets', 'slug' => 'baskets.scan', 'module' => 'baskets'],

            // Batch Management
            ['name' => 'View Batches', 'slug' => 'batches.view', 'module' => 'batches'],
            ['name' => 'Create Batches', 'slug' => 'batches.create', 'module' => 'batches'],
            ['name' => 'Edit Batches', 'slug' => 'batches.edit', 'module' => 'batches'],
            ['name' => 'Delete Batches', 'slug' => 'batches.delete', 'module' => 'batches'],

            // Invoice Management
            ['name' => 'View Invoices', 'slug' => 'invoices.view', 'module' => 'invoices'],
            ['name' => 'Create Invoices', 'slug' => 'invoices.create', 'module' => 'invoices'],
            ['name' => 'Edit Invoices', 'slug' => 'invoices.edit', 'module' => 'invoices'],
            ['name' => 'Delete Invoices', 'slug' => 'invoices.delete', 'module' => 'invoices'],
            ['name' => 'Process Payments', 'slug' => 'invoices.payments', 'module' => 'invoices'],

            // Dispatch Management
            ['name' => 'View Dispatches', 'slug' => 'dispatches.view', 'module' => 'dispatches'],
            ['name' => 'Create Dispatches', 'slug' => 'dispatches.create', 'module' => 'dispatches'],
            ['name' => 'Approve Dispatches', 'slug' => 'dispatches.approve', 'module' => 'dispatches'],
            ['name' => 'Process Dispatches', 'slug' => 'dispatches.process', 'module' => 'dispatches'],

            // Storage Management
            ['name' => 'View Storage', 'slug' => 'storage.view', 'module' => 'storage'],
            ['name' => 'Manage Storage', 'slug' => 'storage.manage', 'module' => 'storage'],
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'reports'],
            ['name' => 'View Financial Reports', 'slug' => 'financial.reports', 'module' => 'reports'],

            // User Management
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'users'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'module' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'users'],
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'module' => 'users'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'users'],

            // System Administration
            ['name' => 'System Settings', 'slug' => 'system.settings', 'module' => 'system'],
            ['name' => 'View Logs', 'slug' => 'system.logs', 'module' => 'system'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Create roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Full system access with all permissions',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrative access to most system features',
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Management access to operational features',
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Basic operational access',
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Read-only access to most features',
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );

            // Assign permissions based on role
            switch ($role->slug) {
                case 'super_admin':
                    $role->syncPermissions(Permission::pluck('id')->toArray());
                    break;

                case 'admin':
                    $role->syncPermissions(Permission::whereNotIn('slug', [
                        'system.settings',
                        'system.logs'
                    ])->pluck('id')->toArray());
                    break;

                case 'manager':
                    $role->syncPermissions(Permission::whereIn('slug', [
                        'customers.view', 'customers.create', 'customers.edit',
                        'baskets.view', 'baskets.create', 'baskets.edit', 'baskets.scan',
                        'batches.view', 'batches.create', 'batches.edit',
                        'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.payments',
                        'dispatches.view', 'dispatches.create', 'dispatches.approve', 'dispatches.process',
                        'storage.view', 'storage.manage', 'reports.view', 'financial.reports',
                        'users.view'
                    ])->pluck('id')->toArray());
                    break;

                case 'staff':
                    $role->syncPermissions(Permission::whereIn('slug', [
                        'customers.view', 'customers.create', 'customers.edit',
                        'baskets.view', 'baskets.create', 'baskets.edit', 'baskets.scan',
                        'batches.view', 'batches.create', 'batches.edit',
                        'invoices.view', 'invoices.create', 'invoices.edit',
                        'dispatches.view', 'dispatches.create',
                        'storage.view', 'reports.view'
                    ])->pluck('id')->toArray());
                    break;

                case 'viewer':
                    $role->syncPermissions(Permission::whereIn('slug', [
                        'customers.view',
                        'baskets.view',
                        'batches.view',
                        'invoices.view',
                        'dispatches.view',
                        'storage.view',
                        'reports.view'
                    ])->pluck('id')->toArray());
                    break;
            }
        }
    }
}
