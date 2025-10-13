<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SemiFinishedProduct;
use App\Models\SemiFinishedBranchStock;
use App\Models\FinishedProduct;
use App\Models\BranchStock;
use App\Models\Branch;
use App\Models\Material;

class MultiBranchStockMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting multi-branch stock migration...');
        
        $branches = Branch::where('is_active', true)->get();
        $this->command->info("Found {$branches->count()} active branches");

        // 1. Migrate existing semi-finished products to multi-branch system
        $this->migrateSemiFinishedProducts($branches);
        
        // 2. Ensure all finished products have branch stocks
        $this->ensureFinishedProductBranchStocks($branches);
        
        // 3. Ensure all materials have proper branch context
        $this->ensureMaterialStocks($branches);

        $this->command->info('Multi-branch stock migration completed successfully!');
    }

    private function migrateSemiFinishedProducts($branches)
    {
        $semiFinishedProducts = SemiFinishedProduct::where('is_active', true)->get();
        $this->command->info("Migrating {$semiFinishedProducts->count()} semi-finished products...");
        
        foreach ($semiFinishedProducts as $product) {
            foreach ($branches as $branch) {
                // Check if branch stock already exists
                $existingStock = SemiFinishedBranchStock::where('branch_id', $branch->id)
                    ->where('semi_finished_product_id', $product->id)
                    ->first();
                
                if (!$existingStock) {
                    // Create initial stock with random values for demo
                    $initialStock = rand(10, 100);
                    $minimumStock = rand(5, 20);
                    
                    SemiFinishedBranchStock::create([
                        'branch_id' => $branch->id,
                        'semi_finished_product_id' => $product->id,
                        'current_stock' => $initialStock,
                        'minimum_stock' => $minimumStock,
                        'maximum_stock' => $initialStock * 2,
                        'average_cost' => $product->unit_price ?? 0,
                        'last_updated' => now()
                    ]);
                    
                    $this->command->line("  - Created stock for {$product->name} at {$branch->name}: {$initialStock} units");
                }
            }
        }
    }

    private function ensureFinishedProductBranchStocks($branches)
    {
        $finishedProducts = FinishedProduct::where('is_active', true)->get();
        $this->command->info("Ensuring branch stocks for {$finishedProducts->count()} finished products...");
        
        foreach ($finishedProducts as $product) {
            foreach ($branches as $branch) {
                $existingStock = BranchStock::where('branch_id', $branch->id)
                    ->where('item_type', 'finished_product')
                    ->where('item_id', $product->id)
                    ->first();
                
                if (!$existingStock) {
                    $initialStock = $product->stock_quantity ?? rand(5, 50);
                    
                    BranchStock::create([
                        'branch_id' => $branch->id,
                        'item_type' => 'finished_product',
                        'item_id' => $product->id,
                        'current_stock' => $initialStock,
                        'minimum_stock' => $product->min_stock ?? 5,
                        'maximum_stock' => ($product->max_stock ?? $initialStock * 2),
                        'average_cost' => $product->production_cost ?? 0,
                        'last_updated' => now()
                    ]);
                    
                    $this->command->line("  - Created stock for {$product->name} at {$branch->name}: {$initialStock} units");
                }
            }
        }
    }

    private function ensureMaterialStocks($branches)
    {
        $materials = Material::where('is_active', true)->where('is_centralized', true)->get();
        $this->command->info("Ensuring material stocks for {$materials->count()} centralized materials...");
        
        foreach ($materials as $material) {
            foreach ($branches as $branch) {
                $existingStock = BranchStock::where('branch_id', $branch->id)
                    ->where('item_type', 'material')
                    ->where('item_id', $material->id)
                    ->first();
                
                if (!$existingStock) {
                    $initialStock = $material->current_stock ?? rand(20, 200);
                    
                    BranchStock::create([
                        'branch_id' => $branch->id,
                        'item_type' => 'material',
                        'item_id' => $material->id,
                        'current_stock' => $initialStock,
                        'minimum_stock' => $material->minimum_stock ?? 10,
                        'maximum_stock' => $initialStock * 3,
                        'average_cost' => $material->unit_price ?? 0,
                        'last_updated' => now()
                    ]);
                    
                    $this->command->line("  - Created stock for {$material->name} at {$branch->name}: {$initialStock} units");
                }
            }
        }
    }
}
