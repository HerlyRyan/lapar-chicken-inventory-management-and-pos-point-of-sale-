<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SemiFinishedProduct;
use App\Models\Branch;
use App\Models\SemiFinishedBranchStock;
use App\Models\SemiFinishedDistribution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SemiFinishedDistributionController extends Controller
{
    /**
     * Display a listing of semi-finished distributions
     */
    public function index(Request $request)
    {
        $query = SemiFinishedDistribution::with(['sentBy', 'targetBranch', 'semiFinishedProduct.unit', 'handledBy'])
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by target branch
        if ($request->filled('branch_id')) {
            $query->where('target_branch_id', $request->branch_id);
        }

        // Search by distribution code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('distribution_code', 'like', "%{$search}%");
        }

        $distributions = $query->paginate(15);

        // Get branches for filter dropdown
        $branches = Branch::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('semi-finished-distributions.index', compact('distributions', 'branches'));
    }

    /**
     * Branch inbox: list incoming (sent) distributions for the active/selected branch
     */
    public function inbox(Request $request)
    {
        $user = Auth::user();

        // Determine active branch
        $branchId = null;
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) {
            // Super Admin can switch branch via session or URL param
            $branchId = $request->input('branch_id', session('selected_branch_id'));
        } else {
            // Staff uses assigned branch
            $branchId = $user?->branch_id;
        }

        if (!$branchId) {
            return redirect()->route('dashboard')
                ->with('error', 'Silakan pilih cabang terlebih dahulu untuk melihat kotak masuk distribusi.');
        }

        $query = SemiFinishedDistribution::with(['sentBy', 'targetBranch', 'semiFinishedProduct.unit'])
            ->where('status', 'sent')
            ->where('target_branch_id', $branchId)
            ->orderBy('created_at', 'desc');

        // Optional search by distribution code
        if ($request->filled('search')) {
            $query->where('distribution_code', 'like', '%' . $request->search . '%');
        }

        $distributions = $query->paginate(15);
        $branch = Branch::find($branchId);

        return view('semi-finished-distributions.inbox', compact('distributions', 'branch', 'branchId'));
    }

    /**
     * Show the form for creating a new distribution
     */
    public function create(Request $request)
    {
        // Selected target branch from query or old input
        $selectedBranchId = $request->input('branch_id');
        $preselectProductId = $request->input('product');

        // Current header-selected branch (from BranchContext middleware)
        $currentBranchId = app()->bound('current_branch_id') ? app('current_branch_id') : null;

        // Do not allow preselecting destination equal to current header branch
        if ($selectedBranchId && $currentBranchId && (int)$selectedBranchId === (int)$currentBranchId) {
            $selectedBranchId = null;
        }

        // Determine default production center branch for stock lookup
        $defaultCenterBranchId = Branch::production()->value('id');

        // Compute stock for the CENTER/PRODUCTION branch (business source of distribution)
        // Expose as center_stock to match backend validation in store()
        $semiFinishedProducts = SemiFinishedProduct::query()
            ->select('semi_finished_products.*')
            ->selectRaw(
                'COALESCE((
                    SELECT s.quantity FROM semi_finished_branch_stocks s
                    WHERE s.semi_finished_product_id = semi_finished_products.id
                      AND s.branch_id = ?
                    LIMIT 1
                ), 0) as center_stock',
                [$defaultCenterBranchId]
            )
            ->where(function ($q) use ($preselectProductId) {
                $q->where('semi_finished_products.is_active', true);
                if ($preselectProductId) {
                    $q->orWhere('semi_finished_products.id', (int) $preselectProductId);
                }
            })
            ->with('unit')
            ->orderBy('semi_finished_products.name')
            ->get();

        $branches = Branch::where('is_active', true)
            ->when($currentBranchId, function ($q) use ($currentBranchId) {
                $q->where('id', '!=', $currentBranchId);
            })
            ->orderBy('name')
            ->get();

        // Compute lock flags using BranchContext + session context
        $currentBranch = app()->bound('current_branch') ? app('current_branch') : null;
        $currentBranchId = app()->bound('current_branch_id') ? app('current_branch_id') : null;
        // Overview mode when no current branch is selected (from BranchContext)
        $isOverview = empty($currentBranchId);
        $isNotProduction = !$isOverview && ($currentBranch && ($currentBranch->type !== 'production'));
        $isLocked = $isOverview || $isNotProduction;

        return view('semi-finished-distributions.create', compact(
            'semiFinishedProducts',
            'branches',
            'selectedBranchId',
            'currentBranch',
            'currentBranchId',
            'isOverview',
            'isNotProduction',
            'isLocked'
        ));
    }

    /**
     * Store a newly created distribution
     */
    public function store(Request $request)
    {
        // Prevent sending while in overview (no branch selected in header)
        $currentBranchId = app()->bound('current_branch_id') ? app('current_branch_id') : null;
        if (!$currentBranchId) {
            return redirect()->back()
                ->with('error', 'Anda saat ini dalam mode "Overview Semua Cabang". Pilih cabang sumber di header terlebih dahulu untuk mengirim distribusi.')
                ->withInput();
        }

        // Enforce that only production branch can send semi-finished distributions
        $currentBranch = app()->bound('current_branch') ? app('current_branch') : null;
        if (!$currentBranch || ($currentBranch->type !== 'production')) {
            return redirect()->back()
                ->with('error', 'Distribusi bahan setengah jadi hanya bisa dikirim oleh cabang pusat produksi. Silakan pilih cabang produksi di header.')
                ->withInput();
        }

        // Destination cannot be the same as the current (source) branch
        if ((int) $request->input('branch_id') === (int) $currentBranchId) {
            return redirect()->back()
                ->with('error', 'Cabang tujuan tidak boleh sama dengan cabang sumber. Silakan pilih cabang tujuan lain.')
                ->withInput();
        }

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'items' => 'required|array|min:1',
            'items.*.semi_finished_product_id' => 'required|exists:semi_finished_products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Ensure at least one production center branch exists
        $defaultCenterBranchId = Branch::production()->value('id');
        if (!$defaultCenterBranchId) {
            return redirect()->back()->with('error', 'Tidak ada cabang produksi untuk mengambil stok.')->withInput();
        }

        // Gather product IDs and load products and center stocks
        $items = collect($request->input('items', []))->map(function ($item) {
            return [
                'semi_finished_product_id' => (int) $item['semi_finished_product_id'],
                'quantity' => (float) $item['quantity'],
                'unit_cost' => isset($item['unit_cost']) ? (float) $item['unit_cost'] : null,
                'notes' => $item['notes'] ?? null,
            ];
        });

        $productIds = $items->pluck('semi_finished_product_id')->unique()->values();
        $products = SemiFinishedProduct::with('unit')->whereIn('id', $productIds)->get()->keyBy('id');

        // Validate stock sufficiency for all items
        $insufficient = [];
        foreach ($items as $item) {
            $p = $products->get($item['semi_finished_product_id']);
            if (!$p) {
                $insufficient[] = 'Produk tidak ditemukan (ID: ' . $item['semi_finished_product_id'] . ')';
                continue;
            }
            $centerBranchId = $defaultCenterBranchId;
            $stock = (float) (SemiFinishedBranchStock::where('branch_id', $centerBranchId)
                ->where('semi_finished_product_id', $item['semi_finished_product_id'])
                ->value('quantity') ?? 0);
            if ($stock < $item['quantity']) {
                $insufficient[] = $p->name . ' - tersedia: ' . number_format((float) $stock, 3) . ' ' . ($p->unit->name ?? 'unit');
            }
        }

        if (!empty($insufficient)) {
            return redirect()->back()
                ->with('error', 'Stok pusat tidak mencukupi untuk: ' . implode('; ', $insufficient))
                ->withInput();
        }

        DB::transaction(function () use ($request, $items, $products, $defaultCenterBranchId) {
            // Lock today's rows to derive a base sequential number safely
            $baseCount = SemiFinishedDistribution::whereDate('created_at', now())
                ->lockForUpdate()
                ->count();
            $seq = $baseCount;

            foreach ($items as $item) {
                $product = $products->get($item['semi_finished_product_id']);
                $unitCost = $item['unit_cost'] ?? (float) ($product->unit_price ?? 0);
                $notes = $item['notes'] ?? $request->input('notes');

                // Create one distribution per item
                $seq++;
                $code = 'SF-' . now()->format('Ymd') . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
                SemiFinishedDistribution::create([
                    'distribution_code' => $code,
                    'sent_by' => Auth::id(),
                    'target_branch_id' => $request->branch_id,
                    'semi_finished_product_id' => $item['semi_finished_product_id'],
                    'quantity_sent' => $item['quantity'],
                    'unit_cost' => $unitCost,
                    'total_cost' => $unitCost * $item['quantity'],
                    'distribution_notes' => $notes,
                    'status' => 'sent',
                ]);

                // Reduce center branch stock per item
                $centerBranchId = $defaultCenterBranchId;
                $centerStock = SemiFinishedBranchStock::firstOrCreate([
                    'branch_id' => $centerBranchId,
                    'semi_finished_product_id' => $item['semi_finished_product_id'],
                ], [
                    'quantity' => 0,
                ]);
                $centerStock->updateStock($item['quantity'], null, 'subtract');
            }
        });

        return redirect()->route('semi-finished-distributions.index')
            ->with('success', 'Distribusi berhasil dikirim. Menunggu konfirmasi dari Kepala Toko.');
    }

    /**
     * Display the specified distribution
     */
    public function show(SemiFinishedDistribution $distribution)
    {
        $distribution->load(['sentBy', 'targetBranch', 'semiFinishedProduct.unit', 'handledBy']);
        
        return view('semi-finished-distributions.show', compact('distribution'));
    }

    /**
     * Accept the distribution (by branch head)
     */
    public function accept(Request $request, SemiFinishedDistribution $distribution)
    {
        // Only allow accepting sent distributions
        if ($distribution->status !== 'sent') {
            return redirect()->route('semi-finished-distributions.inbox', ['branch_id' => $distribution->target_branch_id])
                ->with('error', 'Hanya distribusi dengan status "Dikirim" yang dapat diterima.');
        }

        // Authorization: only Super Admin or users from the target branch (preferably Kepala Toko)
        $user = Auth::user();
        $isSuperAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('Super Admin');
        $sameBranch = $user?->branch_id === $distribution->target_branch_id;
        $roleAllowed = true;
        if ($user && method_exists($user, 'hasRole')) {
            $roleAllowed = $user->hasRole('Kepala Toko') || $user->hasRole('Store Head') || $isSuperAdmin;
        }
        if (!($isSuperAdmin || ($sameBranch && $roleAllowed))) {
            return redirect()->route('semi-finished-distributions.inbox', ['branch_id' => $distribution->target_branch_id])
                ->with('error', 'Anda tidak berhak menerima distribusi untuk cabang ini.');
        }

        $request->validate([
            'response_notes' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () use ($request, $distribution) {
            // Update distribution status
            $distribution->update([
                'status' => 'accepted',
                'handled_by' => Auth::id(),
                'handled_at' => now(),
                'response_notes' => $request->response_notes
            ]);

            // Add to branch semi-finished stock (existing table: semi_finished_branch_stocks)
            $branchStock = SemiFinishedBranchStock::firstOrCreate([
                'branch_id' => $distribution->target_branch_id,
                'semi_finished_product_id' => $distribution->semi_finished_product_id,
            ], [
                'quantity' => 0,
            ]);
            $branchStock->updateStock($distribution->quantity_sent, null, 'add');

            // Log stock movement (if you have stock movement tracking)
            // StockMovement::create([...]);
        });

        return redirect()->route('semi-finished-distributions.inbox', ['branch_id' => $distribution->target_branch_id])
            ->with('success', 'Distribusi berhasil diterima. Bahan setengah jadi telah ditambahkan ke stok cabang.');
    }

    /**
     * Reject the distribution (by branch head)
     */
    public function reject(Request $request, SemiFinishedDistribution $distribution)
    {
        // Only allow rejecting sent distributions
        if ($distribution->status !== 'sent') {
            return redirect()->route('semi-finished-distributions.inbox', ['branch_id' => $distribution->target_branch_id])
                ->with('error', 'Hanya distribusi dengan status "Dikirim" yang dapat ditolak.');
        }

        // Authorization: only Super Admin or users from the target branch (preferably Kepala Toko)
        $user = Auth::user();
        $isSuperAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('Super Admin');
        $sameBranch = $user?->branch_id === $distribution->target_branch_id;
        $roleAllowed = true;
        if ($user && method_exists($user, 'hasRole')) {
            $roleAllowed = $user->hasRole('Kepala Toko') || $user->hasRole('Store Head') || $isSuperAdmin;
        }
        if (!($isSuperAdmin || ($sameBranch && $roleAllowed))) {
            return redirect()->route('semi-finished-distributions.inbox', ['branch_id' => $distribution->target_branch_id])
                ->with('error', 'Anda tidak berhak menolak distribusi untuk cabang ini.');
        }

        $request->validate([
            'response_notes' => 'required|string|max:1000'
        ]);

        DB::transaction(function () use ($request, $distribution) {
            // Update distribution status
            $distribution->update([
                'status' => 'rejected',
                'handled_by' => Auth::id(),
                'handled_at' => now(),
                'response_notes' => $request->response_notes
            ]);

            // Return stock to center branch (since it was reduced when sent)
            $semiFinishedProduct = $distribution->semiFinishedProduct;
            $centerBranchId = Branch::production()->value('id');
            if ($centerBranchId) {
                $centerStock = SemiFinishedBranchStock::firstOrCreate([
                    'branch_id' => $centerBranchId,
                    'semi_finished_product_id' => $semiFinishedProduct->id,
                ], [
                    'quantity' => 0,
                ]);
                $centerStock->updateStock($distribution->quantity_sent, null, 'add');
            }

            // Log stock movement (if you have stock movement tracking)
            // StockMovement::create([...]);
        });

        return redirect()->route('semi-finished-distributions.inbox', ['branch_id' => $distribution->target_branch_id])
            ->with('success', 'Distribusi berhasil ditolak. Stok telah dikembalikan ke pusat produksi.');
    }

    /**
     * Generate unique distribution code
     */
    private function generateDistributionCode(): string
    {
        $date = now()->format('Ymd');
        $count = SemiFinishedDistribution::whereDate('created_at', now())->count() + 1;
        return 'SF-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
