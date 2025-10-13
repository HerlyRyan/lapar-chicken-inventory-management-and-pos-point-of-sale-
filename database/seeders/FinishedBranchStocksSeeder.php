<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\FinishedProduct;

class FinishedBranchStocksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds stock data for branch ID 6 (LC Sekumpul)
     */
    public function run(): void
    {
        // Check if branch exists
        $branchExists = DB::table('branches')->where('id', 6)->exists();
        
        if (!$branchExists) {
            $this->command->error('Branch with ID 6 does not exist. Seeding aborted.');
            return;
        }
        
        // Get all finished products
        $products = FinishedProduct::all();
        
        if ($products->isEmpty()) {
            $this->command->error('No finished products found. Seeding aborted.');
            return;
        }
        
        $this->command->info('Adding stock data for ' . $products->count() . ' products in branch ID 6');
        
        foreach ($products as $product) {
            // Check if stock entry already exists
            $exists = DB::table('finished_branch_stocks')
                ->where('branch_id', 6)
                ->where('finished_product_id', $product->id)
                ->exists();
                
            if (!$exists) {
                // Add stock entry with random quantity between 10 and 50
                DB::table('finished_branch_stocks')->insert([
                    'branch_id' => 6,
                    'finished_product_id' => $product->id,
                    'quantity' => rand(10, 50),
                    'minimum_stock' => 5,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $this->command->line('Added stock for product: ' . $product->name);
            } else {
                $this->command->line('Stock for product ' . $product->name . ' already exists. Skipping.');
            }
        }
        
        $this->command->info('Finished product stock data for branch ID 6 seeded successfully!');
    }
}
