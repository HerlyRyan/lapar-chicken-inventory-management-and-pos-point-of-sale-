<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Insert Roles
        $roles = [
            [
                'name' => 'Super Admin',
                'code' => 'super_admin',
                'description' => 'Full system access with all permissions',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Branch Manager',
                'code' => 'branch_manager',
                'description' => 'Manage specific branch operations',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Inventory Manager',
                'code' => 'inventory_manager',
                'description' => 'Manage inventory, stock, and procurement',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sales Staff',
                'code' => 'sales_staff',
                'description' => 'Handle sales transactions and customer service',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Production Staff',
                'code' => 'production_staff',
                'description' => 'Handle production and conversion processes',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Warehouse Staff',
                'code' => 'warehouse_staff',
                'description' => 'Handle stock movements and distributions',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('roles')->insert($roles);

        // Insert Permissions
        $permissions = [
            // User Management
            ['name' => 'View Users', 'code' => 'users.view', 'module' => 'users', 'description' => 'View user list and details'],
            ['name' => 'Create Users', 'code' => 'users.create', 'module' => 'users', 'description' => 'Create new users'],
            ['name' => 'Update Users', 'code' => 'users.update', 'module' => 'users', 'description' => 'Update user information'],
            ['name' => 'Delete Users', 'code' => 'users.delete', 'module' => 'users', 'description' => 'Delete users'],
            ['name' => 'Assign Roles', 'code' => 'users.assign_roles', 'module' => 'users', 'description' => 'Assign roles to users'],

            // Branch Management
            ['name' => 'View Branches', 'code' => 'branches.view', 'module' => 'branches', 'description' => 'View branch list and details'],
            ['name' => 'Create Branches', 'code' => 'branches.create', 'module' => 'branches', 'description' => 'Create new branches'],
            ['name' => 'Update Branches', 'code' => 'branches.update', 'module' => 'branches', 'description' => 'Update branch information'],
            ['name' => 'Delete Branches', 'code' => 'branches.delete', 'module' => 'branches', 'description' => 'Delete branches'],

            // Inventory Management
            ['name' => 'View Materials', 'code' => 'materials.view', 'module' => 'inventory', 'description' => 'View materials list and details'],
            ['name' => 'Create Materials', 'code' => 'materials.create', 'module' => 'inventory', 'description' => 'Create new materials'],
            ['name' => 'Update Materials', 'code' => 'materials.update', 'module' => 'inventory', 'description' => 'Update material information'],
            ['name' => 'Delete Materials', 'code' => 'materials.delete', 'module' => 'inventory', 'description' => 'Delete materials'],
            
            ['name' => 'View Finished Products', 'code' => 'finished_products.view', 'module' => 'inventory', 'description' => 'View finished products'],
            ['name' => 'Create Finished Products', 'code' => 'finished_products.create', 'module' => 'inventory', 'description' => 'Create new finished products'],
            ['name' => 'Update Finished Products', 'code' => 'finished_products.update', 'module' => 'inventory', 'description' => 'Update finished products'],
            ['name' => 'Delete Finished Products', 'code' => 'finished_products.delete', 'module' => 'inventory', 'description' => 'Delete finished products'],

            // Stock Management
            ['name' => 'View Stock', 'code' => 'stock.view', 'module' => 'stock', 'description' => 'View stock levels and movements'],
            ['name' => 'Adjust Stock', 'code' => 'stock.adjust', 'module' => 'stock', 'description' => 'Make stock adjustments'],
            ['name' => 'Transfer Stock', 'code' => 'stock.transfer', 'module' => 'stock', 'description' => 'Transfer stock between branches'],

            // Sales Management
            ['name' => 'View Sales', 'code' => 'sales.view', 'module' => 'sales', 'description' => 'View sales transactions'],
            ['name' => 'Create Sales', 'code' => 'sales.create', 'module' => 'sales', 'description' => 'Process sales transactions'],
            ['name' => 'Update Sales', 'code' => 'sales.update', 'module' => 'sales', 'description' => 'Update sales transactions'],
            ['name' => 'Cancel Sales', 'code' => 'sales.cancel', 'module' => 'sales', 'description' => 'Cancel sales transactions'],
            ['name' => 'Refund Sales', 'code' => 'sales.refund', 'module' => 'sales', 'description' => 'Process sales refunds'],

            // Purchase Management
            ['name' => 'View Purchase Orders', 'code' => 'purchase_orders.view', 'module' => 'purchasing', 'description' => 'View purchase orders'],
            ['name' => 'Create Purchase Orders', 'code' => 'purchase_orders.create', 'module' => 'purchasing', 'description' => 'Create purchase orders'],
            ['name' => 'Update Purchase Orders', 'code' => 'purchase_orders.update', 'module' => 'purchasing', 'description' => 'Update purchase orders'],
            ['name' => 'Approve Purchase Orders', 'code' => 'purchase_orders.approve', 'module' => 'purchasing', 'description' => 'Approve purchase orders'],
            ['name' => 'Receive Purchase Orders', 'code' => 'purchase_orders.receive', 'module' => 'purchasing', 'description' => 'Receive purchase orders'],

            // Production Management
            ['name' => 'View Production', 'code' => 'production.view', 'module' => 'production', 'description' => 'View production requests and recipes'],
            ['name' => 'Create Production Requests', 'code' => 'production.create_requests', 'module' => 'production', 'description' => 'Create production requests'],
            ['name' => 'Process Production', 'code' => 'production.process', 'module' => 'production', 'description' => 'Process production requests'],
            ['name' => 'Manage Recipes', 'code' => 'production.manage_recipes', 'module' => 'production', 'description' => 'Create and update conversion recipes'],

            // Distribution Management
            ['name' => 'View Distributions', 'code' => 'distributions.view', 'module' => 'distribution', 'description' => 'View distribution requests'],
            ['name' => 'Create Distributions', 'code' => 'distributions.create', 'module' => 'distribution', 'description' => 'Create distribution requests'],
            ['name' => 'Approve Distributions', 'code' => 'distributions.approve', 'module' => 'distribution', 'description' => 'Approve distribution requests'],
            ['name' => 'Send Distributions', 'code' => 'distributions.send', 'module' => 'distribution', 'description' => 'Send distributions'],
            ['name' => 'Receive Distributions', 'code' => 'distributions.receive', 'module' => 'distribution', 'description' => 'Receive distributions'],

            // Reports
            ['name' => 'View Reports', 'code' => 'reports.view', 'module' => 'reports', 'description' => 'View all reports'],
            ['name' => 'Export Reports', 'code' => 'reports.export', 'module' => 'reports', 'description' => 'Export reports to various formats'],

            // System Configuration
            ['name' => 'System Settings', 'code' => 'system.settings', 'module' => 'system', 'description' => 'Manage system settings'],
            ['name' => 'Manage Roles', 'code' => 'system.manage_roles', 'module' => 'system', 'description' => 'Create and manage roles'],
            ['name' => 'Manage Permissions', 'code' => 'system.manage_permissions', 'module' => 'system', 'description' => 'Assign permissions to roles'],
        ];

        $permissionData = [];
        foreach ($permissions as $permission) {
            $permissionData[] = array_merge($permission, [
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('permissions')->insert($permissionData);

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    private function assignPermissionsToRoles()
    {
        $now = Carbon::now();

        // Get role and permission IDs
        $roles = DB::table('roles')->get()->keyBy('code');
        $permissions = DB::table('permissions')->get()->keyBy('code');

        $rolePermissions = [];

        // Super Admin - All permissions
        foreach ($permissions as $permission) {
            $rolePermissions[] = [
                'role_id' => $roles['super_admin']->id,
                'permission_id' => $permission->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Branch Manager - Most permissions for branch operations
        $branchManagerPermissions = [
            'users.view', 'users.create', 'users.update', 'users.assign_roles',
            'branches.view', 'branches.update',
            'materials.view', 'materials.create', 'materials.update',
            'finished_products.view', 'finished_products.create', 'finished_products.update',
            'stock.view', 'stock.adjust', 'stock.transfer',
            'sales.view', 'sales.create', 'sales.update', 'sales.cancel', 'sales.refund',
            'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.update', 'purchase_orders.approve', 'purchase_orders.receive',
            'production.view', 'production.create_requests', 'production.process',
            'distributions.view', 'distributions.create', 'distributions.approve', 'distributions.send', 'distributions.receive',
            'reports.view', 'reports.export',
        ];

        foreach ($branchManagerPermissions as $permCode) {
            if (isset($permissions[$permCode])) {
                $rolePermissions[] = [
                    'role_id' => $roles['branch_manager']->id,
                    'permission_id' => $permissions[$permCode]->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Inventory Manager
        $inventoryManagerPermissions = [
            'materials.view', 'materials.create', 'materials.update',
            'finished_products.view', 'finished_products.create', 'finished_products.update',
            'stock.view', 'stock.adjust', 'stock.transfer',
            'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.update', 'purchase_orders.receive',
            'distributions.view', 'distributions.create', 'distributions.send', 'distributions.receive',
            'reports.view',
        ];

        foreach ($inventoryManagerPermissions as $permCode) {
            if (isset($permissions[$permCode])) {
                $rolePermissions[] = [
                    'role_id' => $roles['inventory_manager']->id,
                    'permission_id' => $permissions[$permCode]->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Sales Staff
        $salesStaffPermissions = [
            'finished_products.view',
            'stock.view',
            'sales.view', 'sales.create', 'sales.update',
        ];

        foreach ($salesStaffPermissions as $permCode) {
            if (isset($permissions[$permCode])) {
                $rolePermissions[] = [
                    'role_id' => $roles['sales_staff']->id,
                    'permission_id' => $permissions[$permCode]->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Production Staff
        $productionStaffPermissions = [
            'materials.view',
            'finished_products.view',
            'stock.view',
            'production.view', 'production.create_requests', 'production.process',
        ];

        foreach ($productionStaffPermissions as $permCode) {
            if (isset($permissions[$permCode])) {
                $rolePermissions[] = [
                    'role_id' => $roles['production_staff']->id,
                    'permission_id' => $permissions[$permCode]->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Warehouse Staff
        $warehouseStaffPermissions = [
            'materials.view',
            'finished_products.view',
            'stock.view', 'stock.adjust', 'stock.transfer',
            'purchase_orders.view', 'purchase_orders.receive',
            'distributions.view', 'distributions.send', 'distributions.receive',
        ];

        foreach ($warehouseStaffPermissions as $permCode) {
            if (isset($permissions[$permCode])) {
                $rolePermissions[] = [
                    'role_id' => $roles['warehouse_staff']->id,
                    'permission_id' => $permissions[$permCode]->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('role_permissions')->insert($rolePermissions);
    }
}
