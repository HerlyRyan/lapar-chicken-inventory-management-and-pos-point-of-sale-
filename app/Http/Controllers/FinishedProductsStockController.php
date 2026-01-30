<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\FinishedProduct;
use App\Models\FinishedBranchStock;
use App\Models\SalesPackage;

class FinishedProductsStockController extends Controller
{
    /**
     * Resolve the active/selected branch context based on user role and request/session.
     */
    protected function resolveSelectedBranch(Request $request): ?Branch
    {
        $user = Auth::user();
        if ($user && !$user->hasRole('Super Admin')) {
            return $user->branch; // enforce branch restriction for non super admin
        }

        // Use the same session key as dashboard/BranchComposer to keep branch selection consistent
        $selectedBranchId = $request->input('branch_id', session('selected_dashboard_branch'));
        if ($selectedBranchId) {
            return Branch::active()->find($selectedBranchId);
        }

        return null;
    }

    /**
     * Index: shows finished products stock for the active branch context.
     * - Staff: restricted to their branch.
     * - Super Admin: uses selected branch from URL/session; if none, aggregates all retail branches.
     */
    public function index(Request $request)
    {
        $selectedBranch = $this->resolveSelectedBranch($request);
        $contextBranch = $selectedBranch;

        // Base query
        $query = FinishedProduct::query()->select('finished_products.*');

        $query = FinishedProduct::query()
            ->select('finished_products.*')
            ->selectSub(function ($sub) use ($contextBranch) {
                $sub->from('finished_branch_stocks as fbs')
                    ->selectRaw('COALESCE(SUM(fbs.quantity), 0)')
                    ->whereColumn('fbs.finished_product_id', 'finished_products.id');

                if ($contextBranch) {
                    $sub->where('fbs.branch_id', $contextBranch->id);
                } else {
                    $sub->leftJoin('branches as b', 'b.id', '=', 'fbs.branch_id')
                        ->where('b.type', 'production');
                }
            }, 'center_stock')
            ->where('finished_products.is_active', true)
            ->with(['unit', 'category']);

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('finished_products.name', 'like', "%{$search}%")
                    ->orWhere('finished_products.code', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('finished_products.category_id', $request->integer('category_id'));
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
        $finishedProducts = $query->orderByRaw('
            CASE 
                WHEN center_stock = 0 THEN 1
                WHEN center_stock < minimum_stock THEN 2
                WHEN center_stock < minimum_stock * 2 THEN 3
                ELSE 4
            END, finished_products.name ASC
        ')->paginate(24);

        // Categories for filter dropdown
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        return view('finished-products-stock.index', compact('finishedProducts', 'categories', 'selectedBranch'));
    }

    /**
     * Show details for a finished product (view computes context stock).
     */
    public function show(Request $request, FinishedProduct $finishedProduct)
    {
        $finishedProduct->load(['unit', 'category']);
        $selectedBranch = $this->resolveSelectedBranch($request);
        return view('finished-products-stock.show', compact('finishedProduct', 'selectedBranch'));
    }

    /**
     * Adjust stock for the active branch context.
     * - Staff: can only adjust their own branch.
     * - Super Admin: adjusts the currently selected branch (from request/session).
     */
    public function adjustStock(Request $request, FinishedProduct $finishedProduct)
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

        // Fallback to managing branch or first retail branch if none determined
        if (!$targetBranch) {
            $fallbackBranchId = $finishedProduct->managing_branch_id ?: Branch::retail()->value('id');
            $targetBranch = $fallbackBranchId ? Branch::find($fallbackBranchId) : null;
        }

        if (!$targetBranch) {
            return redirect()->back()->with('error', 'Tidak ada cabang yang valid untuk penyesuaian stok.');
        }

        // Create or fetch stock record for this branch-product
        $branchStock = FinishedBranchStock::firstOrCreate([
            'branch_id' => $targetBranch->id,
            'finished_product_id' => $finishedProduct->id,
        ], [
            'quantity' => 0,
        ]);

        $oldStock = (float) $branchStock->quantity;
        $newStock = $oldStock;

        $type = $request->input('adjustment_type');
        $qty = (float) $request->input('quantity');
        $notes = $request->input('reason') . ($request->filled('notes') ? (' - ' . $request->input('notes')) : '');

        switch ($type) {
            case 'add':
                $branchStock->updateStock('in', $qty, $notes, optional($user)->id);
                $newStock = $branchStock->fresh()->quantity;
                break;
            case 'reduce':
                if ($qty > $oldStock) {
                    return redirect()->back()->with('error', 'Jumlah pengurangan tidak boleh lebih besar dari stok saat ini.');
                }
                $branchStock->updateStock('out', $qty, $notes, optional($user)->id);
                $newStock = $branchStock->fresh()->quantity;
                break;
            case 'set':
                // hitung difference lalu panggil updateStock hanya dengan diff (movement amount)
                $diff = $qty - $oldStock;
                if ($diff > 0) {
                    $branchStock->updateStock('in', $diff, $notes, optional($user)->id);
                } elseif ($diff < 0) {
                    $branchStock->updateStock('out', abs($diff), $notes, optional($user)->id);
                }
                $newStock = $branchStock->fresh()->quantity;
                break;
        }


        $adjustmentMessages = [
            'add' => 'Stok berhasil ditambahkan',
            'reduce' => 'Stok berhasil dikurangi',
            'set' => 'Stok berhasil diatur ulang',
        ];

        return redirect()
            ->route('finished-products-stock.index', ['branch_id' => $targetBranch->id])
            ->with('success', $adjustmentMessages[$type] . '. Stok ' . $finishedProduct->name . ' sekarang: ' . number_format($newStock, 3));
    }

    public function items(Branch $branch)
    {
        // ===== PRODUCTS =====
        $branchStocks = FinishedBranchStock::with(['finishedProduct.category'])
            ->where('branch_id', $branch->id)
            ->get();

        $products = $branchStocks->map(fn($item) => [
            'id'       => $item->finished_product_id,
            'stock_id' => $item->id,
            'name'     => $item->finishedProduct->name,
            'price'    => (float) $item->finishedProduct->price,
            'stock'    => (float) $item->quantity,
            'category' => $item->finishedProduct->category?->code,
        ]);

        // ===== PACKAGES =====
        $packages = SalesPackage::with(['packageItems.finishedProduct', 'category'])
            ->where('is_active', true)
            ->get()
            ->filter(fn($pkg) => $pkg->isAvailableInBranch($branch->id))
            ->map(fn($pkg) => [
                'id'       => $pkg->id,
                'name'     => $pkg->name,
                'price'    => (float) $pkg->final_price,
                'stock'    => $pkg->getAvailableQuantityInBranch($branch->id),
                'category' => $pkg->category?->code,
            ]);

        return response()->json([
            'products' => $products->values()->toArray(),
            'packages' => $packages->values()->toArray(),
        ]);
    }
}
