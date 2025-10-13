<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Permission;
use App\Models\RawMaterial;
use App\Models\SemiFinishedProduct;
use App\Models\FinishedProduct;
use App\Models\BranchStock;
use Illuminate\Support\Facades\Hash;

class NewInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Units
        $units = [
            ['name' => 'Kilogram', 'symbol' => 'kg', 'description' => 'Unit berat dalam kilogram'],
            ['name' => 'Gram', 'symbol' => 'g', 'description' => 'Unit berat dalam gram'],
            ['name' => 'Liter', 'symbol' => 'L', 'description' => 'Unit volume dalam liter'],
            ['name' => 'Mililiter', 'symbol' => 'ml', 'description' => 'Unit volume dalam mililiter'],
            ['name' => 'Pieces', 'symbol' => 'pcs', 'description' => 'Unit satuan dalam pieces'],
            ['name' => 'Pack', 'symbol' => 'pack', 'description' => 'Unit kemasan dalam pack'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }

        // 2. Create Categories
        $categories = [
            ['name' => 'Bahan Baku Utama', 'slug' => 'bahan-baku-utama', 'description' => 'Bahan baku utama untuk produksi'],
            ['name' => 'Bumbu dan Rempah', 'slug' => 'bumbu-rempah', 'description' => 'Bumbu dan rempah-rempah'],
            ['name' => 'Kemasan', 'slug' => 'kemasan', 'description' => 'Bahan kemasan produk'],
            ['name' => 'Produk Setengah Jadi', 'slug' => 'produk-setengah-jadi', 'description' => 'Produk dalam tahap setengah jadi'],
            ['name' => 'Produk Jadi', 'slug' => 'produk-jadi', 'description' => 'Produk siap jual'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // 3. Create Branches
        $branches = [
            ['name' => 'Cabang Pusat', 'code' => 'CP001', 'address' => 'Jl. Raya Utama No. 123', 'phone' => '021-12345678', 'email' => 'pusat@laparchicken.com'],
            ['name' => 'Cabang Timur', 'code' => 'CT001', 'address' => 'Jl. Timur Raya No. 456', 'phone' => '021-87654321', 'email' => 'timur@laparchicken.com'],
            ['name' => 'Cabang Barat', 'code' => 'CB001', 'address' => 'Jl. Barat Indah No. 789', 'phone' => '021-11223344', 'email' => 'barat@laparchicken.com'],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }

        // 4. Create Roles
        $roles = [
            ['name' => 'Super Admin', 'code' => 'SUPER_ADMIN', 'description' => 'Administrator dengan akses penuh'],
            ['name' => 'Branch Manager', 'code' => 'BRANCH_MANAGER', 'description' => 'Manager cabang'],
            ['name' => 'Staff', 'code' => 'STAFF', 'description' => 'Staff operasional'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // 5. Create Permissions
        $permissions = [
            ['name' => 'Manage Users', 'code' => 'MANAGE_USERS', 'description' => 'Can manage users'],
            ['name' => 'Manage Inventory', 'code' => 'MANAGE_INVENTORY', 'description' => 'Can manage inventory'],
            ['name' => 'Manage Sales', 'code' => 'MANAGE_SALES', 'description' => 'Can manage sales'],
            ['name' => 'View Reports', 'code' => 'VIEW_REPORTS', 'description' => 'Can view reports'],
            ['name' => 'Manage Branches', 'code' => 'MANAGE_BRANCHES', 'description' => 'Can manage branches'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // 6. Create Users
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@laparchicken.com',
                'password' => Hash::make('password'),
                'branch_id' => 1,
            ],
            [
                'name' => 'Manager Timur',
                'email' => 'manager.timur@laparchicken.com',
                'password' => Hash::make('password'),
                'branch_id' => 2,
            ],
            [
                'name' => 'Staff Barat',
                'email' => 'staff.barat@laparchicken.com',
                'password' => Hash::make('password'),
                'branch_id' => 3,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
        }

        // 7. Assign roles to users
        UserRole::create(['user_id' => 1, 'role_id' => 1]); // Super Admin
        UserRole::create(['user_id' => 2, 'role_id' => 2]); // Manager
        UserRole::create(['user_id' => 3, 'role_id' => 3]); // Staff

        // 8. Create Raw Materials
        $rawMaterials = [
            [
                'name' => 'Ayam Segar',
                'code' => 'RM001',
                'description' => 'Ayam segar untuk bahan baku utama',
                'category_id' => 1,
                'unit_id' => 1, // kg
                'purchase_price' => 25000,
                'selling_price' => 30000,
                'minimum_stock' => 50,
                'current_stock' => 100,
                'is_centralized' => true,
                'managing_branch_id' => 1,
            ],
            [
                'name' => 'Tepung Terigu',
                'code' => 'RM002',
                'description' => 'Tepung terigu untuk coating',
                'category_id' => 1,
                'unit_id' => 1, // kg
                'purchase_price' => 12000,
                'selling_price' => 15000,
                'minimum_stock' => 25,
                'current_stock' => 50,
                'is_centralized' => true,
                'managing_branch_id' => 1,
            ],
            [
                'name' => 'Bumbu Racik',
                'code' => 'RM003',
                'description' => 'Bumbu racik khusus ayam goreng',
                'category_id' => 2,
                'unit_id' => 2, // g
                'purchase_price' => 50000,
                'selling_price' => 60000,
                'minimum_stock' => 5000,
                'current_stock' => 10000,
                'is_centralized' => true,
                'managing_branch_id' => 1,
            ],
        ];

        foreach ($rawMaterials as $material) {
            RawMaterial::create($material);
        }

        // 9. Create Semi Finished Products
        $semiFinishedProducts = [
            [
                'name' => 'Ayam Marinasi',
                'code' => 'SF001',
                'description' => 'Ayam yang sudah dibumbui dan siap digoreng',
                'category_id' => 4,
                'unit_id' => 1, // kg
                'production_cost' => 35000,
                'selling_price' => 40000,
                'minimum_stock' => 20,
                'current_stock' => 30,
                'is_centralized' => false,
                'managing_branch_id' => 1,
            ],
        ];

        foreach ($semiFinishedProducts as $product) {
            SemiFinishedProduct::create($product);
        }

        // 10. Create Finished Products
        $finishedProducts = [
            [
                'name' => 'Ayam Goreng Original',
                'code' => 'FP001',
                'description' => 'Ayam goreng dengan resep original',
                'category_id' => 5,
                'unit_id' => 5, // pcs
                'production_cost' => 15000,
                'selling_price' => 25000,
                'minimum_stock' => 10,
                'current_stock' => 25,
                'is_centralized' => false,
                'managing_branch_id' => 1,
            ],
            [
                'name' => 'Ayam Goreng Pedas',
                'code' => 'FP002',
                'description' => 'Ayam goreng dengan bumbu pedas',
                'category_id' => 5,
                'unit_id' => 5, // pcs
                'production_cost' => 16000,
                'selling_price' => 27000,
                'minimum_stock' => 10,
                'current_stock' => 20,
                'is_centralized' => false,
                'managing_branch_id' => 1,
            ],
        ];

        foreach ($finishedProducts as $product) {
            FinishedProduct::create($product);
        }

        // 11. Create Branch Stocks for all items in all branches
        $branches = Branch::all();
        $rawMaterials = RawMaterial::all();
        $semiFinishedProducts = SemiFinishedProduct::all();
        $finishedProducts = FinishedProduct::all();

        foreach ($branches as $branch) {
            // Raw Materials Stock
            foreach ($rawMaterials as $material) {
                BranchStock::create([
                    'branch_id' => $branch->id,
                    'stockable_type' => RawMaterial::class,
                    'stockable_id' => $material->id,
                    'quantity' => rand(10, 100),
                    'minimum_stock' => $material->minimum_stock,
                    'average_cost' => $material->purchase_price,
                ]);
            }

            // Semi Finished Products Stock
            foreach ($semiFinishedProducts as $product) {
                BranchStock::create([
                    'branch_id' => $branch->id,
                    'stockable_type' => SemiFinishedProduct::class,
                    'stockable_id' => $product->id,
                    'quantity' => rand(5, 30),
                    'minimum_stock' => $product->minimum_stock,
                    'average_cost' => $product->production_cost,
                ]);
            }

            // Finished Products Stock
            foreach ($finishedProducts as $product) {
                BranchStock::create([
                    'branch_id' => $branch->id,
                    'stockable_type' => FinishedProduct::class,
                    'stockable_id' => $product->id,
                    'quantity' => rand(5, 50),
                    'minimum_stock' => $product->minimum_stock,
                    'average_cost' => $product->production_cost,
                ]);
            }
        }
    }
}
