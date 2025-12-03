<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\SemiFinishedProduct;
use App\Models\SemiFinishedBranchStock;
use App\Http\Controllers\Concerns\ResolvesBranchContext;

class SemiFinishedStockController extends Controller
{
    use ResolvesBranchContext;

    /**
     * Unified index: shows semi-finished stock for the active branch context.
     * - Staff: restricted to their branch.
     * - Super Admin: uses selected branch from URL/session; if none, aggregates all production centers (legacy behavior).
     */
    public function index(Request $request)
    {
        // Resolve active branch context
        $selectedBranch = $this->resolveSelectedBranch($request);
        $contextBranch = $selectedBranch;

        $query = SemiFinishedProduct::query()
            ->select('semi_finished_products.*')
            ->selectSub(function ($sub) use ($contextBranch) {
                $sub->from('semi_finished_branch_stocks as sfbs')->selectRaw('COALESCE(SUM(sfbs.quantity), 0)')->whereColumn('sfbs.semi_finished_product_id', 'semi_finished_products.id');

                if ($contextBranch) {
                    $sub->where('sfbs.branch_id', $contextBranch->id);
                } else {
                    $sub->leftJoin('branches as b', 'b.id', '=', 'sfbs.branch_id')->where('b.type', 'production');
                }
            }, 'center_stock')
            ->where('semi_finished_products.is_active', true)
            ->with(['unit', 'category']);

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('semi_finished_products.name', 'like', "%{$search}%")->orWhere('semi_finished_products.code', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('semi_finished_products.category_id', $request->integer('category_id'));
        }

        // Filter by stock level on computed alias
        if ($request->filled('stock_level')) {
            $stockLevel = $request->input('stock_level');
            switch ($stockLevel) {
                case 'low':
                    $query->havingRaw('center_stock > 0 AND center_stock < minimum_stock');
                    break;
                case 'warning':
                    $query->havingRaw('center_stock >= minimum_stock AND center_stock < (minimum_stock * 2)');
                    break;
                case 'normal':
                    $query->havingRaw('center_stock >= (minimum_stock * 2)');
                    break;
                case 'empty':
                    $query->havingRaw('center_stock = 0');
                    break;
            }
        }

        // Order: empty -> low -> warning -> normal, then name
        $semiFinishedProducts = $query
            ->orderByRaw(
                '
            CASE
                WHEN center_stock = 0 THEN 1
                WHEN center_stock < minimum_stock THEN 2
                WHEN center_stock < minimum_stock * 2 THEN 3
                ELSE 4
            END, semi_finished_products.name ASC
        ',
            )
            ->paginate(24);

        // Categories for filter dropdown
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        return view('semi-finished-stock.index', compact('semiFinishedProducts', 'categories', 'selectedBranch'));
    }

    /**
     * Show details for a semi-finished product (relations only; view computes context stock).
     */
    public function show(Request $request, SemiFinishedProduct $semiFinishedProduct)
    {
        $semiFinishedProduct->load(['unit', 'category']);
        $selectedBranch = $this->resolveSelectedBranch($request);
        return view('semi-finished-stock.show', compact('semiFinishedProduct', 'selectedBranch'));
    }

    /**
     * Adjust stock for the active branch context.
     * - Staff: can only adjust their own branch.
     * - Super Admin: adjusts the currently selected branch (from request/session).
     */
    public function adjustStock(Request $request, SemiFinishedProduct $semiFinishedProduct)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,reduce,set',
            'quantity' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'branch_id' => 'nullable|integer|exists:branches,id',
        ]);

        $user = Auth::user();

        // Determine target branch for adjustment
        $targetBranch = null;
        if ($user && !$user->hasRole('Super Admin')) {
            $targetBranch = $user->branch; // enforce restriction
        } else {
            $targetBranchId = $request->input('branch_id', session('selected_dashboard_branch'));
            if ($targetBranchId) {
                $targetBranch = Branch::find($targetBranchId);
            }
        }

        // Fallback to managing branch or first production if none determined
        if (!$targetBranch) {
            $fallbackBranchId = $semiFinishedProduct->managing_branch_id ?: Branch::production()->value('id');
            $targetBranch = $fallbackBranchId ? Branch::find($fallbackBranchId) : null;
        }

        if (!$targetBranch) {
            return redirect()->back()->with('error', 'Tidak ada cabang yang valid untuk penyesuaian stok.');
        }

        // Create or fetch stock record for this branch-product
        $branchStock = SemiFinishedBranchStock::firstOrCreate(
            [
                'branch_id' => $targetBranch->id,
                'semi_finished_product_id' => $semiFinishedProduct->id,
            ],
            [
                'quantity' => 0,
            ],
        );

        $oldStock = (float) $branchStock->quantity;
        $newStock = $oldStock;

        $type = $request->input('adjustment_type');
        switch ($type) {
            case 'add':
                $branchStock->updateStock($request->input('quantity'), null, 'add');
                $newStock = $oldStock + (float) $request->input('quantity');
                break;
            case 'reduce':
                if ($request->input('quantity') > $oldStock) {
                    return redirect()->back()->with('error', 'Jumlah pengurangan tidak boleh lebih besar dari stok saat ini.');
                }
                $branchStock->updateStock($request->input('quantity'), null, 'subtract');
                $newStock = $oldStock - (float) $request->input('quantity');
                break;
            case 'set':
                $branchStock->updateStock($request->input('quantity'), null, 'set');
                $newStock = (float) $request->input('quantity');
                break;
        }

        $adjustmentMessages = [
            'add' => 'Stok berhasil ditambahkan',
            'reduce' => 'Stok berhasil dikurangi',
            'set' => 'Stok berhasil diatur ulang',
        ];

        return redirect()
            ->route('semi-finished-stock.index', ['branch_id' => $targetBranch->id])
            ->with('success', $adjustmentMessages[$type] . '. Stok ' . $semiFinishedProduct->name . ' sekarang: ' . number_format($newStock, 3));
    }
}
