<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Services\SpreadsheetParserService;

class ImportSpreadsheetDataSeeder extends Seeder
{
    private $parser;
    
    public function run()
    {
        $this->parser = new SpreadsheetParserService();
        $this->command->info('Starting spreadsheet data import (NON-DESTRUCTIVE)...');
        
        // Import data safely without destroying existing data
        $this->importSuppliersFromSpreadsheet();
        $this->importRawMaterialsFromSpreadsheet();
        $this->importSemiFinishedProductsFromSpreadsheet();
        $this->importFinishedProductsFromSpreadsheet();
        
        $this->command->info('Spreadsheet data import completed successfully!');
    }

    private function importSuppliersFromSpreadsheet()
    {
        $this->command->info('Importing suppliers from spreadsheet...');
        
        $suppliers = $this->parser->parseSuppliers();
        $count = 0;
        
        foreach ($suppliers as $supplierData) {
            // Check if supplier already exists
            $exists = DB::table('suppliers')->where('name', $supplierData['name'])->exists();
            
            if (!$exists) {
                $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $supplierData['name']), 0, 5));
                
                try {
                    DB::table('suppliers')->insert([
                        'name' => $supplierData['name'],
                        'code' => $code,
                        'phone' => $supplierData['phone'] ?? null,
                        'address' => $supplierData['address'] ?? null,
                        'email' => null,
                        'is_active' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $count++;
                } catch (\Exception $e) {
                    $this->command->warn("Failed to import supplier {$supplierData['name']}: " . $e->getMessage());
                }
            }
        }
        
        $this->command->info("New suppliers imported: {$count}");
    }

    private function importRawMaterialsFromSpreadsheet()
    {
        $this->command->info('Importing raw materials from spreadsheet...');
        
        $rawMaterials = $this->parser->parseRawMaterials();
        $count = 0;
        
        foreach ($rawMaterials as $data) {
            if (!empty($data['name']) && !empty($data['code'])) {
                // Check if raw material already exists
                $exists = DB::table('raw_materials')->where('code', $data['code'])->exists();
                
                if (!$exists) {
                    // Find category (use existing or create basic one)
                    $category = DB::table('categories')->where('name', 'like', '%' . $data['category'] . '%')->first();
                    if (!$category) {
                        // Create basic category if not exists
                        $categoryCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $data['category']), 0, 3));
                        DB::table('categories')->insertOrIgnore([
                            'name' => $data['category'],
                            'code' => $categoryCode,
                            'description' => 'Auto-created for ' . $data['category'],
                            'is_active' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $category = DB::table('categories')->where('name', $data['category'])->first();
                    }
                    
                    // Find unit (use existing or create basic one)
                    $unit = DB::table('units')->where('unit_name', $data['unit'])->first();
                    if (!$unit) {
                        // Create basic unit if not exists
                        $abbreviation = strtoupper(substr($data['unit'], 0, 3));
                        DB::table('units')->insertOrIgnore([
                            'unit_name' => $data['unit'],
                            'abbreviation' => $abbreviation,
                            'description' => 'Unit for ' . $data['unit'],
                            'is_active' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $unit = DB::table('units')->where('unit_name', $data['unit'])->first();
                    }
                    
                    // Find supplier
                    $supplier = null;
                    $supplierId = null;
                    if (!empty($data['supplier_name'])) {
                        $supplier = DB::table('suppliers')->where('name', 'like', '%' . $data['supplier_name'] . '%')->first();
                        $supplierId = $supplier ? $supplier->id : null;
                    }
                    
                    try {
                        DB::table('raw_materials')->insert([
                            'name' => $data['name'],
                            'code' => $data['code'],
                            'category_id' => $category->id,
                            'unit_id' => $unit->id,
                            'supplier_id' => $supplierId,
                            'current_stock' => $data['current_stock'] ?? 0,
                            'minimum_stock' => $data['minimum_stock'] ?? 0,
                            'unit_price' => $data['unit_price'] ?? 0,
                            'is_active' => 1,
                            'description' => 'Imported from spreadsheet data',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $count++;
                    } catch (\Exception $e) {
                        $this->command->warn("Failed to import raw material {$data['name']}: " . $e->getMessage());
                    }
                }
            }
        }
        
        $this->command->info("New raw materials imported: {$count}");
    }

    private function importSemiFinishedProductsFromSpreadsheet()
    {
        $this->command->info('Importing semi-finished products from spreadsheet...');
        
        $semiFinished = $this->parser->parseSemiFinishedProducts();
        $count = 0;
        
        foreach ($semiFinished as $data) {
            if (!empty($data['name']) && !empty($data['code'])) {
                // Check if semi-finished product already exists
                $exists = DB::table('semi_finished_products')->where('code', $data['code'])->exists();
                
                if (!$exists) {
                    // Find category
                    $category = DB::table('categories')->where('name', 'like', '%' . $data['category'] . '%')->first();
                    if (!$category) {
                        $categoryCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $data['category']), 0, 3));
                        DB::table('categories')->insertOrIgnore([
                            'name' => $data['category'],
                            'code' => $categoryCode,
                            'description' => 'Auto-created for ' . $data['category'],
                            'is_active' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $category = DB::table('categories')->where('name', $data['category'])->first();
                    }
                    
                    // Find unit
                    $unit = DB::table('units')->where('unit_name', $data['unit'])->first();
                    if (!$unit) {
                        $abbreviation = strtoupper(substr($data['unit'], 0, 3));
                        DB::table('units')->insertOrIgnore([
                            'unit_name' => $data['unit'],
                            'abbreviation' => $abbreviation,
                            'description' => 'Unit for ' . $data['unit'],
                            'is_active' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $unit = DB::table('units')->where('unit_name', $data['unit'])->first();
                    }
                    
                    try {
                        DB::table('semi_finished_products')->insert([
                            'name' => $data['name'],
                            'code' => $data['code'],
                            'category_id' => $category->id,
                            'unit_id' => $unit->id,
                            'minimum_stock' => $data['minimum_stock'] ?? 0,
                            'unit_price' => 0,
                            'is_active' => 1,
                            'description' => 'Imported from spreadsheet data',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $count++;
                    } catch (\Exception $e) {
                        $this->command->warn("Failed to import semi-finished product {$data['name']}: " . $e->getMessage());
                    }
                }
            }
        }
        
        $this->command->info("New semi-finished products imported: {$count}");
    }

    private function importFinishedProductsFromSpreadsheet()
    {
        $this->command->info('Importing finished products from spreadsheet...');
        
        $finishedProducts = $this->parser->parseFinishedProducts();
        $count = 0;
        
        foreach ($finishedProducts as $data) {
            if (!empty($data['name']) && !empty($data['code'])) {
                // Check if finished product already exists
                $exists = DB::table('finished_products')->where('code', $data['code'])->exists();
                
                if (!$exists) {
                    // Find category
                    $category = DB::table('categories')->where('name', 'like', '%' . $data['category'] . '%')->first();
                    if (!$category) {
                        $categoryCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $data['category']), 0, 3));
                        DB::table('categories')->insertOrIgnore([
                            'name' => $data['category'],
                            'code' => $categoryCode,
                            'description' => 'Auto-created for ' . $data['category'],
                            'is_active' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $category = DB::table('categories')->where('name', $data['category'])->first();
                    }
                    
                    // Find unit (default to pieces)
                    $unit = DB::table('units')->where('unit_name', 'pcs')->first();
                    if (!$unit) {
                        DB::table('units')->insertOrIgnore([
                            'unit_name' => 'pcs',
                            'abbreviation' => 'PCS',
                            'description' => 'Unit for pieces',
                            'is_active' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $unit = DB::table('units')->where('unit_name', 'pcs')->first();
                    }
                    
                    try {
                        DB::table('finished_products')->insert([
                            'name' => $data['name'],
                            'code' => $data['code'],
                            'category_id' => $category->id,
                            'unit_id' => $unit->id,
                            'price' => $data['price'] ?? 0,
                            'production_cost' => 0,
                            'minimum_stock' => 5,
                            'is_active' => 1,
                            'description' => 'Imported from spreadsheet data',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $count++;
                    } catch (\Exception $e) {
                        $this->command->warn("Failed to import finished product {$data['name']}: " . $e->getMessage());
                    }
                }
            }
        }
        
        $this->command->info("New finished products imported: {$count}");
    }
}
