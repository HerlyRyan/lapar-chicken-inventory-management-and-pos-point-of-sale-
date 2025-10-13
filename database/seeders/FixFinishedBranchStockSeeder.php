<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\FinishedProduct;
use App\Models\FinishedBranchStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixFinishedBranchStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder ensures that every active finished product has a stock record for every active branch
     * It will create missing stock records and set a default quantity of 10 for demonstration purposes
     */
    public function run(): void
    {
        // Get all active branches
        $branches = Branch::where('is_active', true)->get();
        
        if ($branches->isEmpty()) {
            $this->command->info('No active branches found. Please create branches first.');
            return;
        }
        
        // Get all active finished products
        $products = FinishedProduct::where('is_active', true)->get();
        
        if ($products->isEmpty()) {
            $this->command->info('No active finished products found. Please create products first.');
            return;
        }
        
        $this->command->info('Starting to fix finished branch stock records...');
        $this->command->info('Found ' . $branches->count() . ' active branches and ' . $products->count() . ' active finished products');
        
        $created = 0;
        $updated = 0;
        $alreadyExists = 0;
        
        // For each branch, ensure all products have stock records
        foreach ($branches as $branch) {
            $this->command->info('Processing branch: ' . $branch->name . ' (ID: ' . $branch->id . ')');
            
            foreach ($products as $product) {
                // Check if stock record already exists
                $stockRecord = FinishedBranchStock::where('branch_id', $branch->id)
                    ->where('finished_product_id', $product->id)
                    ->first();
                
                if ($stockRecord) {
                    // Update if quantity is 0
                    if ($stockRecord->quantity <= 0) {
                        $stockRecord->quantity = 10; // Set a default quantity for demo
                        $stockRecord->save();
                        $updated++;
                        $this->command->info('Updated stock for product: ' . $product->name . ' in branch: ' . $branch->name);
                    } else {
                        $alreadyExists++;
                    }
                } else {
                    // Create new stock record
                    FinishedBranchStock::create([
                        'branch_id' => $branch->id,
                        'finished_product_id' => $product->id,
                        'quantity' => 10, // Set a default quantity for demo
                    ]);
                    $created++;
                    $this->command->info('Created stock for product: ' . $product->name . ' in branch: ' . $branch->name);
                }
            }
        }
        
        $this->command->info('Finished fixing branch stocks!');
        $this->command->info('Created: ' . $created . ' | Updated: ' . $updated . ' | Already Exists: ' . $alreadyExists);
    }
}
