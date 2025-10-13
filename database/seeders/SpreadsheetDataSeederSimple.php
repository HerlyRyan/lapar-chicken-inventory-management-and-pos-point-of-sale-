<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Unit;
use App\Models\RawMaterial;
use App\Models\SemiFinishedProduct;
use App\Models\FinishedProduct;
use App\Models\Branch;
use App\Models\SemiFinishedBranchStock;
use App\Models\FinishedBranchStock;
use App\Services\SpreadsheetParserService;

class SpreadsheetDataSeederSimple extends Seeder
{
    private $parser;
    
    public function run()
    {
        $this->parser = new SpreadsheetParserService();
        $this->command->info('Starting spreadsheet data import...');
        
        DB::transaction(function () {
            // Import in correct order due to foreign key constraints
            $this->importBasicData();
            $this->importRawMaterials();
            $this->importSemiFinishedProducts();
            $this->importFinishedProducts();
        });
        
        $this->command->info('Spreadsheet data import completed successfully!');
    }

    private function importBasicData()
    {
        $this->command->info('Importing basic data (categories, units, suppliers)...');
        
        // Import essential categories if they don't exist
        $categories = [
            ['name' => 'Supply Pusat', 'code' => 'SP'],
            ['name' => 'Material Supply', 'code' => 'MS'],
            ['name' => 'Packing Supply', 'code' => 'PS'],
            ['name' => 'Cleaning Supply', 'code' => 'CS'],
            ['name' => 'Ala Carte', 'code' => 'AC'],
            ['name' => 'Drink', 'code' => 'DR'],
            ['name' => 'Device Supply', 'code' => 'DS'],
        ];
        
        foreach ($categories as $cat) {
            DB::table('categories')->insertOrIgnore([
                'name' => $cat['name'],
                'code' => $cat['code'],
                'description' => 'Category for ' . $cat['name'],
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Import essential units if they don't exist
        $units = [
            ['unit_name' => 'Karung', 'abbreviation' => 'KRG'],
            ['unit_name' => 'KG', 'abbreviation' => 'KG'],
            ['unit_name' => 'gram', 'abbreviation' => 'GR'],
            ['unit_name' => 'pack', 'abbreviation' => 'PCK'],
            ['unit_name' => 'Ekor', 'abbreviation' => 'EKR'],
            ['unit_name' => 'potong', 'abbreviation' => 'PTG'],
            ['unit_name' => 'pcs', 'abbreviation' => 'PCS'],
            ['unit_name' => 'ml', 'abbreviation' => 'ML'],
        ];
        
        foreach ($units as $unit) {
            DB::table('units')->insertOrIgnore([
                'unit_name' => $unit['unit_name'],
                'abbreviation' => $unit['abbreviation'],
                'description' => 'Unit for ' . $unit['unit_name'],
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Import suppliers from parsed data
        $suppliers = $this->parser->parseSuppliers();
        $supplierCount = 0;
        
        foreach ($suppliers as $supplierData) {
            $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $supplierData['name']), 0, 5));
            
            DB::table('suppliers')->insertOrIgnore([
                'name' => $supplierData['name'],
                'code' => $code,
                'phone' => $supplierData['phone'] ?? null,
                'address' => $supplierData['address'] ?? null,
                'email' => null,
                'contact_person' => null,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $supplierCount++;
        }
        
        $this->command->info("Basic data imported - Categories: " . count($categories) . ", Units: " . count($units) . ", Suppliers: {$supplierCount}");
    }

    private function importRawMaterials()
    {
        $this->command->info('Importing raw materials...');
        
        $rawMaterials = $this->parser->parseRawMaterials();
        $count = 0;
        
        foreach ($rawMaterials as $data) {
            if (!empty($data['name']) && !empty($data['code'])) {
                // Find category
                $category = DB::table('categories')->where('name', 'like', '%' . $data['category'] . '%')->first();
                $categoryId = $category ? $category->id : 1; // Default to first category
                
                // Find unit
                $unit = DB::table('units')->where('unit_name', $data['unit'])->first();
                $unitId = $unit ? $unit->id : 1; // Default to first unit
                
                // Find supplier
                $supplier = null;
                $supplierId = null;
                if (!empty($data['supplier_name'])) {
                    $supplier = DB::table('suppliers')->where('name', 'like', '%' . $data['supplier_name'] . '%')->first();
                    $supplierId = $supplier ? $supplier->id : null;
                }
                
                DB::table('raw_materials')->insertOrIgnore([
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
            }
        }
        
        $this->command->info("Raw materials imported: {$count}");
    }

    private function importSemiFinishedProducts()
    {
        $this->command->info('Importing semi-finished products...');
        
        $semiFinished = $this->parser->parseSemiFinishedProducts();
        $count = 0;
        
        foreach ($semiFinished as $data) {
            if (!empty($data['name']) && !empty($data['code'])) {
                // Find category
                $category = DB::table('categories')->where('name', 'like', '%' . $data['category'] . '%')->first();
                $categoryId = $category ? $category->id : 1;
                
                // Find unit
                $unit = DB::table('units')->where('unit_name', $data['unit'])->first();
                $unitId = $unit ? $unit->id : 1;
                
                DB::table('semi_finished_products')->insertOrIgnore([
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'category_id' => $categoryId,
                    'unit_id' => $unitId,
                    'minimum_stock' => $data['minimum_stock'] ?? 0,
                    'stock_quantity' => $data['branch_stock'] ?? 0,
                    'unit_price' => 0,
                    'is_active' => 1,
                    'description' => 'Imported from spreadsheet data',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $count++;
            }
        }
        
        $this->command->info("Semi-finished products imported: {$count}");
    }

    private function importFinishedProducts()
    {
        $this->command->info('Importing finished products...');
        
        $finishedProducts = $this->parser->parseFinishedProducts();
        $count = 0;
        
        foreach ($finishedProducts as $data) {
            if (!empty($data['name']) && !empty($data['code'])) {
                // Find category
                $category = DB::table('categories')->where('name', 'like', '%' . $data['category'] . '%')->first();
                $categoryId = $category ? $category->id : 1;
                
                // Find unit (default to pieces)
                $unit = DB::table('units')->where('unit_name', 'pcs')->first();
                $unitId = $unit ? $unit->id : 1;
                
                DB::table('finished_products')->insertOrIgnore([
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'category_id' => $categoryId,
                    'unit_id' => $unitId,
                    'price' => $data['price'] ?? 0,
                    'production_cost' => 0,
                    'minimum_stock' => 5,
                    'stock_quantity' => 0,
                    'is_active' => 1,
                    'description' => 'Imported from spreadsheet data',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $count++;
            }
        }
        
        $this->command->info("Finished products imported: {$count}");
    }
}
