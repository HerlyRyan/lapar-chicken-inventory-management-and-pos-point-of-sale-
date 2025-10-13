<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'code' => 'view_dashboard', 'module' => 'Dashboard', 'description' => 'Melihat dashboard'],
            
            // Master Data - Account Types
            ['name' => 'View Account Types', 'code' => 'view_account_types', 'module' => 'Master Data', 'description' => 'Melihat jenis akun'],
            ['name' => 'Create Account Types', 'code' => 'create_account_types', 'module' => 'Master Data', 'description' => 'Membuat jenis akun'],
            ['name' => 'Edit Account Types', 'code' => 'edit_account_types', 'module' => 'Master Data', 'description' => 'Mengedit jenis akun'],
            ['name' => 'Delete Account Types', 'code' => 'delete_account_types', 'module' => 'Master Data', 'description' => 'Menghapus jenis akun'],
            
            // Master Data - Branches
            ['name' => 'View Branches', 'code' => 'view_branches', 'module' => 'Master Data', 'description' => 'Melihat cabang'],
            ['name' => 'Create Branches', 'code' => 'create_branches', 'module' => 'Master Data', 'description' => 'Membuat cabang'],
            ['name' => 'Edit Branches', 'code' => 'edit_branches', 'module' => 'Master Data', 'description' => 'Mengedit cabang'],
            ['name' => 'Delete Branches', 'code' => 'delete_branches', 'module' => 'Master Data', 'description' => 'Menghapus cabang'],
            
            // Master Data - Products
            ['name' => 'View Products', 'code' => 'view_products', 'module' => 'Master Data', 'description' => 'Melihat produk'],
            ['name' => 'Create Products', 'code' => 'create_products', 'module' => 'Master Data', 'description' => 'Membuat produk'],
            ['name' => 'Edit Products', 'code' => 'edit_products', 'module' => 'Master Data', 'description' => 'Mengedit produk'],
            ['name' => 'Delete Products', 'code' => 'delete_products', 'module' => 'Master Data', 'description' => 'Menghapus produk'],
            
            // User Management
            ['name' => 'View Users', 'code' => 'view_users', 'module' => 'User Management', 'description' => 'Melihat user'],
            ['name' => 'Create Users', 'code' => 'create_users', 'module' => 'User Management', 'description' => 'Membuat user'],
            ['name' => 'Edit Users', 'code' => 'edit_users', 'module' => 'User Management', 'description' => 'Mengedit user'],
            ['name' => 'Delete Users', 'code' => 'delete_users', 'module' => 'User Management', 'description' => 'Menghapus user'],
            ['name' => 'Manage User Roles', 'code' => 'manage_user_roles', 'module' => 'User Management', 'description' => 'Mengelola role user'],
            
            // Role & Permission Management
            ['name' => 'View Roles', 'code' => 'view_roles', 'module' => 'User Management', 'description' => 'Melihat role'],
            ['name' => 'Create Roles', 'code' => 'create_roles', 'module' => 'User Management', 'description' => 'Membuat role'],
            ['name' => 'Edit Roles', 'code' => 'edit_roles', 'module' => 'User Management', 'description' => 'Mengedit role'],
            ['name' => 'Delete Roles', 'code' => 'delete_roles', 'module' => 'User Management', 'description' => 'Menghapus role'],
            ['name' => 'View Permissions', 'code' => 'view_permissions', 'module' => 'User Management', 'description' => 'Melihat permission'],
            ['name' => 'Create Permissions', 'code' => 'create_permissions', 'module' => 'User Management', 'description' => 'Membuat permission'],
            ['name' => 'Edit Permissions', 'code' => 'edit_permissions', 'module' => 'User Management', 'description' => 'Mengedit permission'],
            ['name' => 'Delete Permissions', 'code' => 'delete_permissions', 'module' => 'User Management', 'description' => 'Menghapus permission'],
            
            // Transactions
            ['name' => 'View Sales', 'code' => 'view_sales', 'module' => 'Transaksi', 'description' => 'Melihat penjualan'],
            ['name' => 'Create Sales', 'code' => 'create_sales', 'module' => 'Transaksi', 'description' => 'Membuat penjualan'],
            ['name' => 'Edit Sales', 'code' => 'edit_sales', 'module' => 'Transaksi', 'description' => 'Mengedit penjualan'],
            ['name' => 'Delete Sales', 'code' => 'delete_sales', 'module' => 'Transaksi', 'description' => 'Menghapus penjualan'],
            
            ['name' => 'View Production', 'code' => 'view_production', 'module' => 'Transaksi', 'description' => 'Melihat produksi'],
            ['name' => 'Create Production', 'code' => 'create_production', 'module' => 'Transaksi', 'description' => 'Membuat produksi'],
            ['name' => 'Edit Production', 'code' => 'edit_production', 'module' => 'Transaksi', 'description' => 'Mengedit produksi'],
            ['name' => 'Delete Production', 'code' => 'delete_production', 'module' => 'Transaksi', 'description' => 'Menghapus produksi'],
            
            // Reports
            ['name' => 'View Reports', 'code' => 'view_reports', 'module' => 'Laporan', 'description' => 'Melihat laporan'],
            ['name' => 'Export Reports', 'code' => 'export_reports', 'module' => 'Laporan', 'description' => 'Export laporan'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['code' => $permission['code']],
                $permission
            );
        }

        // Create Roles
        $roles = [
            [
                'name' => 'Super Admin',
                'code' => 'super_admin',
                'description' => 'Akses penuh ke seluruh sistem',
                'is_active' => true,
            ],
            [
                'name' => 'Admin',
                'code' => 'admin',
                'description' => 'Akses admin untuk mengelola data dan transaksi',
                'is_active' => true,
            ],
            [
                'name' => 'Manager Cabang',
                'code' => 'branch_manager',
                'description' => 'Mengelola operasional cabang',
                'is_active' => true,
            ],
            [
                'name' => 'Kasir',
                'code' => 'cashier',
                'description' => 'Melakukan transaksi penjualan',
                'is_active' => true,
            ],
            [
                'name' => 'Staff Produksi',
                'code' => 'production_staff',
                'description' => 'Mengelola produksi dan stok',
                'is_active' => true,
            ],
            [
                'name' => 'Viewer',
                'code' => 'viewer',
                'description' => 'Hanya dapat melihat data',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['code' => $roleData['code']],
                $roleData
            );

            // Assign permissions based on role
            $this->assignPermissionsToRole($role);
        }
    }

    private function assignPermissionsToRole($role)
    {
        $allPermissions = Permission::all();
        
        switch ($role->code) {
            case 'super_admin':
                // Super admin gets all permissions
                $role->permissions()->sync($allPermissions->pluck('id'));
                break;
                
            case 'admin':
                // Admin gets most permissions except some critical ones
                $adminPermissions = $allPermissions->whereNotIn('code', [
                    'delete_users',
                    'delete_roles',
                    'delete_permissions'
                ]);
                $role->permissions()->sync($adminPermissions->pluck('id'));
                break;
                
            case 'branch_manager':
                // Branch manager gets branch-related permissions
                $managerPermissions = $allPermissions->whereIn('code', [
                    'view_dashboard',
                    'view_branches', 'edit_branches',
                    'view_products', 'create_products', 'edit_products',
                    'view_sales', 'create_sales', 'edit_sales',
                    'view_production', 'create_production', 'edit_production',
                    'view_reports', 'export_reports',
                    'view_users', 'create_users', 'edit_users'
                ]);
                $role->permissions()->sync($managerPermissions->pluck('id'));
                break;
                
            case 'cashier':
                // Cashier only gets sales-related permissions
                $cashierPermissions = $allPermissions->whereIn('code', [
                    'view_dashboard',
                    'view_sales', 'create_sales', 'edit_sales',
                    'view_products',
                    'view_reports'
                ]);
                $role->permissions()->sync($cashierPermissions->pluck('id'));
                break;
                
            case 'production_staff':
                // Production staff gets production-related permissions
                $productionPermissions = $allPermissions->whereIn('code', [
                    'view_dashboard',
                    'view_products', 'edit_products',
                    'view_production', 'create_production', 'edit_production',
                    'view_reports'
                ]);
                $role->permissions()->sync($productionPermissions->pluck('id'));
                break;
                
            case 'viewer':
                // Viewer only gets view permissions
                $viewerPermissions = $allPermissions->filter(function($permission) {
                    return str_starts_with($permission->code, 'view_');
                });
                $role->permissions()->sync($viewerPermissions->pluck('id'));
                break;
        }
    }
}
