<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Services\SpreadsheetParserService;

class FinishedProductsImporter extends Seeder
{
    private $parser;
    
    public function run()
    {
        $this->parser = new SpreadsheetParserService();
        $this->command->info('Starting finished products import...');
        
        $this->importFinishedProducts();
        
        $this->command->info('Finished products import completed!');
    }

    private function importFinishedProducts()
    {
        $this->command->info('Importing finished products...');
        
        $finishedProducts = $this->parser->parseFinishedProducts();
        $count = 0;
        
        // Get default category and unit as fallbacks
        $defaultCategory = DB::table('categories')->first();
        $defaultUnit = DB::table('units')->first();
        
        if (!$defaultCategory || !$defaultUnit) {
            $this->command->warn('No categories or units found in database. Skipping finished products import.');
            return;
        }
        
        foreach ($finishedProducts as $data) {
            if (!empty($data['name']) && !empty($data['code'])) {
                // Check if finished product already exists
                $exists = DB::table('finished_products')->where('code', $data['code'])->exists();
                
                if (!$exists) {
                    // Find category (use default if not found)
                    $category = DB::table('categories')->where('name', 'like', '%' . $data['category'] . '%')->first();
                    $categoryId = $category ? $category->id : $defaultCategory->id;
                    
                    // Use default unit since finished products don't have unit in spreadsheet
                    $unitId = $defaultUnit->id;
                    
                    try {
                        // Insert finished product
                        $productId = DB::table('finished_products')->insertGetId([
                            'name' => $data['name'],
                            'code' => $data['code'],
                            'category_id' => $categoryId,
                            'unit_id' => $unitId,
                            'price' => $data['price'] ?? 0,
                            'production_cost' => 0,
                            'minimum_stock' => $data['minimum_stock'] ?? 0,
                            'description' => 'Imported from spreadsheet data',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        // Initialize stock for all branches
                        $branches = DB::table('branches')->get();
                        foreach ($branches as $branch) {
                            DB::table('finished_branch_stocks')->insertOrIgnore([
                                'finished_product_id' => $productId,
                                'branch_id' => $branch->id,
                                'quantity' => $data['current_stock'] ?? 0,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                        
                        $count++;
                        $this->command->info("✓ Imported finished product: {$data['name']}");
                    } catch (\Exception $e) {
                        $this->command->warn("✗ Failed to import finished product {$data['name']}: " . $e->getMessage());
                    }
                } else {
                    $this->command->info("- Finished product already exists: {$data['name']}");
                }
            }
        }
        
        $this->command->info("Finished products imported: {$count}");
    }
}
