<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Sample Materials
        $materials = [
            [
                'name' => 'Ayam Fillet',
                'code' => 'MAT001',
                'description' => 'Fillet ayam segar untuk olahan',
                'category_id' => 1, // Asumsi kategori 1 ada
                'unit_id' => 1, // Asumsi unit 1 ada (kg)
                'purchase_price' => 45000.00,
                'selling_price' => 0, // Material tidak dijual langsung
                'minimum_stock' => 50.00,
                'current_stock' => 100.00,
                'is_active' => true,
                'is_centralized' => false,
                'managing_branch_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Tepung Bumbu',
                'code' => 'MAT002',
                'description' => 'Tepung bumbu untuk coating ayam',
                'category_id' => 1,
                'unit_id' => 1, // kg
                'purchase_price' => 25000.00,
                'selling_price' => 0,
                'minimum_stock' => 20.00,
                'current_stock' => 50.00,
                'is_active' => true,
                'is_centralized' => true,
                'managing_branch_id' => 1, // Central branch
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Minyak Goreng',
                'code' => 'MAT003',
                'description' => 'Minyak goreng untuk menggoreng',
                'category_id' => 1,
                'unit_id' => 2, // Asumsi unit 2 adalah liter
                'purchase_price' => 15000.00,
                'selling_price' => 0,
                'minimum_stock' => 30.00,
                'current_stock' => 80.00,
                'is_active' => true,
                'is_centralized' => false,
                'managing_branch_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('materials')->insert($materials);

        // Sample Semi-Finished Products
        $semiFinishedProducts = [
            [
                'name' => 'Ayam Marinasi',
                'code' => 'SFP001',
                'description' => 'Ayam fillet yang sudah dibumbui dan marinasi',
                'category_id' => 2, // Asumsi kategori 2 untuk semi-finished
                'unit_id' => 1, // kg
                'production_cost' => 55000.00,
                'selling_price' => 0, // Tidak dijual langsung
                'minimum_stock' => 20.00,
                'current_stock' => 40.00,
                'is_active' => true,
                'is_centralized' => true,
                'managing_branch_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Ayam Coating',
                'code' => 'SFP002',
                'description' => 'Ayam yang sudah di-coating dengan tepung bumbu',
                'category_id' => 2,
                'unit_id' => 1, // kg
                'production_cost' => 65000.00,
                'selling_price' => 0,
                'minimum_stock' => 15.00,
                'current_stock' => 30.00,
                'is_active' => true,
                'is_centralized' => false,
                'managing_branch_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('semi_finished_products')->insert($semiFinishedProducts);

        // Sample Finished Products
        $finishedProducts = [
            [
                'name' => 'Ayam Crispy Original',
                'code' => 'FP001',
                'description' => 'Ayam crispy dengan bumbu original',
                'category_id' => 3, // Asumsi kategori 3 untuk finished products
                'unit_id' => 3, // Asumsi unit 3 adalah pieces
                'production_cost' => 8000.00,
                'selling_price' => 15000.00,
                'min_stock' => 50.00,
                'stock_quantity' => 100.00,
                'is_active' => true,
                'is_centralized' => false,
                'managing_branch_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Ayam Crispy Spicy',
                'code' => 'FP002',
                'description' => 'Ayam crispy dengan bumbu pedas',
                'category_id' => 3,
                'unit_id' => 3, // pieces
                'production_cost' => 8500.00,
                'selling_price' => 16000.00,
                'min_stock' => 30.00,
                'stock_quantity' => 80.00,
                'is_active' => true,
                'is_centralized' => false,
                'managing_branch_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Paket Ayam + Nasi',
                'code' => 'FP003',
                'description' => 'Paket ayam crispy dengan nasi dan sambal',
                'category_id' => 3,
                'unit_id' => 3, // paket
                'production_cost' => 12000.00,
                'selling_price' => 25000.00,
                'min_stock' => 20.00,
                'stock_quantity' => 50.00,
                'is_active' => true,
                'is_centralized' => false,
                'managing_branch_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('finished_products')->insert($finishedProducts);

        // Assign Super Admin role to first user
        $user = DB::table('users')->first();
        if ($user) {
            $existingRole = DB::table('user_roles')
                ->where('user_id', $user->id)
                ->where('role_id', 1)
                ->first();
                
            if (!$existingRole) {
                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role_id' => 1, // Super Admin
                    'assigned_by' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        echo "Product data seeded successfully!\n";
    }
}
