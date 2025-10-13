<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // 1. Create sample branches if not exist
            $branches = [
                ['name' => 'Cabang Pusat', 'code' => 'CP001', 'address' => 'Jl. Sudirman No. 123', 'phone' => '021-1234567', 'manager_name' => 'Ahmad Wijaya'],
                ['name' => 'Cabang Bekasi', 'code' => 'CB001', 'address' => 'Jl. Raya Bekasi No. 456', 'phone' => '021-7654321', 'manager_name' => 'Siti Aminah'],
                ['name' => 'Cabang Depok', 'code' => 'CD001', 'address' => 'Jl. Margonda No. 789', 'phone' => '021-9876543', 'manager_name' => 'Budi Santoso']
            ];

            foreach ($branches as $branch) {
                DB::table('branches')->updateOrInsert(
                    ['code' => $branch['code']], 
                    array_merge($branch, ['is_active' => 1, 'created_at' => now(), 'updated_at' => now()])
                );
            }

            // Get existing branch IDs
            $branches = DB::table('branches')->pluck('id')->toArray();
            $branchIds = !empty($branches) ? $branches : [1, 2, 3];

            // 2. Create sample users if not exist
            $users = [
                ['name' => 'Admin System', 'email' => 'admin@laparchicken.com', 'password' => bcrypt('password'), 'branch_id' => $branchIds[0] ?? 1],
                ['name' => 'Kasir Pusat', 'email' => 'kasir1@laparchicken.com', 'password' => bcrypt('password'), 'branch_id' => $branchIds[0] ?? 1],
                ['name' => 'Kasir Bekasi', 'email' => 'kasir2@laparchicken.com', 'password' => bcrypt('password'), 'branch_id' => $branchIds[1] ?? 2],
                ['name' => 'Kasir Depok', 'email' => 'kasir3@laparchicken.com', 'password' => bcrypt('password'), 'branch_id' => $branchIds[2] ?? 3]
            ];

            foreach ($users as $user) {
                DB::table('users')->updateOrInsert(
                    ['email' => $user['email']], 
                    array_merge($user, ['email_verified_at' => now(), 'created_at' => now(), 'updated_at' => now()])
                );
            }

            // 3. Create sample materials if not exist
            $materials = [
                ['name' => 'Ayam Utuh', 'code' => 'AY001', 'description' => 'Ayam segar utuh', 'category' => 'raw_material', 'unit' => 'kg', 'minimum_stock' => 50, 'current_stock' => 100],
                ['name' => 'Tepung Bumbu', 'code' => 'TB001', 'description' => 'Tepung bumbu spesial', 'category' => 'raw_material', 'unit' => 'kg', 'minimum_stock' => 20, 'current_stock' => 45],
                ['name' => 'Minyak Goreng', 'code' => 'MG001', 'description' => 'Minyak goreng untuk menggoreng', 'category' => 'raw_material', 'unit' => 'liter', 'minimum_stock' => 30, 'current_stock' => 75],
                ['name' => 'Ayam Potong', 'code' => 'AP001', 'description' => 'Ayam yang sudah dipotong-potong', 'category' => 'semi_finished', 'unit' => 'kg', 'minimum_stock' => 25, 'current_stock' => 60],
                ['name' => 'Ayam Goreng Crispy', 'code' => 'AGC001', 'description' => 'Ayam goreng crispy siap saji', 'category' => 'finished_product', 'unit' => 'potong', 'minimum_stock' => 50, 'current_stock' => 120]
            ];

            foreach ($materials as $material) {
                DB::table('materials')->updateOrInsert(
                    ['code' => $material['code']], 
                    array_merge($material, ['is_active' => 1, 'created_at' => now(), 'updated_at' => now()])
                );
            }

            // 4. Create sample sales for last 30 days
            $today = Carbon::today();
            $existingUserIds = DB::table('users')->pluck('id')->toArray();
            $userIds = !empty($existingUserIds) ? $existingUserIds : [1, 2, 3, 4];
            
            for ($i = 0; $i < 30; $i++) {
                $saleDate = $today->copy()->subDays($i);
                $branchId = $branchIds[array_rand($branchIds)];
                $processedBy = $userIds[array_rand($userIds)];
                
                // Create 2-5 sales per day per branch
                for ($j = 0; $j < rand(2, 5); $j++) {
                    $totalAmount = rand(50000, 500000);
                    $discountAmount = rand(0, 50000);
                    $taxAmount = rand(0, 10000);
                    $finalAmount = $totalAmount - $discountAmount + $taxAmount;
                    
                    DB::table('sales')->insert([
                        'transaction_code' => 'TR' . date('Ymd', strtotime($saleDate)) . sprintf('%04d', $j + 1),
                        'branch_id' => $branchId,
                        'total_amount' => $totalAmount,
                        'discount_amount' => $discountAmount,
                        'tax_amount' => $taxAmount,
                        'final_amount' => $finalAmount,
                        'payment_method' => ['cash', 'card', 'transfer'][rand(0, 2)],
                        'transaction_date' => $saleDate,
                        'status' => 'completed',
                        'notes' => 'Sample transaction',
                        'created_at' => $saleDate,
                        'updated_at' => $saleDate
                    ]);
                }
            }

            // 5. Create sample stock movements
            $existingMaterialIds = DB::table('materials')->pluck('id')->toArray();
            $materialIds = !empty($existingMaterialIds) ? $existingMaterialIds : [1, 2, 3, 4, 5];
            
            for ($i = 0; $i < 50; $i++) {
                $movementDate = $today->copy()->subDays(rand(0, 30));
                $materialId = $materialIds[array_rand($materialIds)];
                $branchId = $branchIds[array_rand($branchIds)];
                $movementType = ['in', 'out', 'transfer'][rand(0, 2)];
                $quantityMoved = rand(1, 20);
                $quantityBefore = rand(50, 100);
                $quantityAfter = $movementType === 'in' ? $quantityBefore + $quantityMoved : $quantityBefore - $quantityMoved;
                
                DB::table('stock_movements')->insert([
                    'material_id' => $materialId,
                    'branch_id' => $branchId,
                    'movement_type' => $movementType,
                    'movement_category' => ['raw_material', 'semi_finished', 'finished_product'][rand(0, 2)],
                    'quantity_before' => $quantityBefore,
                    'quantity_moved' => $quantityMoved,
                    'quantity_after' => $quantityAfter,
                    'unit_cost' => rand(10000, 50000),
                    'total_cost' => $quantityMoved * rand(10000, 50000),
                    'reference_type' => 'adjustment',
                    'notes' => 'Sample movement',
                    'processed_by' => $userIds[array_rand($userIds)],
                    'processed_at' => $movementDate,
                    'created_at' => $movementDate,
                    'updated_at' => $movementDate
                ]);
            }

            DB::commit();
            $this->command->info('Sample data created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('Error creating sample data: ' . $e->getMessage());
        }
    }
}
