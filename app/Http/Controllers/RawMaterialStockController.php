<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Category;
use Illuminate\Http\Request;

class RawMaterialStockController extends Controller
{
    /**
     * Display raw materials with stock information
     */
    public function index(Request $request)
    {
        // Get raw materials with their current stock
        $rawMaterialsQuery = RawMaterial::where('is_active', true)
            ->with(['unit', 'supplier', 'category']);

        // Map query params to align with other stock pages (backward-compatible)
        $search = $request->input('search', $request->input('q'));
        $stockLevel = $request->input('stock_level', $request->input('stock'));
        $categoryId = $request->input('category_id');

        // Filter by search if provided (name, code, supplier)
        if (!empty($search)) {
            $rawMaterialsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by category if provided
        if (!empty($categoryId)) {
            $rawMaterialsQuery->where('category_id', $categoryId);
        }

        // Filter by stock level: empty (<=0), low (< min), warning ([min, 2x min)), normal (>= 2x min)
        if (!empty($stockLevel)) {
            if ($stockLevel === 'empty') {
                $rawMaterialsQuery->whereRaw('current_stock <= 0');
            } elseif ($stockLevel === 'low') {
                $rawMaterialsQuery->whereRaw('current_stock > 0 AND current_stock < minimum_stock');
            } elseif ($stockLevel === 'warning') {
                $rawMaterialsQuery->whereRaw('current_stock >= minimum_stock AND current_stock < (minimum_stock * 2)');
            } elseif ($stockLevel === 'normal') {
                $rawMaterialsQuery->whereRaw('current_stock >= (minimum_stock * 2)');
            }
        }

        // Order by stock status (low first) then by name
        $rawMaterials = $rawMaterialsQuery->orderByRaw('
            CASE 
                WHEN current_stock <= 0 THEN 0
                WHEN current_stock < minimum_stock THEN 1
                WHEN current_stock < minimum_stock * 2 THEN 2
                ELSE 3
            END, name ASC
        ')->paginate(24);

        // Preserve query string parameters in pagination links
        $rawMaterials->appends($request->query());

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('raw-materials.stock-card', compact('rawMaterials', 'categories'));
    }
}
