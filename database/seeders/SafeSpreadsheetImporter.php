<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Services\SpreadsheetParserService;

class SafeSpreadsheetImporter extends Seeder
{
    private $parser;
    
    public function run()
    {
        $this->parser = new SpreadsheetParserService();
        $this->command->info('Starting SAFE spreadsheet data import...');
        
        // Only import data that we know will work
        $this->importSuppliersOnly();
        $this->importRawMaterialsOnly();
        
        $this->command->info('Safe spreadsheet data import completed!');
    }

    private function importSuppliersOnly()
    {
        $this->command->info('Importing suppliers (safe mode)...');
        
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
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $count++;
                    $this->command->info("✓ Imported supplier: {$supplierData['name']}");
                } catch (\Exception $e) {
                    $this->command->warn("✗ Failed to import supplier {$supplierData['name']}: " . $e->getMessage());
                }
            } else {
                $this->command->info("- Supplier already exists: {$supplierData['name']}");
            }
        }
        
        $this->command->info("Suppliers imported: {$count}");
    }

    private function importRawMaterialsOnly()
    {
        $this->command->info('Importing raw materials (safe mode)...');
        
        $rawMaterials = $this->parser->parseRawMaterials();
        $count = 0;
        
        // Get first available category and unit as fallbacks
        $defaultCategory = DB::table('categories')->first();
        $defaultUnit = DB::table('units')->first();
        
        if (!$defaultCategory || !$defaultUnit) {
            $this->command->warn('No categories or units found in database. Skipping raw materials import.');
            return;
        }
        
        foreach ($rawMaterials as $data) {
            if (!empty($data['name']) && !empty($data['code'])) {
                // Check if raw material already exists
                $exists = DB::table('raw_materials')->where('code', $data['code'])->exists();
                
                if (!$exists) {
                    // Find category (use default if not found)
                    $category = DB::table('categories')->where('name', 'like', '%' . $data['category'] . '%')->first();
                    $categoryId = $category ? $category->id : $defaultCategory->id;
                    
                    // Find unit (use default if not found)
                    $unit = DB::table('units')->where('unit_name', $data['unit'])->first();
                    $unitId = $unit ? $unit->id : $defaultUnit->id;
                    
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
                            'category_id' => $categoryId,
                            'unit_id' => $unitId,
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
                        $this->command->info("✓ Imported raw material: {$data['name']}");
                    } catch (\Exception $e) {
                        $this->command->warn("✗ Failed to import raw material {$data['name']}: " . $e->getMessage());
                    }
                } else {
                    $this->command->info("- Raw material already exists: {$data['name']}");
                }
            }
        }
        
        $this->command->info("Raw materials imported: {$count}");
    }
}
