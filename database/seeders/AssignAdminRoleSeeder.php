<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AssignAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find Super Admin role
        $superAdminRole = Role::where('code', 'super_admin')->first();
        
        if ($superAdminRole) {
            // Find admin user (usually first user or user with admin email)
            $adminUser = User::where('email', 'admin@example.com')
                           ->orWhere('email', 'admin@lapar-chicken.com')
                           ->orWhere('id', 1)
                           ->first();
            
            if ($adminUser) {
                // Assign Super Admin role to admin user
                if (!$adminUser->roles()->where('role_id', $superAdminRole->id)->exists()) {
                    $adminUser->roles()->attach($superAdminRole->id);
                    $this->command->info("Super Admin role assigned to user: {$adminUser->email}");
                } else {
                    $this->command->info("User {$adminUser->email} already has Super Admin role");
                }
            } else {
                $this->command->error("Admin user not found. Please create an admin user first.");
            }
        } else {
            $this->command->error("Super Admin role not found. Please run RolePermissionSeeder first.");
        }
    }
}
