<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseFixer extends Seeder
{
    public function run()
    {
        // 1. Seed Branches (required for users)
        DB::table('branches')->insert([
            [
                'id' => 1,
                'name' => 'Cabang Utama',
                'code' => 'MAIN',
                'address' => 'Jl. Utama No. 1',
                'phone' => '021-1234567',
                'email' => 'main@laparchicken.com',
                'is_active' => true,
                'is_main' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Cabang Kedua',
                'code' => 'BRANCH2',
                'address' => 'Jl. Kedua No. 2',
                'phone' => '021-7654321',
                'email' => 'branch2@laparchicken.com',
                'is_active' => true,
                'is_main' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 2. Seed Units
        DB::table('units')->insert([
            [
                'name' => 'Kilogram',
                'symbol' => 'kg',
                'description' => 'Satuan berat kilogram',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pieces',
                'symbol' => 'pcs',
                'description' => 'Satuan buah/potong',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Liter',
                'symbol' => 'L',
                'description' => 'Satuan volume liter',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 3. Seed Categories
        DB::table('categories')->insert([
            [
                'name' => 'Ayam Goreng',
                'code' => 'FRIED',
                'description' => 'Kategori ayam goreng',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ayam Bakar',
                'code' => 'GRILLED',
                'description' => 'Kategori ayam bakar',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Minuman',
                'code' => 'DRINK',
                'description' => 'Kategori minuman',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 4. Seed Suppliers
        DB::table('suppliers')->insert([
            [
                'name' => 'PT. Ayam Segar',
                'code' => 'SUP001',
                'address' => 'Jl. Supplier No. 1',
                'phone' => '021-1111111',
                'email' => 'info@ayamsegar.com',
                'contact_person' => 'Budi Santoso',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CV. Bumbu Nusantara',
                'code' => 'SUP002',
                'address' => 'Jl. Bumbu No. 2',
                'phone' => '021-2222222',
                'email' => 'info@bumbunusantara.com',
                'contact_person' => 'Siti Nurhaliza',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 5. Seed Account Types
        DB::table('account_types')->insert([
            [
                'name' => 'Admin',
                'code' => 'ADMIN',
                'description' => 'Administrator sistem',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager',
                'code' => 'MANAGER',
                'description' => 'Manager cabang',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kasir',
                'code' => 'CASHIER',
                'description' => 'Kasir',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 6. Seed Admin User
        DB::table('users')->insert([
            [
                'name' => 'Administrator',
                'email' => 'admin@laparchicken.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_active' => true,
                'branch_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager Utama',
                'email' => 'manager@laparchicken.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_active' => true,
                'branch_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 7. Seed Sample Materials
        DB::table('materials')->insert([
            [
                'name' => 'Ayam Potong',
                'code' => 'MAT001',
                'description' => 'Ayam potong segar',
                'unit_id' => 1, // kg
                'minimum_stock' => 10.00,
                'supplier_id' => 1,
                'unit_price' => 35000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tepung Bumbu',
                'code' => 'MAT002',
                'description' => 'Tepung bumbu untuk ayam goreng',
                'unit_id' => 1, // kg
                'minimum_stock' => 5.00,
                'supplier_id' => 2,
                'unit_price' => 15000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 8. Seed Sample Finished Products
        DB::table('finished_products')->insert([
            [
                'name' => 'Ayam Goreng Crispy',
                'code' => 'PROD001',
                'description' => 'Ayam goreng crispy original',
                'unit_id' => 2, // pcs
                'category_id' => 1, // Ayam Goreng
                'min_stock' => 5.00,
                'price' => 25000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ayam Bakar Madu',
                'code' => 'PROD002',
                'description' => 'Ayam bakar dengan saus madu',
                'unit_id' => 2, // pcs
                'category_id' => 2, // Ayam Bakar
                'min_stock' => 3.00,
                'price' => 30000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
