<?php

namespace App\Http\Controllers;

use App\Models\{Sale, Material, FinishedProduct, StockMovement, Branch, User};
use App\Services\DocumentVerificationService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected $verificationService;

    public function __construct(DocumentVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
        // Disable auth middleware for development
        // $this->middleware('auth');
    }
    /**
     * Tampilan utama laporan
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Laporan Penjualan (Sales Report)
     * Relasi: Sales -> Branch, User (kasir), SaleItems -> FinishedProduct
     */
    public function salesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
        ], [
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'end_date.required' => 'Tanggal akhir wajib diisi',
            'end_date.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai',
            'branch_id.exists' => 'Cabang yang dipilih tidak valid',
        ]);

        $query = Sale::with(['branch', 'user', 'saleItems.finishedProduct'])
            ->whereBetween('sale_date', [$request->start_date, $request->end_date]);

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();
        $branches = Branch::where('is_active', true)->get();
        
        // Grand Total
        $grandTotal = $sales->sum('final_amount');
        $totalDiscount = $sales->sum('discount_amount');
        $totalTax = $sales->sum('tax_amount');
        
        $data = [
            'sales' => $sales,
            'branches' => $branches,
            'filters' => $request->all(),
            'grandTotal' => $grandTotal,
            'totalDiscount' => $totalDiscount,
            'totalTax' => $totalTax,
            'period' => Carbon::parse($request->start_date)->format('d/m/Y') . ' - ' . Carbon::parse($request->end_date)->format('d/m/Y'),
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'generated_by' => auth()->user()->name ?? 'System',
        ];

        // Add verification data if verified
        if ($request->has('verified') && $request->verified === 'true') {
            $data['verified_at'] = now()->format('d/m/Y H:i:s');
            $data['verified_by'] = auth()->user()->name ?? 'System';
        }

        if ($request->format === 'pdf') {
            return $this->generatePDF('reports.sales_pdf', $data, 'Laporan_Penjualan_' . date('Y-m-d'));
        }

        return view('reports.sales', $data);
    }

    /**
     * Laporan Stok (Stock Report)
     * Relasi: Materials -> Unit, StockMovements -> Material, User
     */
    public function stockReport(Request $request)
    {
        $request->validate([
            'category' => 'nullable|in:raw_material,semi_finished,finished_product',
            'low_stock_only' => 'nullable|boolean',
        ], [
            'category.in' => 'Kategori yang dipilih tidak valid',
        ]);

        $query = Material::with(['unit', 'stockMovements' => function($q) {
            $q->latest()->limit(5);
        }]);

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->low_stock_only) {
            $query->whereRaw('current_stock <= minimum_stock');
        }

        $materials = $query->orderBy('name')->get();
        
        // Calculate additional metrics for decision making
        $lowStockItems = $materials->filter(function($material) {
            return $material->current_stock <= $material->minimum_stock;
        });

        $totalInventoryValue = $materials->sum(function($material) {
            return $material->current_stock * ($material->price_per_unit ?? 0);
        });

        // Add stock status for each material
        $materials->each(function($material) {
            if ($material->current_stock <= 0) {
                $material->stock_status = 'Habis';
            } elseif ($material->current_stock <= $material->minimum_stock * 0.5) {
                $material->stock_status = 'Kritis';
            } elseif ($material->current_stock <= $material->minimum_stock) {
                $material->stock_status = 'Rendah';
            } else {
                $material->stock_status = 'Aman';
            }
        });

        $data = [
            'materials' => $materials,
            'lowStockItems' => $lowStockItems,
            'totalInventoryValue' => $totalInventoryValue,
            'filters' => $request->all(),
            'report_date' => now()->format('d/m/Y'),
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'generated_by' => auth()->user()->name ?? 'System',
            'categories' => [
                'raw_material' => 'Bahan Mentah',
                'semi_finished' => 'Bahan Setengah Jadi', 
                'finished_product' => 'Produk Siap Jual'
            ]
        ];

        // Add verification data if verified
        if ($request->has('verified') && $request->verified === 'true') {
            $data['verified_at'] = now()->format('d/m/Y H:i:s');
            $data['verified_by'] = auth()->user()->name ?? 'System';
        }

        if ($request->format === 'pdf') {
            return $this->generatePDF('reports.stock_pdf', $data, 'Laporan_Stok_' . date('Y-m-d'));
        }

        return view('reports.stock', $data);
    }

    /**
     * Laporan Pergerakan Stok (Stock Movement Report)
     * Relasi: StockMovements -> Material, User, Branch
     */
    public function stockMovementReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'material_id' => 'nullable|exists:materials,id',
            'movement_type' => 'nullable|in:in,out,adjustment',
        ], [
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'end_date.required' => 'Tanggal akhir wajib diisi',
            'end_date.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai',
            'material_id.exists' => 'Material yang dipilih tidak valid',
            'movement_type.in' => 'Tipe pergerakan yang dipilih tidak valid',
        ]);

        $query = StockMovement::with(['material.unit', 'user', 'branch'])
            ->whereBetween('movement_date', [$request->start_date, $request->end_date]);

        if ($request->material_id) {
            $query->where('material_id', $request->material_id);
        }

        if ($request->movement_type) {
            $query->where('type', $request->movement_type);
        }

        $movements = $query->orderBy('movement_date', 'desc')->get();
        $materials = Material::where('is_active', true)->orderBy('name')->get();

        $data = [
            'movements' => $movements,
            'materials' => $materials,
            'filters' => $request->all(),
            'period' => Carbon::parse($request->start_date)->format('d/m/Y') . ' - ' . Carbon::parse($request->end_date)->format('d/m/Y'),
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'generated_by' => auth()->user()->name ?? 'System',
            'movementTypes' => [
                'in' => 'Masuk',
                'out' => 'Keluar',
                'adjustment' => 'Penyesuaian'
            ]
        ];

        // Add verification data if verified
        if ($request->has('verified') && $request->verified === 'true') {
            $data['verified_at'] = now()->format('d/m/Y H:i:s');
            $data['verified_by'] = auth()->user()->name ?? 'System';
        }

        if ($request->format === 'pdf') {
            return $this->generatePDF('reports.stock_movement_pdf', $data, 'Laporan_Pergerakan_Stok_' . date('Y-m-d'));
        }

        return view('reports.stock-movement', $data);
    }

    /**
     * Generate PDF untuk laporan dengan QR Code verification
     */
    public function generatePDF($view, $data, $filename)
    {
        // Generate document ID untuk verifikasi
        $documentId = 'RPT' . now()->format('YmdHis') . '_' . substr(md5($filename), 0, 8);
        
        // Generate QR Code verification
        $verificationData = [
            'total_amount' => $data['grandTotal'] ?? $data['totalInventoryValue'] ?? 0,
            'period' => $data['period'] ?? $data['report_date'] ?? now()->format('d/m/Y'),
            'generated_by' => auth()->user()->name ?? 'Development User'
        ];
        
        $verification = $this->verificationService->generateVerificationReport(
            $documentId, 
            'report', 
            $verificationData
        );
        
        // Add verification data to PDF
        $data['document_id'] = $documentId;
        $data['qr_code_data'] = $verification['qr_code'];
        $data['verification_hash'] = $verification['verification_hash'];
        $data['barcode'] = $verification['barcode'];
        $data['generated_at'] = Carbon::now()->format('d/m/Y H:i:s');
        $data['generated_by'] = auth()->user()->name ?? 'Development User';
        
        // Generate digital signature if verified
        if (isset($data['verified_at'])) {
            $signature = $this->verificationService->generateDigitalSignature(
                $documentId,
                $data['verified_by'],
                $data['verified_at']
            );
            $data['digital_signature'] = $signature['signature'];
            $data['signature_qr'] = $signature['qr_code'];
        }
        
        $pdf = Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
            
        return $pdf->download($filename . '.pdf');
    }

    /**
     * Verifikasi laporan (untuk atasan)
     */
    public function verifyReport(Request $request)
    {
        // Logic untuk verifikasi laporan dengan TTD digital
        // Bisa menambahkan QR code atau barcode untuk verifikasi
        
        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diverifikasi',
            'verified_at' => Carbon::now()->format('d/m/Y H:i:s'),
            'verified_by' => auth()->user()->name
        ]);
    }

    /**
     * Verifikasi semua laporan sekaligus
     */
    public function verifyAllReports(Request $request)
    {
        // Logic untuk verifikasi multiple laporan sekaligus
        
        return response()->json([
            'success' => true,
            'message' => 'Semua laporan berhasil diverifikasi',
            'verified_count' => $request->report_ids ? count($request->report_ids) : 0,
            'verified_at' => Carbon::now()->format('d/m/Y H:i:s'),
            'verified_by' => auth()->user()->name
        ]);
    }

    // ===== ADDITIONAL REPORT METHODS TO FIX RouteNotFoundException =====

    /**
     * Laporan Laba Rugi
     */
    public function profitLoss(Request $request)
    {
        return view('reports.profit-loss', [
            'title' => 'Laporan Laba Rugi',
            'description' => 'Analisis profitabilitas per produk & cabang'
        ]);
    }

    /**
     * Analisis Biaya Produksi
     */
    public function costAnalysis(Request $request)
    {
        return view('reports.cost-analysis', [
            'title' => 'Analisis Biaya Produksi',
            'description' => 'Breakdown biaya bahan & operasional'
        ]);
    }

    /**
     * Laporan Stok
     */
    public function stock(Request $request)
    {
        return view('reports.stock', [
            'title' => 'Laporan Stok',
            'description' => 'Status stok semua jenis bahan'
        ]);
    }

    /**
     * Laporan Pergerakan Stok
     */
    public function stockMovement(Request $request)
    {
        return view('reports.stock-movement', [
            'title' => 'Laporan Pergerakan Stok',
            'description' => 'Tracking in/out stok per periode'
        ]);
    }

    /**
     * Laporan Aging Stok
     */
    public function stockAging(Request $request)
    {
        return view('reports.stock-aging', [
            'title' => 'Laporan Aging Stok',
            'description' => 'Analisis stok lama & mendekati expired'
        ]);
    }

    /**
     * Analisis ABC Inventory
     */
    public function abcAnalysis(Request $request)
    {
        return view('reports.abc-analysis', [
            'title' => 'Analisis ABC Inventory',
            'description' => 'Klasifikasi produk berdasarkan nilai'
        ]);
    }

    /**
     * Efisiensi Produksi
     */
    public function productionEfficiency(Request $request)
    {
        return view('reports.production-efficiency', [
            'title' => 'Efisiensi Produksi',
            'description' => 'Analisis yield & waste produksi'
        ]);
    }

    /**
     * Performa Supplier
     */
    public function supplierPerformance(Request $request)
    {
        return view('reports.supplier-performance', [
            'title' => 'Performa Supplier',
            'description' => 'Evaluasi kualitas & ketepatan supplier'
        ]);
    }

    /**
     * Performa Cabang
     */
    public function branchPerformance(Request $request)
    {
        return view('reports.branch-performance', [
            'title' => 'Performa Cabang',
            'description' => 'Komparasi kinerja antar cabang'
        ]);
    }

    /**
     * Forecasting Demand
     */
    public function demandForecasting(Request $request)
    {
        return view('reports.demand-forecasting', [
            'title' => 'Forecasting Demand',
            'description' => 'Prediksi kebutuhan berdasarkan trend'
        ]);
    }

    /**
     * Analisis Musiman
     */
    public function seasonalAnalysis(Request $request)
    {
        return view('reports.seasonal-analysis', [
            'title' => 'Analisis Musiman',
            'description' => 'Pola penjualan berdasarkan musim'
        ]);
    }

    /**
     * Log Notifikasi WhatsApp
     */
    public function whatsappLogs(Request $request)
    {
        return view('reports.whatsapp-logs', [
            'title' => 'Log Notifikasi WhatsApp',
            'description' => 'Tracking komunikasi sistem'
        ]);
    }
}
