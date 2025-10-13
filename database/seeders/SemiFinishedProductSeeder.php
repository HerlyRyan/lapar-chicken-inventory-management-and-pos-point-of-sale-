<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SemiFinishedProduct;
use App\Models\SemiFinishedBranchStock;
use App\Models\Branch;
use App\Models\Unit;

class SemiFinishedProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get unit IDs
        $kgUnit = Unit::where('abbreviation', 'kg')->first();
        $packUnit = Unit::where('abbreviation', 'pack')->first();
        
        // Create sample semi-finished products
        $semiFinishedProducts = [
            [
                'name' => 'Ayam Marinasi',
                'code' => 'SM001',
                'description' => 'Ayam yang sudah dimarinasi dengan bumbu khusus',
                'unit_id' => $kgUnit->id,
                'unit_price' => 40000,
                'is_active' => true,
            ],
            [
                'name' => 'Tepung Bumbu Siap Pakai',
                'code' => 'SM002', 
                'description' => 'Campuran tepung dengan bumbu yang sudah diracik',
                'unit_id' => $kgUnit->id,
                'unit_price' => 18000,
                'is_active' => true,
            ],
            [
                'name' => 'Potongan Ayam Seasoned',
                'code' => 'SM003',
                'description' => 'Potongan ayam yang sudah diberi bumbu dasar',
                'unit_id' => $packUnit->id,
                'unit_price' => 35000,
                'is_active' => true,
            ],
            [
                'name' => 'Batter Mix Siap Goreng',
                'code' => 'SM004',
                'description' => 'Adonan tepung siap untuk menggoreng ayam',
                'unit_id' => $kgUnit->id,
                'unit_price' => 22000,
                'is_active' => true,
            ]
        ];

        // Get all active branches
        $branches = Branch::where('is_active', true)->get();
        
        foreach ($semiFinishedProducts as $productData) {
            $product = SemiFinishedProduct::create($productData);
            
            // Initialize stock for each branch
            foreach ($branches as $branch) {
                // Set different initial stock quantities for variety
                $initialStock = rand(10, 50);
                $minimumStock = rand(5, 15);
                
                SemiFinishedBranchStock::create([
                    'branch_id' => $branch->id,
                    'semi_finished_product_id' => $product->id,
                    'quantity' => $initialStock,
                    'minimum_stock' => $minimumStock,
                    'average_cost' => $product->unit_price
                ]);
            }
        }
        
        $this->command->info('Semi-finished products and branch stocks seeded successfully!');
    }
}
