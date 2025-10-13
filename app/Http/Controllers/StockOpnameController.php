<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGeneratorHelper;
use App\Http\Controllers\Concerns\ResolvesBranchContext;
use App\Models\Branch;
use App\Models\RawMaterial;
use App\Models\SemiFinishedBranchStock;
use App\Models\SemiFinishedProduct;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOpnameController extends Controller
{
    use ResolvesBranchContext;

    public function index(Request $request)
    {
        $opnames = StockOpname::with(['branch'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('stock-opnames.index', compact('opnames'));
    }

    public function create(Request $request)
    {
        $branches = Branch::active()->orderBy('name')->get();
        return view('stock-opnames.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $messages = [
            'required' => ':attribute wajib diisi.',
            'in' => ':attribute tidak valid.',
            'exists' => ':attribute tidak ditemukan.',
        ];
        $attributes = [
            'product_type' => 'Jenis Produk',
            'branch_id' => 'Cabang',
        ];
        $data = $request->validate([
            'product_type' => 'required|in:raw,semi',
            'branch_id' => 'nullable|exists:branches,id',
            'notes' => 'nullable|string|max:1000',
        ], $messages, $attributes);

        // For semi-finished, branch is required
        if ($data['product_type'] === 'semi' && empty($data['branch_id'])) {
            return back()->withErrors(['branch_id' => 'Cabang wajib dipilih untuk produk setengah jadi.'])->withInput();
        }

        $opnameNumber = CodeGeneratorHelper::generateDailySequentialCode('SO', null, StockOpname::class, 'opname_number');

        $stockOpname = StockOpname::create([
            'opname_number' => $opnameNumber,
            'status' => 'draft',
            'product_type' => $data['product_type'],
            'branch_id' => $data['product_type'] === 'semi' ? $data['branch_id'] : null,
            'user_id' => Auth::id(),
            'notes' => $data['notes'] ?? null,
        ]);

        // Snapshot items
        if ($data['product_type'] === 'raw') {
            $materials = RawMaterial::active()->with('unit')->orderBy('name')->get();
            $items = $materials->map(function ($m) use ($stockOpname) {
                $unitAbbr = optional($m->unit)->abbreviation;
                $systemQty = (int) $m->current_stock;
                $unitCost = (float) $m->unit_price;
                return [
                    'stock_opname_id' => $stockOpname->id,
                    'item_type' => 'raw',
                    'item_id' => $m->id,
                    'item_code' => $m->code,
                    'item_name' => $m->name,
                    'unit_abbr' => $unitAbbr,
                    'system_quantity' => $systemQty,
                    'real_quantity' => 0,
                    'difference' => 0,
                    'status' => 'matched',
                    'unit_cost' => $unitCost,
                    'value_difference' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();
            if (!empty($items)) {
                StockOpnameItem::insert($items);
            }
        } else {
            $branchId = (int) $data['branch_id'];
            $products = SemiFinishedProduct::active()->with('unit')->orderBy('name')->get();
            // Preload branch stock map to avoid N+1
            $stocks = SemiFinishedBranchStock::where('branch_id', $branchId)->get()->keyBy('semi_finished_product_id');
            $items = $products->map(function ($p) use ($stockOpname, $stocks) {
                $unitAbbr = optional($p->unit)->abbreviation;
                $stock = $stocks->get($p->id);
                $systemQty = $stock ? (int) $stock->quantity : 0;
                $unitCost = (float) $p->production_cost;
                return [
                    'stock_opname_id' => $stockOpname->id,
                    'item_type' => 'semi',
                    'item_id' => $p->id,
                    'item_code' => $p->code,
                    'item_name' => $p->name,
                    'unit_abbr' => $unitAbbr,
                    'system_quantity' => $systemQty,
                    'real_quantity' => 0,
                    'difference' => 0,
                    'status' => 'matched',
                    'unit_cost' => $unitCost,
                    'value_difference' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();
            if (!empty($items)) {
                StockOpnameItem::insert($items);
            }
        }

        return redirect()->route('stock-opnames.edit', $stockOpname)->with('success', 'Draft stok opname berhasil dibuat.');
    }

    public function edit(Request $request, StockOpname $stockOpname)
    {
        $stockOpname->load(['items']);
        return view('stock-opnames.edit', compact('stockOpname'));
    }

    public function update(Request $request, StockOpname $stockOpname)
    {
        $messages = [
            'required' => ':attribute wajib diisi.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'array' => ':attribute tidak valid.',
        ];
        $attributes = [
            'items' => 'Daftar Item',
        ];
        $data = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:stock_opname_items,id',
            'items.*.real_quantity' => 'required|integer|min:0',
        ], $messages, $attributes);

        $stockOpname->notes = $data['notes'] ?? null;
        $stockOpname->save();

        $itemInputs = collect($data['items'])->keyBy('id');

        $items = $stockOpname->items()->whereIn('id', $itemInputs->keys())->get();
        foreach ($items as $item) {
            $item->real_quantity = (int) $itemInputs[$item->id]['real_quantity'];
            $item->recompute();
            $item->save();
        }

        $stockOpname->recalcSummary();

        return back()->with('success', 'Draft stok opname berhasil disimpan.');
    }

    public function submit(Request $request, StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'draft') {
            return redirect()->route('stock-opnames.show', $stockOpname)->with('info', 'Stok opname sudah disubmit.');
        }

        // Ensure all items have been computed at least once
        $items = $stockOpname->items;
        foreach ($items as $item) {
            $item->recompute();
            $item->save();
        }

        $stockOpname->status = 'submitted';
        $stockOpname->submitted_at = now();
        $stockOpname->recalcSummary();

        return redirect()->route('stock-opnames.show', $stockOpname)->with('success', 'Stok opname berhasil disubmit.');
    }

    public function show(Request $request, StockOpname $stockOpname)
    {
        $stockOpname->load('items');
        return view('stock-opnames.show', compact('stockOpname'));
    }
}
