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

class SpreadsheetDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->parser = new SpreadsheetParserService();
        $this->command->info('Starting spreadsheet data import...');
        
        DB::transaction(function () {
            // Import in correct order due to foreign key constraints
            $this->importCategories();
            $this->importUnits();
            $this->importSuppliers();
            $this->importRawMaterials();
            $this->importSemiFinishedProducts();
            $this->importFinishedProducts();
        });
        
        $this->command->info('Spreadsheet data import completed successfully!');
    }

    private function importCategories()
    {
        $this->command->info('Importing Categories...');
        
        $categories = $this->parser->getUniqueCategories();
        $count = 0;
        
        foreach ($categories as $categoryName) {
            if (!empty($categoryName) && $categoryName !== '#N/A') {
                $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $categoryName), 0, 3));
                Category::updateOrCreate(
                    ['name' => $categoryName],
                    [
                        'code' => $code,
                        'description' => 'Category for ' . $categoryName,
                        'is_active' => true
                    ]
                );
                $count++;
            }
        }
        
        $this->command->info("Categories imported: {$count}");
    }

    private function importUnits()
    {
        $this->command->info('Importing units...');
        
        $units = $this->parser->getUniqueUnits();
        $count = 0;
        
        foreach ($units as $unitName) {
            if (!empty($unitName)) {
                $abbreviation = strtoupper(substr($unitName, 0, 3));
                Unit::updateOrCreate(
                    ['unit_name' => $unitName],
                    [
                        'abbreviation' => $abbreviation,
                        'description' => 'Unit for ' . $unitName,
                        'is_active' => true
                    ]
                );
                $count++;
            }
        }
        
        $this->command->info("Units imported: {$count}");
    }

    private function importSuppliers()
    {
        $this->command->info('Importing suppliers...');
        
        $suppliers = $this->parser->parseSuppliers();
        $count = 0;
        
        foreach ($suppliers as $supplierData) {
            Supplier::updateOrCreate(
                ['name' => $supplierData['name']],
                [
                    'phone' => $supplierData['phone'],
                    'whatsapp' => $supplierData['whatsapp'],
                    'address' => $supplierData['address'],
                    'latitude' => $supplierData['latitude'],
                    'longitude' => $supplierData['longitude'],
                    'instagram' => $supplierData['instagram'],
                    'shopee_url' => $supplierData['shopee_url'],
                    'tokopedia_url' => $supplierData['tokopedia_url'],
                    'is_active' => true,
                ]
            );
            $count++;
        }
        
        $this->command->info("Suppliers imported: {$count}");
    }

    private function importRawMaterials()
    {
        $this->command->info('Importing raw materials...');
        
        $rawMaterials = $this->parser->parseRawMaterials();
        $count = 0;
        
        foreach ($rawMaterials as $data) {
            if (!empty($data['name']) && !empty($data['code'])) {
                // Find or create category
                $category = Category::firstOrCreate(
                    ['name' => $data['category']],
                    ['description' => 'Auto-created for ' . $data['category']]
                );
                
                // Find or create unit
                $unit = Unit::firstOrCreate(
                    ['name' => $data['unit']],
                    [
                        'code' => strtoupper(substr($data['unit'], 0, 3)),
                        'symbol' => strtolower($data['unit']),
                        'is_active' => true
                    ]
                );
                
                // Find supplier if exists
                $supplier = null;
                if (!empty($data['supplier_name'])) {
                    $supplier = Supplier::where('name', $data['supplier_name'])->first();
                }
                
                RawMaterial::updateOrCreate(
                    ['code' => $data['code']],
                    [
                        'name' => $data['name'],
                        'category_id' => $category->id,
                        'unit_id' => $unit->id,
                        'supplier_id' => $supplier ? $supplier->id : null,
                        'current_stock' => $data['current_stock'] ?? 0,
                        'minimum_stock' => $data['minimum_stock'] ?? 0,
                        'unit_price' => $data['unit_price'] ?? 0,
                        'is_active' => true,
                        'description' => 'Imported from spreadsheet data',
                    ]
                );
                
                $count++;
            }
        }
        
        $this->command->info("Raw materials imported: {$count}");
    }

    private function importSemiFinishedProducts()
    {
        $this->command->info('Importing semi-finished products...');
        
        $semiFinished = $this->parser->parseSemiFinishedProducts();
        $branches = Branch::all();
        $count = 0;
        
        foreach ($semiFinished as $data) {
            if (!empty($data['name']) && !empty($data['code'])) {
                // Find or create category
                $category = Category::firstOrCreate(
                    ['name' => $data['category']],
                    ['description' => 'Auto-created for ' . $data['category']]
                );
                
                // Find or create unit
                $unit = Unit::firstOrCreate(
                    ['name' => $data['unit']],
                    [
                        'code' => strtoupper(substr($data['unit'], 0, 3)),
                        'symbol' => strtolower($data['unit']),
                        'is_active' => true
                    ]
                );
                
                $product = SemiFinishedProduct::updateOrCreate(
                    ['code' => $data['code']],
                    [
                        'name' => $data['name'],
                        'category_id' => $category->id,
                        'unit_id' => $unit->id,
                        'minimum_stock' => $data['minimum_stock'] ?? 0,
                        'stock_quantity' => $data['branch_stock'] ?? 0,
                        'unit_price' => 0, // Will be calculated later
                        'is_active' => true,
                        'description' => 'Imported from spreadsheet data',
                    ]
                );
                
                // Create branch stock (assuming LC02 branch or default to first branch)
                if ($branches->isNotEmpty()) {
                    $branch = $branches->first(); // Use first branch as default
                    SemiFinishedBranchStock::updateOrCreate(
                        [
                            'semi_finished_product_id' => $product->id,
                            'branch_id' => $branch->id,
                        ],
                        [
                            'quantity' => $data['branch_stock'] ?? 0,
                            'minimum_stock' => $data['minimum_stock'] ?? 0,
                        ]
                    );
                }
                
                $count++;
            }
        }
        
        $this->command->info("Semi-finished products imported: {$count}");
    }

    private function importFinishedProducts()
    {
        $this->command->info('Importing finished products...');
        
        $finishedProducts = $this->parser->parseFinishedProducts();
        $branches = Branch::all();
        $count = 0;
        
        foreach ($finishedProducts as $data) {
            if (!empty($data['name']) && !empty($data['code'])) {
                // Find or create category
                $category = Category::firstOrCreate(
                    ['name' => $data['category']],
                    ['description' => 'Auto-created for ' . $data['category']]
                );
                
                // Default unit for finished products (usually pieces)
                $unit = Unit::firstOrCreate(
                    ['name' => 'Pieces'],
                    [
                        'code' => 'PCS',
                        'symbol' => 'pcs',
                        'is_active' => true
                    ]
                );
                
                $product = FinishedProduct::updateOrCreate(
                    ['code' => $data['code']],
                    [
                        'name' => $data['name'],
                        'category_id' => $category->id,
                        'unit_id' => $unit->id,
                        'price' => $data['price'] ?? 0,
                        'production_cost' => 0, // Will be calculated later based on recipes
                        'minimum_stock' => 5, // Default minimum stock
                        'stock_quantity' => 0, // Will be managed via branch stocks
                        'is_active' => true,
                        'description' => 'Imported from spreadsheet data',
                    ]
                );
                
                // Create initial branch stock for all branches
                foreach ($branches as $branch) {
                    FinishedBranchStock::updateOrCreate(
                        [
                            'finished_product_id' => $product->id,
                            'branch_id' => $branch->id,
                        ],
                        [
                            'quantity' => 0, // Initial stock is zero
                            'minimum_stock' => 5, // Default minimum
                            'average_cost' => $data['price'] ?? 0,
                        ]
                    );
                }
                
                $count++;
            }
        }
        
        $this->command->info("Finished products imported: {$count}");
    }
}
