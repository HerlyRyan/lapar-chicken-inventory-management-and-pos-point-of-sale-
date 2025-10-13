<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Services\SpreadsheetParserService;

class SemiFinishedProductsImporter extends Seeder
{
    private $parser;
    
    public function run()
    {
        $this->parser = new SpreadsheetParserService();
        $this->command->info('Starting semi-finished products import...');
        
        $this->importSemiFinishedProducts();
        
        $this->command->info('Semi-finished products import completed!');
    }

    private function importSemiFinishedProducts()
    {
        $this->command->info('Importing semi-finished products...');
        
        $semiFinishedProducts = $this->parser->parseSemiFinishedProducts();
        $count = 0;
        
        // Get default category and unit as fallbacks
        $defaultCategory = DB::table('categories')->first();
        $defaultUnit = DB::table('units')->first();
        
        if (!$defaultCategory || !$defaultUnit) {
            $this->command->warn('No categories or units found in database. Skipping semi-finished products import.');
            return;
        }
        
        foreach ($semiFinishedProducts as $data) {
            if (!empty($data['name']) && !empty($data['code'])) {
                // Check if semi-finished product already exists
                $exists = DB::table('semi_finished_products')->where('code', $data['code'])->exists();
                
                if (!$exists) {
                    // Find category (use default if not found)
                    $category = DB::table('categories')->where('name', 'like', '%' . $data['category'] . '%')->first();
                    $categoryId = $category ? $category->id : $defaultCategory->id;
                    
                    // Find unit (use default if not found)
                    $unit = DB::table('units')->where('unit_name', $data['unit'])->first();
                    $unitId = $unit ? $unit->id : $defaultUnit->id;
                    
                    try {
                        // Insert semi-finished product
                        $productId = DB::table('semi_finished_products')->insertGetId([
                            'name' => $data['name'],
                            'code' => $data['code'],
                            'category_id' => $categoryId,
                            'unit_id' => $unitId,
                            'minimum_stock' => $data['minimum_stock'] ?? 0,
                            'unit_price' => $data['unit_price'] ?? 0,
                            'description' => 'Imported from spreadsheet data',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        // Initialize stock for all active branches
                        $branches = DB::table('branches')->get();
                        foreach ($branches as $branch) {
                            DB::table('semi_finished_branch_stocks')->insertOrIgnore([
                                'semi_finished_product_id' => $productId,
                                'branch_id' => $branch->id,
                                'quantity' => $data['current_stock'] ?? 0,
                                'average_cost' => $data['unit_price'] ?? 0,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                        
                        $count++;
                        $this->command->info("✓ Imported semi-finished product: {$data['name']}");
                    } catch (\Exception $e) {
                        $this->command->warn("✗ Failed to import semi-finished product {$data['name']}: " . $e->getMessage());
                    }
                } else {
                    $this->command->info("- Semi-finished product already exists: {$data['name']}");
                }
            }
        }
        
        $this->command->info("Semi-finished products imported: {$count}");
    }
}
