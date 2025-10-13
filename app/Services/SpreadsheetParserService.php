<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class SpreadsheetParserService
{
    private $dataPath;

    public function __construct()
    {
        $this->dataPath = base_path('data_spreadsheet');
    }

    /**
     * Parse suppliers from supplier.md
     */
    public function parseSuppliers()
    {
        $filePath = $this->dataPath . '/supplier.md';
        
        if (!File::exists($filePath)) {
            return [];
        }

        $content = File::get($filePath);
        $lines = explode("\n", $content);
        $suppliers = [];

        foreach ($lines as $line) {
            if (strpos($line, '|') !== false && !$this->isHeaderLine($line)) {
                $columns = array_map('trim', explode('|', $line));
                
                if (count($columns) >= 4 && !empty($columns[3])) {
                    $suppliers[] = [
                        'name' => $columns[3], // Supplier
                        'phone' => $columns[4] ?? null, // Telepon
                        'whatsapp' => $columns[5] ?? null, // WhatsApp
                        'instagram' => $columns[6] ?? null, // Instagram
                        'address' => $columns[7] ?? null, // Link Alamat
                        'latitude' => $this->parseFloat($columns[1] ?? null), // Lintang
                        'longitude' => $this->parseFloat($columns[2] ?? null), // Bujur
                        'shopee_url' => $columns[8] ?? null, // Shopee
                        'tokopedia_url' => $columns[9] ?? null, // Tokopedia
                    ];
                }
            }
        }

        return array_filter($suppliers, function($supplier) {
            return !empty($supplier['name']) && $supplier['name'] !== 'Supplier';
        });
    }

    /**
     * Parse raw materials from raw_materials.md and raw_material_supplier.md
     */
    public function parseRawMaterials()
    {
        $materialsFile = $this->dataPath . '/raw_materials.md';
        $suppliersFile = $this->dataPath . '/raw_material_supplier.md';
        
        $basicData = $this->parseRawMaterialsBasic($materialsFile);
        $supplierData = $this->parseRawMaterialsWithSuppliers($suppliersFile);
        
        // Merge the data
        $merged = [];
        foreach ($basicData as $code => $item) {
            $merged[$code] = array_merge($item, $supplierData[$code] ?? []);
        }
        
        return array_values($merged);
    }

    private function parseRawMaterialsBasic($filePath)
    {
        if (!File::exists($filePath)) {
            return [];
        }

        $content = File::get($filePath);
        $lines = explode("\n", $content);
        $materials = [];

        foreach ($lines as $line) {
            if (strpos($line, '|') !== false && !$this->isHeaderLine($line)) {
                $columns = array_map('trim', explode('|', $line));
                
                if (count($columns) >= 7 && !empty($columns[1])) {
                    $code = $columns[1]; // ID Supply
                    $materials[$code] = [
                        'code' => $code,
                        'category' => $columns[2], // Kategori
                        'name' => $columns[3], // Nama Bahan
                        'current_stock' => $this->parseDecimal($columns[4]), // Stok Terkini
                        'unit' => $columns[5], // Satuan
                        'initial_stock' => $this->parseDecimal($columns[6]), // Stok Awal Bulan
                        'minimum_stock' => $this->parseDecimal($columns[7]), // Ambang Batas
                    ];
                }
            }
        }

        return $materials;
    }

    private function parseRawMaterialsWithSuppliers($filePath)
    {
        if (!File::exists($filePath)) {
            return [];
        }

        $content = File::get($filePath);
        $lines = explode("\n", $content);
        $materials = [];

        foreach ($lines as $line) {
            if (strpos($line, '|') !== false && !$this->isHeaderLine($line)) {
                $columns = array_map('trim', explode('|', $line));
                
                if (count($columns) >= 6 && !empty($columns[1])) {
                    $code = $columns[1]; // ID SUPPLY
                    $materials[$code] = [
                        'supplier_name' => $columns[8] ?? null, // SUPPLIERS 1
                        'unit_price' => $this->parsePrice($columns[9] ?? '0'), // HARGA
                        'purchase_qty' => $this->parseDecimal($columns[4] ?? '0'), // PEMBELIAN Qty
                        'purchase_unit' => $columns[5] ?? null, // PEMBELIAN Satuan
                        'netto_amount' => $this->parseDecimal($columns[6] ?? '0'), // NETTO Jumlah
                        'netto_unit' => $columns[7] ?? null, // NETTO Satuan
                    ];
                }
            }
        }

        return $materials;
    }

    /**
     * Parse semi-finished products from semi_finished.md
     */
    public function parseSemiFinishedProducts()
    {
        $filePath = $this->dataPath . '/semi_finished.md';
        
        if (!File::exists($filePath)) {
            return [];
        }

        $content = File::get($filePath);
        $lines = explode("\n", $content);
        $products = [];

        foreach ($lines as $line) {
            if (strpos($line, '|') !== false && !$this->isHeaderLine($line)) {
                $columns = array_map('trim', explode('|', $line));
                
                if (count($columns) >= 7 && !empty($columns[1]) && !empty($columns[3])) {
                    $products[] = [
                        'code' => $columns[1], // ID Supply
                        'category' => $columns[2], // Kategori
                        'name' => $columns[3], // Nama Bahan
                        'branch_stock' => $this->parseDecimal($columns[4]), // Stok Terkini LC02
                        'unit' => $columns[5], // Satuan
                        'initial_stock' => $this->parseDecimal($columns[6]), // Data Awal Bulan
                        'minimum_stock' => $this->parseDecimal($columns[7]), // Ambang Batas
                    ];
                }
            }
        }

        return array_filter($products, function($product) {
            return !empty($product['name']) && $product['name'] !== 'Nama Bahan';
        });
    }

    /**
     * Parse finished products from finished_product.md
     */
    public function parseFinishedProducts()
    {
        $filePath = $this->dataPath . '/finished_product.md';
        
        if (!File::exists($filePath)) {
            return [];
        }

        $content = File::get($filePath);
        $lines = explode("\n", $content);
        $products = [];

        foreach ($lines as $line) {
            if (strpos($line, '|') !== false && !$this->isHeaderLine($line)) {
                $columns = array_map('trim', explode('|', $line));
                
                if (count($columns) >= 4 && !empty($columns[1]) && !empty($columns[3])) {
                    $products[] = [
                        'code' => $columns[1], // ID
                        'category' => $columns[2], // KATEGORI
                        'name' => $columns[3], // PRODUK
                        'price' => $this->parsePrice($columns[4]), // Offline
                    ];
                }
            }
        }

        return array_filter($products, function($product) {
            return !empty($product['name']) && 
                   $product['name'] !== 'PRODUK' && 
                   $product['category'] !== '#N/A' &&
                   !empty($product['code']);
        });
    }

    /**
     * Helper function to check if line is a header
     */
    private function isHeaderLine($line)
    {
        return strpos($line, '---') !== false || 
               strpos($line, 'ID Supply') !== false ||
               strpos($line, 'ID SUPPLY') !== false ||
               strpos($line, 'Lintang') !== false ||
               strpos($line, 'KATEGORI') !== false;
    }

    /**
     * Helper function to parse price from "Rp13.000" format to numeric
     */
    private function parsePrice($priceString)
    {
        if (empty($priceString)) return 0;
        
        // Remove "Rp", spaces, and dots, convert to integer
        $cleaned = preg_replace('/[Rp\s\.]/', '', $priceString);
        $cleaned = str_replace(',', '', $cleaned);
        return (float) $cleaned;
    }

    /**
     * Helper function to parse decimal values like "7 3/5" to decimal
     */
    private function parseDecimal($value)
    {
        if (empty($value)) return 0;
        
        // Handle fraction format like "7 3/5"
        if (strpos($value, '/') !== false) {
            $parts = explode(' ', trim($value));
            $whole = (float) $parts[0];
            if (isset($parts[1]) && strpos($parts[1], '/') !== false) {
                $fraction = explode('/', $parts[1]);
                if (count($fraction) == 2 && $fraction[1] != 0) {
                    $decimal = $fraction[0] / $fraction[1];
                    return $whole + $decimal;
                }
            }
        }
        
        return (float) $value;
    }

    /**
     * Helper function to parse float values
     */
    private function parseFloat($value)
    {
        if (empty($value)) return null;
        return (float) $value;
    }

    /**
     * Get unique categories from all data sources
     */
    public function getUniqueCategories()
    {
        $categories = collect();
        
        // From raw materials
        $rawMaterials = $this->parseRawMaterials();
        foreach ($rawMaterials as $item) {
            if (!empty($item['category'])) {
                $categories->push($item['category']);
            }
        }
        
        // From semi-finished products
        $semiFinished = $this->parseSemiFinishedProducts();
        foreach ($semiFinished as $item) {
            if (!empty($item['category'])) {
                $categories->push($item['category']);
            }
        }
        
        // From finished products
        $finished = $this->parseFinishedProducts();
        foreach ($finished as $item) {
            if (!empty($item['category'])) {
                $categories->push($item['category']);
            }
        }
        
        return $categories->unique()->values()->toArray();
    }

    /**
     * Get unique units from all data sources
     */
    public function getUniqueUnits()
    {
        $units = collect();
        
        // From raw materials
        $rawMaterials = $this->parseRawMaterials();
        foreach ($rawMaterials as $item) {
            if (!empty($item['unit'])) {
                $units->push($item['unit']);
            }
            if (!empty($item['purchase_unit'])) {
                $units->push($item['purchase_unit']);
            }
            if (!empty($item['netto_unit'])) {
                $units->push($item['netto_unit']);
            }
        }
        
        // From semi-finished products
        $semiFinished = $this->parseSemiFinishedProducts();
        foreach ($semiFinished as $item) {
            if (!empty($item['unit'])) {
                $units->push($item['unit']);
            }
        }
        
        return $units->unique()->values()->toArray();
    }
}
