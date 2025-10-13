<?php

namespace App\Http\Controllers;

use App\Models\SalesPackage;
use App\Models\SalesPackageItem;
use App\Models\FinishedProduct;
use App\Models\FinishedBranchStock;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SalesPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SalesPackage::with(['packageItems.finishedProduct', 'creator', 'category']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }
        
        // Category filter
        if ($request->filled('category')) {
            $categoryId = $request->category;
            $query->where('category_id', $categoryId);
        }
        
        $salesPackages = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('sales-packages.index', compact('salesPackages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $finishedProducts = FinishedProduct::with(['unit', 'category'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return view('sales-packages.create', compact('finishedProducts', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'category_name' => 'required|string',
            'base_price' => 'nullable|numeric|min:0',
            'final_price' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'additional_charge' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'items' => 'required|array|min:1',
            'items.*.finished_product_id' => 'required|exists:finished_products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ], [
            'name.required' => 'Nama paket harus diisi.',
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'discount_amount.numeric' => 'Diskon (Rp) harus berupa angka.',
            'discount_amount.min' => 'Diskon (Rp) tidak boleh bernilai negatif.',
            'discount_percentage.numeric' => 'Diskon (%) harus berupa angka.',
            'discount_percentage.min' => 'Diskon (%) tidak boleh bernilai negatif.',
            'discount_percentage.max' => 'Diskon (%) maksimal 100%.',
            'additional_charge.numeric' => 'Biaya tambahan harus berupa angka.',
            'additional_charge.min' => 'Biaya tambahan tidak boleh bernilai negatif.',
            'items.required' => 'Setidaknya harus ada 1 produk dalam paket.',
            'items.*.finished_product_id.required' => 'Produk harus dipilih.',
            'items.*.finished_product_id.exists' => 'Produk yang dipilih tidak valid.',
            'items.*.quantity.required' => 'Jumlah produk harus diisi.',
            'items.*.quantity.min' => 'Jumlah produk minimal 0.01.',
        ]);

        // Validate discount (either percentage or amount, not both)
        if ($request->filled('discount_percentage') && $request->filled('discount_amount')) {
            return back()->withErrors(['discount' => 'Pilih antara diskon persentase atau nominal, bukan keduanya.'])
                        ->withInput();
        }

        // Additional validation for empty price fields
        if ($request->filled('discount_amount') && !is_numeric($request->discount_amount)) {
            return back()->withErrors(['discount_amount' => 'Diskon (Rp) harus diisi dengan angka.'])
                        ->withInput();
        }
        
        if ($request->filled('discount_percentage') && !is_numeric($request->discount_percentage)) {
            return back()->withErrors(['discount_percentage' => 'Diskon (%) harus diisi dengan angka.'])
                        ->withInput();
        }
        
        if ($request->filled('additional_charge') && !is_numeric($request->additional_charge)) {
            return back()->withErrors(['additional_charge' => 'Biaya tambahan harus diisi dengan angka.'])
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('sales-packages', 'public');
            }

            // Create sales package
            $salesPackage = SalesPackage::create([
                'name' => $request->name,
                'code' => SalesPackage::generateCode(),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'base_price' => $request->base_price ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'discount_percentage' => $request->discount_percentage ?? 0,
                'additional_charge' => $request->additional_charge ?? 0,
                'final_price' => $request->final_price ?? 0,
                'image' => $imagePath,
                'is_active' => true,
                'created_by' => Auth::id()
            ]);

            // Create package items
            $basePrice = 0;
            foreach ($request->items as $item) {
                $finishedProduct = FinishedProduct::find($item['finished_product_id']);
                $unitPrice = $finishedProduct->price;
                $totalPrice = $item['quantity'] * $unitPrice;
                
                SalesPackageItem::create([
                    'sales_package_id' => $salesPackage->id,
                    'finished_product_id' => $item['finished_product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice
                ]);
                
                $basePrice += $totalPrice;
            }

            // Update base price and calculate final price
            $salesPackage->base_price = $basePrice;
            $salesPackage->calculateFinalPrice();
            $salesPackage->save();

            DB::commit();
            
            return redirect()->route('sales-packages.index')
                           ->with('success', 'Paket penjualan berhasil dibuat.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $salesPackage = SalesPackage::with(['packageItems.finishedProduct', 'creator', 'category'])->findOrFail($id);
        
        // Calculate branch availability
        $branches = Branch::all();
        $branchAvailability = [];
        
        foreach ($branches as $branch) {
            $canMake = true;
            $minQuantity = PHP_INT_MAX;
            
            // Check each package item
            foreach ($salesPackage->packageItems as $packageItem) {
                $finishedProduct = $packageItem->finishedProduct;
                
                // Get current stock for this branch
                $currentStock = FinishedBranchStock::where('finished_product_id', $finishedProduct->id)
                    ->where('branch_id', $branch->id)
                    ->sum('quantity');
                    
                $requiredQuantity = $packageItem->quantity;
                
                if ($currentStock < $requiredQuantity) {
                    $canMake = false;
                    $minQuantity = 0;
                    break;
                } else {
                    $possiblePackages = intval($currentStock / $requiredQuantity);
                    $minQuantity = min($minQuantity, $possiblePackages);
                }
            }
            
            $branchAvailability[$branch->id] = [
                'name' => $branch->name,
                'is_available' => $canMake,
                'available_quantity' => $canMake ? $minQuantity : 0
            ];
        }
        
        return view('sales-packages.show', compact('salesPackage', 'branchAvailability'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $salesPackage = SalesPackage::with(['packageItems.finishedProduct', 'category'])->findOrFail($id);
        $finishedProducts = FinishedProduct::with(['unit', 'category'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('sales-packages.edit', compact('salesPackage', 'finishedProducts', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesPackage $salesPackage)
    {
        // COMPREHENSIVE DEBUG: Log method entry
        \Log::info('=== SALES PACKAGE UPDATE METHOD CALLED ===', [
            'timestamp' => now()->toDateTimeString(),
            'package_id' => $salesPackage->id,
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl(),
            'user_id' => auth()->id(),
            'ip_address' => $request->ip()
        ]);
        
        // Debug: Log ALL incoming request data in detail
        \Log::info('COMPLETE REQUEST DATA:', [
            'all_data' => $request->all(),
            'items_count' => is_array($request->get('items')) ? count($request->get('items')) : 0,
            'items_data' => $request->get('items'),
            'has_image' => $request->hasFile('image'),
            'image_info' => $request->hasFile('image') ? [
                'name' => $request->file('image')->getClientOriginalName(),
                'size' => $request->file('image')->getSize(),
                'mime' => $request->file('image')->getMimeType()
            ] : null
        ]);

        // FIXED: Handle both numeric and string indexed items
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'nullable|numeric|min:0',
            'final_price' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'additional_charge' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'items' => 'required|array|min:1',
        ]);
        
        // Custom validation for items array (supports both numeric and string keys)
        foreach ($request->get('items', []) as $key => $item) {
            $request->validate([
                "items.{$key}.finished_product_id" => 'required|exists:finished_products,id',
                "items.{$key}.quantity" => 'required|numeric|min:0.01',
            ]);
        }

        \Log::info('Validation passed successfully');
        
        \Log::info('Starting discount validation...');
        // FIXED: Validate discount - only trigger when BOTH have meaningful non-zero values
        $hasDiscountPercentage = $request->filled('discount_percentage') && (float)$request->discount_percentage > 0;
        $hasDiscountAmount = $request->filled('discount_amount') && (float)$request->discount_amount > 0;
        
        \Log::info('Discount check:', [
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
            'hasDiscountPercentage' => $hasDiscountPercentage,
            'hasDiscountAmount' => $hasDiscountAmount
        ]);
        
        if ($hasDiscountPercentage && $hasDiscountAmount) {
            \Log::warning('Discount validation failed - both meaningful percentage and amount provided');
            return back()->withErrors(['discount' => 'Pilih antara diskon persentase atau nominal, bukan keduanya.'])
                        ->withInput();
        }
        \Log::info('Discount validation passed');

        \Log::info('Starting database transaction...');
        DB::beginTransaction();
        try {
            \Log::info('Inside database transaction try block');
            
            // Handle image upload
            \Log::info('Starting image upload handling...');
            if ($request->hasFile('image')) {
                \Log::info('Image file detected, processing upload...');
                // Delete old image
                if ($salesPackage->image) {
                    \Log::info('Deleting old image: ' . $salesPackage->image);
                    Storage::disk('public')->delete($salesPackage->image);
                }
                \Log::info('Storing new image...');
                $imagePath = $request->file('image')->store('sales-packages', 'public');
                $salesPackage->image = $imagePath;
                \Log::info('Image stored successfully: ' . $imagePath);
            } else {
                \Log::info('No image file uploaded');
            }

            // Update sales package basic info
            \Log::info('Starting sales package basic info update...');
            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'base_price' => (float) ($request->base_price ?? 0),
                'discount_amount' => (float) ($request->discount_amount ?? 0),
                'discount_percentage' => (float) ($request->discount_percentage ?? 0),
                'additional_charge' => (float) ($request->additional_charge ?? 0),
                'final_price' => (float) ($request->final_price ?? 0),
            ];
            \Log::info('Update data prepared:', $updateData);
            
            $salesPackage->update($updateData);
            \Log::info('Sales package basic info updated successfully');

            // Delete existing items and recreate
            \Log::info('Starting to delete existing package items...');
            $deletedCount = $salesPackage->packageItems()->delete();
            \Log::info('Deleted ' . $deletedCount . ' existing package items');

            // Create new package items
            \Log::info('Starting to create new package items...');
            \Log::info('Items to process:', $request->items);
            $basePrice = 0;
            $itemIndex = 0;
            foreach ($request->items as $key => $item) {
                \Log::info("Processing item {$itemIndex} (key: {$key}):", $item);
                
                $finishedProduct = FinishedProduct::find($item['finished_product_id']);
                if (!$finishedProduct) {
                    \Log::error('Finished product not found for ID: ' . $item['finished_product_id']);
                    throw new \Exception('Finished product not found');
                }
                \Log::info('Found finished product:', ['id' => $finishedProduct->id, 'name' => $finishedProduct->name, 'price' => $finishedProduct->price]);
                
                $unitPrice = $finishedProduct->price;
                $totalPrice = $item['quantity'] * $unitPrice;
                \Log::info('Calculated prices:', ['unit_price' => $unitPrice, 'quantity' => $item['quantity'], 'total_price' => $totalPrice]);
                
                $packageItemData = [
                    'sales_package_id' => $salesPackage->id,
                    'finished_product_id' => $item['finished_product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice
                ];
                \Log::info('Creating package item with data:', $packageItemData);
                
                $newPackageItem = SalesPackageItem::create($packageItemData);
                \Log::info('Package item created successfully with ID: ' . $newPackageItem->id);
                
                $basePrice += $totalPrice;
                $itemIndex++;
            }
            \Log::info('All package items created. Total base price: ' . $basePrice);

            // Update base price and calculate final price
            $salesPackage->base_price = $basePrice;
            $salesPackage->calculateFinalPrice();
            $salesPackage->save();

            \Log::info('Sales Package Updated Successfully:', [
                'package_id' => $salesPackage->id,
                'new_name' => $salesPackage->name,
                'new_base_price' => $salesPackage->base_price,
                'new_final_price' => $salesPackage->final_price,
                'items_count' => $salesPackage->packageItems()->count()
            ]);

            DB::commit();
            
            \Log::info('Database transaction committed successfully');
            
            return redirect()->route('sales-packages.index')
                           ->with('success', 'Paket penjualan berhasil diperbarui.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Sales Package Update Failed:', [
                'package_id' => $salesPackage->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesPackage $salesPackage)
    {
        try {
            // Delete image if exists
            if ($salesPackage->image) {
                Storage::disk('public')->delete($salesPackage->image);
            }
            
            $salesPackage->delete();
            
            return redirect()->route('sales-packages.index')
                           ->with('success', 'Paket penjualan berhasil dihapus.');
                           
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle the active status of the sales package.
     */
    public function toggleStatus(SalesPackage $salesPackage)
    {
        try {
            $salesPackage->is_active = !$salesPackage->is_active;
            $salesPackage->save();
            
            $status = $salesPackage->is_active ? 'diaktifkan' : 'dinonaktifkan';
            
            return redirect()->back()
                           ->with('success', "Paket penjualan berhasil {$status}.");
                           
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Get available sales packages for a specific branch (AJAX)
     */
    public function getPackagesForBranch(Request $request)
    {
        $branchId = $request->branch_id;
        
        $packages = SalesPackage::with(['packageItems.finishedProduct'])
            ->where('is_active', true)
            ->get()
            ->map(function($package) use ($branchId) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'code' => $package->code,
                    'final_price' => $package->final_price,
                    'available_quantity' => $package->getAvailableQuantityInBranch($branchId),
                    'is_available' => $package->isAvailableInBranch($branchId),
                    'description' => $package->description,
                    'components' => $package->packageItems->map(function($item) {
                        return [
                            'name' => $item->finishedProduct->name,
                            'quantity' => $item->quantity,
                            'unit' => $item->finishedProduct->unit->unit_name ?? 'pcs'
                        ];
                    })
                ];
            })
            ->filter(function($package) {
                return $package['is_available'];
            })
            ->values();
            
        return response()->json([
            'success' => true,
            'packages' => $packages
        ]);
    }
}
