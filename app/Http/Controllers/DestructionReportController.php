<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGeneratorHelper;
use App\Models\Branch;
use App\Models\DestructionReport;
use App\Models\DestructionReportItem;
use App\Models\FinishedBranchStock;
use App\Models\FinishedProduct;
use App\Models\SemiFinishedProduct;
use App\Models\SemiFinishedBranchStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DestructionReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentBranch = $user->branch;

        $query = DestructionReport::with(['branch', 'reportedBy', 'approvedBy'])
            ->orderByDesc('destruction_date');

        if (!$user->isSuperAdmin() && $currentBranch) {
            $query->where('branch_id', $currentBranch->id);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('report_number', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('branch', function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('destruction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('destruction_date', '<=', $request->date_to);
        }

        $destructionReports = $query->paginate(15);

        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('destruction-reports.index', compact('destructionReports', 'branches', 'currentBranch'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $currentBranch = $user->branch;

        $selectedBranch = null;
        if ($request->has('branch_id')) {
            $selectedBranch = Branch::find($request->branch_id);
        }
        $branchToUse = $selectedBranch ?? $currentBranch;

        if ($branchToUse && $branchToUse->type === 'production') {
            return redirect()->route('destruction-reports.index')
                ->with('error', 'Pemusnahan produk tidak dapat dilakukan di Pusat Produksi.');
        }

        $branches = Branch::where('is_active', true)->where('type', 'branch')->orderBy('name')->get();

        $finishedProducts = collect();
        $semiFinishedProducts = collect();
        if ($branchToUse) {
            $finishedProducts = FinishedProduct::where('is_active', true)
                ->with(['unit', 'finishedBranchStocks' => function ($q) use ($branchToUse) {
                    $q->where('branch_id', $branchToUse->id);
                }])
                ->orderBy('name')
                ->get();

            $semiFinishedProducts = SemiFinishedProduct::where('is_active', true)
                ->with(['unit', 'semiFinishedBranchStocks' => function ($q) use ($branchToUse) {
                    $q->where('branch_id', $branchToUse->id);
                }])
                ->orderBy('name')
                ->get();
        }

        return view('destruction-reports.create', compact('branches', 'finishedProducts', 'semiFinishedProducts', 'currentBranch', 'selectedBranch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'destruction_date' => 'required|date|before_or_equal:today',
            'reason' => 'required|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.finished_product_id' => 'nullable|exists:finished_products,id',
            'items.*.semi_finished_product_id' => 'nullable|exists:semi_finished_products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.condition_description' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ], [
            'items.required' => 'Minimal satu item harus diajukan.',
            'items.min' => 'Minimal satu item harus diajukan.',
            'items.*.quantity.required' => 'Jumlah wajib diisi.',
            'items.*.quantity.numeric' => 'Jumlah harus berupa angka.',
            'items.*.quantity.min' => 'Jumlah harus lebih dari 0.',
            'items.*.finished_product_id.exists' => 'Produk jadi yang dipilih tidak valid.',
            'items.*.semi_finished_product_id.exists' => 'Produk setengah jadi yang dipilih tidak valid.',
            'destruction_date.before_or_equal' => 'Tanggal pemusnahan tidak boleh melewati hari ini.',
        ], [
            'branch_id' => 'cabang',
            'destruction_date' => 'tanggal pemusnahan',
            'reason' => 'alasan',
            'notes' => 'catatan',
            'items' => 'item',
            'items.*.finished_product_id' => 'produk jadi',
            'items.*.semi_finished_product_id' => 'produk setengah jadi',
            'items.*.quantity' => 'jumlah',
            'items.*.condition_description' => 'kondisi',
        ]);

        $user = Auth::user();
        $branchId = $request->branch_id;

        try {
            DB::beginTransaction();
            $prepared = $this->prepareItems($branchId, $request->items);

            $reportNumber = CodeGeneratorHelper::generateDailySequentialCode('DR', null, \App\Models\DestructionReport::class, 'report_number');

            $destructionReport = DestructionReport::create([
                'report_number' => $reportNumber,
                'branch_id' => $branchId,
                'reported_by' => $user->id,
                'destruction_date' => $request->destruction_date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'status' => 'draft',
            ]);

            foreach ($prepared['lines'] as $line) {
                $destructionReport->destructionReportItems()->create($line);
            }

            // Optionally persist total_cost column for faster listing
            $destructionReport->update(['total_cost' => $prepared['total']]);

            DB::commit();

            return redirect()->route('destruction-reports.index')
                ->with('success', 'Pengajuan pemusnahan produk berhasil dibuat. Menunggu persetujuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(DestructionReport $destructionReport)
    {
        $destructionReport->load(['branch', 'reportedBy', 'approvedBy']);
        // Show all item types
        $items = $destructionReport->destructionReportItems()
            ->with(['finishedProduct.unit', 'semiFinishedProduct.unit'])
            ->get();
        return view('destruction-reports.show', compact('destructionReport', 'items'));
    }

    /**
     * Validate items against stock and production cost, and prepare payload for creation/update.
     * Returns ['lines' => [...], 'total' => decimal]
     */
    protected function prepareItems(int $branchId, array $items): array
    {
        $lines = [];
        $total = 0;

        foreach ($items as $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            if ($qty <= 0) {
                throw new \Exception('Jumlah tidak valid.');
            }

            if (!empty($item['finished_product_id'])) {
                $product = FinishedProduct::findOrFail($item['finished_product_id']);
                $branchStock = FinishedBranchStock::where('branch_id', $branchId)
                    ->where('finished_product_id', $product->id)
                    ->first();

                if (!$branchStock || $branchStock->quantity < $qty) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi. Stok tersedia: " . ($branchStock ? $branchStock->quantity : 0));
                }
                if ($product->production_cost <= 0) {
                    throw new \Exception("Modal dasar untuk {$product->name} belum diatur. Silakan hubungi admin.");
                }

                $lineTotal = $qty * $product->production_cost;
                $lines[] = [
                    'item_type' => 'finished_product',
                    'item_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => $product->production_cost,
                    'total_cost' => $lineTotal,
                    'condition_description' => $item['condition_description'] ?? null,
                ];
                $total += $lineTotal;
            } elseif (!empty($item['semi_finished_product_id'])) {
                $product = SemiFinishedProduct::findOrFail($item['semi_finished_product_id']);
                $branchStock = SemiFinishedBranchStock::where('branch_id', $branchId)
                    ->where('semi_finished_product_id', $product->id)
                    ->first();

                if (!$branchStock || $branchStock->quantity < $qty) {
                    throw new \Exception("Stok {$product->name} (Setengah Jadi) tidak mencukupi. Stok tersedia: " . ($branchStock ? $branchStock->quantity : 0));
                }
                if (($product->production_cost ?? 0) <= 0) {
                    throw new \Exception("Modal dasar untuk {$product->name} (Setengah Jadi) belum diatur. Silakan hubungi admin.");
                }

                $lineTotal = $qty * $product->production_cost;
                $lines[] = [
                    'item_type' => 'semi_finished_product',
                    'item_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => $product->production_cost,
                    'total_cost' => $lineTotal,
                    'condition_description' => $item['condition_description'] ?? null,
                ];
                $total += $lineTotal;
            } else {
                throw new \Exception('Tipe item tidak dikenali atau produk belum dipilih.');
            }
        }

        return compact('lines', 'total');
    }

    public function edit(Request $request, DestructionReport $destructionReport)
    {
        if (in_array($destructionReport->status, ['approved'])) {
            return redirect()->route('destruction-reports.index')->with('error', 'Laporan yang sudah disetujui tidak dapat diedit');
        }

        $user = Auth::user();
        $currentBranch = $user->branch;

        $selectedBranch = null;
        if ($request->has('branch_id')) {
            $selectedBranch = Branch::find($request->branch_id);
        }
        $branchToUse = $selectedBranch ?? $destructionReport->branch ?? $currentBranch;

        if ($branchToUse && $branchToUse->type === 'production') {
            return redirect()->route('destruction-reports.index')
                ->with('error', 'Pemusnahan produk tidak dapat dilakukan di Pusat Produksi.');
        }

        $branches = Branch::where('is_active', true)->where('type', 'branch')->orderBy('name')->get();

        $finishedProducts = collect();
        $semiFinishedProducts = collect();
        if ($branchToUse) {
            $finishedProducts = FinishedProduct::where('is_active', true)
                ->with(['unit', 'finishedBranchStocks' => function ($q) use ($branchToUse) {
                    $q->where('branch_id', $branchToUse->id);
                }])
                ->orderBy('name')
                ->get();

            $semiFinishedProducts = SemiFinishedProduct::where('is_active', true)
                ->with(['unit', 'semiFinishedBranchStocks' => function ($q) use ($branchToUse) {
                    $q->where('branch_id', $branchToUse->id);
                }])
                ->orderBy('name')
                ->get();
        }

        $destructionReport->load('destructionReportItems');

        return view('destruction-reports.edit', compact('destructionReport', 'branches', 'finishedProducts', 'semiFinishedProducts', 'currentBranch', 'selectedBranch'));
    }

    public function update(Request $request, DestructionReport $destructionReport)
    {
        if (in_array($destructionReport->status, ['approved'])) {
            return redirect()->route('destruction-reports.index')->with('error', 'Laporan yang sudah disetujui tidak dapat diedit');
        }

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'destruction_date' => 'required|date|before_or_equal:today',
            'reason' => 'required|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.finished_product_id' => 'nullable|exists:finished_products,id',
            'items.*.semi_finished_product_id' => 'nullable|exists:semi_finished_products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.condition_description' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ], [
            'items.required' => 'Minimal satu item harus diajukan.',
            'items.min' => 'Minimal satu item harus diajukan.',
            'items.*.quantity.required' => 'Jumlah wajib diisi.',
            'items.*.quantity.numeric' => 'Jumlah harus berupa angka.',
            'items.*.quantity.min' => 'Jumlah harus lebih dari 0.',
            'items.*.finished_product_id.exists' => 'Produk jadi yang dipilih tidak valid.',
            'items.*.semi_finished_product_id.exists' => 'Produk setengah jadi yang dipilih tidak valid.',
            'destruction_date.before_or_equal' => 'Tanggal pemusnahan tidak boleh melewati hari ini.',
        ], [
            'branch_id' => 'cabang',
            'destruction_date' => 'tanggal pemusnahan',
            'reason' => 'alasan',
            'notes' => 'catatan',
            'items' => 'item',
            'items.*.finished_product_id' => 'produk jadi',
            'items.*.semi_finished_product_id' => 'produk setengah jadi',
            'items.*.quantity' => 'jumlah',
            'items.*.condition_description' => 'kondisi',
        ]);

        try {
            DB::beginTransaction();

            $prepared = $this->prepareItems($request->branch_id, $request->items);

            $destructionReport->update([
                'branch_id' => $request->branch_id,
                'destruction_date' => $request->destruction_date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'total_cost' => $prepared['total'],
            ]);

            // Reset items and recreate (since still in draft)
            $destructionReport->destructionReportItems()->delete();
            foreach ($prepared['lines'] as $line) {
                $destructionReport->destructionReportItems()->create($line);
            }

            DB::commit();
            return redirect()->route('destruction-reports.index')->with('success', 'Laporan pemusnahan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function approve(Request $request, DestructionReport $destructionReport)
    {
        if ($destructionReport->status !== 'draft') {
            return back()->with('error', 'Hanya laporan dengan status draft yang dapat diproses.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            if ($request->action === 'approve') {
                $items = $destructionReport->destructionReportItems()->get();
                foreach ($items as $item) {
                    if ($item->item_type === 'finished_product') {
                        $branchStock = FinishedBranchStock::where('branch_id', $destructionReport->branch_id)
                            ->where('finished_product_id', $item->item_id)
                            ->lockForUpdate()
                            ->first();

                        if (!$branchStock || $branchStock->quantity < $item->quantity) {
                            throw new \Exception('Stok tidak mencukupi saat approval.');
                        }

                        // Reuse stock helper to avoid duplication and ensure movement logging
                        $branchStock->updateStock('out', $item->quantity, 'Pemusnahan ' . $destructionReport->report_number, $user->id);
                    } elseif ($item->item_type === 'semi_finished_product') {
                        $branchStock = SemiFinishedBranchStock::where('branch_id', $destructionReport->branch_id)
                            ->where('semi_finished_product_id', $item->item_id)
                            ->lockForUpdate()
                            ->first();

                        if (!$branchStock || $branchStock->quantity < $item->quantity) {
                            throw new \Exception('Stok setengah jadi tidak mencukupi saat approval.');
                        }

                        // Semi-finished stock update uses different signature
                        $branchStock->updateStock($item->quantity, null, 'subtract');
                    }
                }

                $destructionReport->update([
                    'status' => 'approved',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);

                $message = 'Pengajuan pemusnahan disetujui dan stok dikurangi.';
            } else {
                $destructionReport->update([
                    'status' => 'rejected',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);

                $message = 'Pengajuan pemusnahan ditolak.';
            }

            DB::commit();

            return redirect()->route('destruction-reports.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(DestructionReport $destructionReport)
    {
        if ($destructionReport->status === 'approved') {
            return back()->with('error', 'Laporan yang sudah disetujui tidak dapat dihapus');
        }

        try {
            DB::beginTransaction();

            $destructionReport->destructionReportItems()->delete();
            $destructionReport->delete();

            DB::commit();
            return redirect()->route('destruction-reports.index')->with('success', 'Laporan pemusnahan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // API: finished products with stock for a branch to be used in creation form
    public function apiFinishedProducts(Request $request)
    {
        $branchId = $request->get('branch_id');
        if (!$branchId) {
            return response()->json([]);
        }

        $finishedProducts = FinishedProduct::where('is_active', true)
            ->with(['unit', 'finishedBranchStocks' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($p) {
                $stock = $p->finishedBranchStocks->first();
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'unit' => $p->unit ? $p->unit->abbreviation : '',
                    'unit_cost' => $p->production_cost,
                    'available_stock' => $stock ? $stock->quantity : 0,
                ];
            })
            ->values();

        return response()->json($finishedProducts);
    }
}
