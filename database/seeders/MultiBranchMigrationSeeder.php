<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Material;
use App\Models\FinishedProduct;
use App\Services\BranchStockService;
use Illuminate\Support\Facades\DB;

class MultiBranchMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Update existing purchase orders to have branch_id
            $this->updatePurchaseOrdersBranchId();
            
            // Set default values for new columns
            $this->setDefaultMaterialValues();
            $this->setDefaultFinishedProductValues();
            
            // Initialize branch stocks for all existing items
            $this->initializeBranchStocks();
            
            DB::commit();
            
            $this->command->info('Multi-branch migration completed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('Migration failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function updatePurchaseOrdersBranchId()
    {
        $this->command->info('Updating purchase orders with branch_id...');
        
        // Get first active branch as default
        $defaultBranch = Branch::where('is_active', true)->first();
        
        if ($defaultBranch) {
            DB::table('purchase_orders')
                ->whereNull('branch_id')
                ->update(['branch_id' => $defaultBranch->id]);
                
            $this->command->info("Updated purchase orders with default branch: {$defaultBranch->name}");
        }
    }
    
    private function setDefaultMaterialValues()
    {
        $this->command->info('Setting default values for materials...');
        
        DB::table('materials')
            ->whereNull('is_centralized')
            ->update([
                'is_centralized' => true,
                'managing_branch_id' => null
            ]);
    }
    
    private function setDefaultFinishedProductValues()
    {
        $this->command->info('Setting default values for finished products...');
        
        DB::table('finished_products')
            ->whereNull('is_centralized')
            ->update([
                'is_centralized' => true,
                'managing_branch_id' => null
            ]);
    }
    
    private function initializeBranchStocks()
    {
        $this->command->info('Initializing branch stocks...');
        
        $branchStockService = new BranchStockService();
        $branches = Branch::where('is_active', true)->get();
        
        foreach ($branches as $branch) {
            $this->command->info("Initializing stock for branch: {$branch->name}");
            
            // Initialize materials
            $materials = Material::where('is_active', true)->get();
            foreach ($materials as $material) {
                DB::table('branch_stocks')->updateOrInsert(
                    [
                        'branch_id' => $branch->id,
                        'item_type' => 'material',
                        'item_id' => $material->id
                    ],
                    [
                        'current_stock' => $material->current_stock ?? 0,
                        'minimum_stock' => $material->minimum_stock,
                        'average_cost' => $material->unit_price ?? 0,
                        'last_updated' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
            
            // Initialize finished products
            $finishedProducts = FinishedProduct::where('is_active', true)->get();
            foreach ($finishedProducts as $product) {
                DB::table('branch_stocks')->updateOrInsert(
                    [
                        'branch_id' => $branch->id,
                        'item_type' => 'finished_product',
                        'item_id' => $product->id
                    ],
                    [
                        'current_stock' => $product->stock_quantity ?? 0,
                        'minimum_stock' => $product->min_stock,
                        'average_cost' => $product->price ?? 0,
                        'last_updated' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
            
            $this->command->info("✓ Branch {$branch->name} stock initialized");
        }
    }
}
