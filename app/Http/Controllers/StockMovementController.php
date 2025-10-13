<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Material;
use App\Models\FinishedProduct;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['material', 'finishedProduct', 'branch', 'user']);

        // Filter by movement type
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        // Filter by category
        if ($request->filled('movement_category')) {
            $query->where('movement_category', $request->movement_category);
        }

        // Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Search by item name or notes
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('material', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('finishedProduct', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }

        $stockMovements = $query->orderBy('processed_at', 'desc')
                               ->orderBy('created_at', 'desc')
                               ->paginate(15);

        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('stock-movements.index', compact('stockMovements', 'branches'));
    }

    public function create()
    {
        $materials = Material::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        $finishedProducts = FinishedProduct::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('stock-movements.create', compact('materials', 'finishedProducts', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:material,finished_product',
            'material_id' => 'required_if:item_type,material|exists:materials,id',
            'finished_product_id' => 'required_if:item_type,finished_product|exists:finished_products,id',
            'branch_id' => 'nullable|exists:branches,id',
            'movement_type' => 'required|in:in,out,transfer,conversion',
            'movement_category' => 'required|in:raw_material,semi_finished,finished_product',
            'quantity_moved' => 'required|numeric|min:0.01',
            'unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            // Get the item (material or finished product)
            $item = null;
            $currentStock = 0;

            if ($request->item_type === 'material') {
                $item = Material::find($request->material_id);
                $currentStock = $item->current_stock ?? 0;
            } else {
                $item = FinishedProduct::find($request->finished_product_id);
                $currentStock = $item->current_stock ?? 0;
            }

            // Calculate new stock
            $quantityMoved = $request->quantity_moved;
            $newStock = $currentStock;

            if ($request->movement_type === 'in') {
                $newStock += $quantityMoved;
            } elseif (in_array($request->movement_type, ['out', 'transfer', 'conversion'])) {
                $newStock -= $quantityMoved;
                
                // Check if sufficient stock
                if ($newStock < 0) {
                    throw new \Exception('Stok tidak mencukupi. Stok saat ini: ' . $currentStock);
                }
            }

            // Create stock movement record
            $movement = StockMovement::create([
                'material_id' => $request->item_type === 'material' ? $request->material_id : null,
                'finished_product_id' => $request->item_type === 'finished_product' ? $request->finished_product_id : null,
                'branch_id' => $request->branch_id,
                'movement_type' => $request->movement_type,
                'movement_category' => $request->movement_category,
                'quantity_before' => $currentStock,
                'quantity_moved' => $quantityMoved,
                'quantity_after' => $newStock,
                'unit_cost' => $request->unit_cost,
                'total_cost' => $request->unit_cost ? ($request->unit_cost * $quantityMoved) : null,
                'reference_type' => 'manual_adjustment',
                'notes' => $request->notes,
                'processed_by' => 1, // Default user ID, you can change this
                'processed_at' => now(),
            ]);

            // Update item stock
            if ($request->item_type === 'material') {
                $item->update(['current_stock' => $newStock]);
            } else {
                $item->update(['current_stock' => $newStock]);
            }
        });

        return redirect()->route('stock-movements.index')->with('success', 'Pergerakan stok berhasil dicatat');
    }

    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['material', 'finishedProduct', 'branch', 'user']);
        return view('stock-movements.show', compact('stockMovement'));
    }

    public function edit(StockMovement $stockMovement)
    {
        $materials = Material::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        $finishedProducts = FinishedProduct::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('stock-movements.edit', compact('stockMovement', 'materials', 'finishedProducts', 'branches'));
    }

    public function update(Request $request, StockMovement $stockMovement)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $stockMovement->update([
            'notes' => $request->notes,
        ]);

        return redirect()->route('stock-movements.index')->with('success', 'Pergerakan stok berhasil diperbarui');
    }

    public function destroy(StockMovement $stockMovement)
    {
        // Note: Deleting stock movements should be done carefully
        // as it affects inventory accuracy
        $stockMovement->delete();
        return redirect()->route('stock-movements.index')->with('success', 'Pergerakan stok berhasil dihapus');
    }

    // API method to get stock by material/product
    public function getStock(Request $request)
    {
        $itemType = $request->get('item_type');
        $itemId = $request->get('item_id');
        
        $stock = 0;
        
        if ($itemType === 'material' && $itemId) {
            $material = Material::find($itemId);
            $stock = $material ? $material->current_stock : 0;
        } elseif ($itemType === 'finished_product' && $itemId) {
            $product = FinishedProduct::find($itemId);
            $stock = $product ? ($product->current_stock ?? 0) : 0;
        }
        
        return response()->json(['stock' => $stock]);
    }
}
