<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DestructionReportController;

use App\Http\Controllers\FinishedProductController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RawMaterialStockController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseReceiptController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\SalesPackageController;
use App\Http\Controllers\SemiFinishedProductController;
use App\Http\Controllers\SemiFinishedUsageRequestController;
use App\Http\Controllers\SemiFinishedUsageApprovalController;
use App\Http\Controllers\SemiFinishedUsageProcessController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\ProductionRequestController;
use App\Http\Controllers\ProductionApprovalController;
use App\Http\Controllers\ProductionProcessController;
use App\Http\Controllers\SemiFinishedStockController;
use App\Http\Controllers\SemiFinishedDistributionController;
use App\Http\Controllers\FinishedProductsStockController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Dev\DevController;
use App\Http\Controllers\Report\BranchReportController;
use App\Http\Controllers\Report\FinishedReportController;
use App\Http\Controllers\Report\ProductBestSellingReportController;
use App\Http\Controllers\Report\RawMaterialReportController;
use App\Http\Controllers\Report\SalePackagesReportController;
use App\Http\Controllers\Report\SalesReportController;
use App\Http\Controllers\Report\SemiFinishedReportController;
use App\Http\Controllers\Report\SemiFinishedUsageReportController;
use App\Http\Controllers\Report\StockTransferReportController;
use App\Http\Controllers\Report\SupplierReportController;
use Illuminate\Http\Request;
use App\Http\Controllers\StockOpnameController;
use App\Models\FinishedBranchStock;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }

    return redirect('/login');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::middleware('role:SUPER_ADMIN,MANAGER')->group(function () {
        Route::get('/dashboard', function (Request $request) {
            // Clear selected branch
            if ($request->clear_dashboard_branch == 1) {
                session(['branch_id' => 0]);
            }

            // Set selected branch
            if ($request->has('branch_id')) {
                session(['branch_id' => $request->branch_id]);
            }

            return view('dashboard.index');
        })->name('dashboard');

        Route::prefix('dashboard/sales')->group(function () {
            Route::get('/yearly', [DashboardController::class, 'yearly']);
            Route::get('/monthly', [DashboardController::class, 'monthly']);
        });
    });

    Route::middleware('role:SUPER_ADMIN')->group(function () {
        // ===== DATA MASTER ROUTES =====
        // User and Role Management
        Route::resource('users', UserController::class);
        Route::get('/users/data', [UserController::class, 'data'])->name('users.data'); // untuk ajax

        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);

        // User Role Management Routes
        Route::get('users/{user}/roles/edit', [UserRoleController::class, 'edit'])->name('user-roles.edit');
        Route::put('users/{user}/roles', [UserRoleController::class, 'update'])->name('user-roles.update');
        Route::post('users/{user}/roles', [UserRoleController::class, 'assignRole'])->name('user-roles.assign');
        Route::delete('users/{user}/roles/{role}', [UserRoleController::class, 'removeRole'])->name('user-roles.remove');

        // Branch Management
        Route::resource('branches', BranchController::class);
        Route::patch('branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])->name('branches.toggle-status');
        Route::post('/switch-branch', [BranchController::class, 'switchBranch'])->name('switch-branch');
        Route::get('/api/branches/{branch}/inventory-summary', [BranchController::class, 'getInventorySummary'])->name('api.branch.inventory-summary');
        Route::post('/api/branches/transfer-stock', [BranchController::class, 'transferStock'])->name('api.branch.transfer-stock');

        // Product Categories and Units
        Route::resource('categories', CategoryController::class);
        Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        Route::get('api/categories/active', [CategoryController::class, 'getActiveCategories'])->name('api.categories.active');
        Route::resource('units', UnitController::class);
        Route::patch('units/{unit}/toggle-status', [UnitController::class, 'toggleStatus'])->name('units.toggle-status');

        // Supplier Management
        Route::resource('suppliers', SupplierController::class);
        Route::patch('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
    });

    // Semi-Finished Usage Approvals (Approvals Inbox)
    Route::prefix('semi-finished-usage-approvals')->name('semi-finished-usage-approvals.')->group(function () {
        Route::get('/', [SemiFinishedUsageApprovalController::class, 'index'])->name('index');
        // Redirect to unified detail page if needed
        Route::get('{semi_finished_usage_approval}', [SemiFinishedUsageApprovalController::class, 'show'])->name('show');
    });

    // Materials Management
    // Raw Materials Stock Monitoring (ensure this is above the resource to avoid collision with {raw_material})
    Route::get('raw-materials/stock', [RawMaterialStockController::class, 'index'])->name('raw-materials.stock');
    Route::resource('raw-materials', RawMaterialController::class); // Bahan mentah
    Route::patch('raw-materials/{rawMaterial}/toggle-status', [RawMaterialController::class, 'toggleStatus'])->name('raw-materials.toggle-status');

    // Raw Materials Branch Stock Management Routes
    Route::post('raw-materials/{rawMaterial}/initialize-branch-stocks', [RawMaterialController::class, 'initializeBranchStocks'])->name('raw-materials.initialize-branch-stocks');
    Route::post('raw-materials/{rawMaterial}/branch-stock/{branchId}/add', [RawMaterialController::class, 'addBranchStock'])->name('raw-materials.add-branch-stock');
    Route::post('raw-materials/{rawMaterial}/branch-stock/{branchId}/reduce', [RawMaterialController::class, 'reduceBranchStock'])->name('raw-materials.reduce-branch-stock');
    Route::post('raw-materials/{rawMaterial}/branch-stock/{branchId}/minimum', [RawMaterialController::class, 'setMinimumStock'])->name('raw-materials.set-minimum-stock');

    // Raw Materials API for real-time price updates
    Route::get('api/raw-materials/latest-prices', [RawMaterialController::class, 'getLatestPrices'])->name('api.raw-materials.latest-prices');

    // ===== PRODUCTS MANAGEMENT ROUTES =====

    // Semi-Finished Products
    Route::resource('semi-finished-products', SemiFinishedProductController::class);
    Route::patch('semi-finished-products/{semiFinishedProduct}/toggle-status', [SemiFinishedProductController::class, 'toggleStatus'])->name('semi-finished-products.toggle-status');
    Route::post('semi-finished-products/stock/update', [SemiFinishedProductController::class, 'updateStock'])->name('semi-finished-products.stock.update');
    Route::post('semi-finished-products/{semiFinishedProduct}/update-stock', [SemiFinishedProductController::class, 'updateStock'])->name('semi-finished-products.update-stock');
    Route::post('semi-finished-products/{semiFinishedProduct}/transfer-stock', [SemiFinishedProductController::class, 'transferStock'])->name('semi-finished-products.transfer-stock');
    Route::get('semi-finished-products/{semiFinishedProduct}/stock-data', [SemiFinishedProductController::class, 'stockData'])->name('semi-finished-products.stock-data');
    Route::get('api/semi-finished-products', [SemiFinishedProductController::class, 'apiIndex'])->name('api.semi-finished-products');

    // Finished Products
    Route::resource('finished-products', FinishedProductController::class);
    Route::patch('finished-products/{finishedProduct}/toggle-status', [FinishedProductController::class, 'toggleStatus'])->name('finished-products.toggle-status');
    Route::get('api/finished-products', [FinishedProductController::class, 'apiIndex'])->name('api.finished-products');

    // ===== PURCHASE MANAGEMENT ROUTES =====

    // Purchase Orders
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::patch('purchase-orders/{purchaseOrder}/mark-as-ordered', [PurchaseOrderController::class, 'markAsOrdered'])->name('purchase-orders.mark-as-ordered');
    Route::get('purchase-orders/{purchaseOrder}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');

    // Purchase Order API Routes
    // Web route deprecated -> redirect to existing API route (preserve supplier_id)
    Route::get('purchase-orders/materials-by-supplier', function (Request $request) {
        $supplierId = $request->query('supplier_id');
        if ($supplierId) {
            return redirect()->route('api.materials-by-supplier', ['supplier_id' => $supplierId]);
        }
        // Fallback: go to API route without supplier (will likely 404)
        return redirect()->to('/api/materials-by-supplier');
    })->name('purchase-orders.materials-by-supplier');
    // Legacy alias route -> keep redirecting through the web alias (preserve query string)
    Route::get('get-materials-by-supplier', function (Request $request) {
        $qs = $request->getQueryString();
        return redirect()->to(route('purchase-orders.materials-by-supplier') . ($qs ? ('?' . $qs) : ''));
    })->name('get.materials-by-supplier');
    Route::post('purchase-orders/validate-prices', [PurchaseOrderController::class, 'validateMaterialPrices'])->name('purchase-orders.validate-prices');

    // Stock Management
    Route::get('api/stock/{itemType}/{itemId}/branch/{branchId}', [BranchController::class, 'getItemStock'])->name('api.stock.check');
    // Stock Transfer CRUD Routes
    Route::resource('stock-transfer', StockTransferController::class)->except(['show', 'destroy']);
    Route::get('stock-transfer/{stockTransfer}/detail', [StockTransferController::class, 'detail'])->name('stock-transfer.detail');
    Route::post('stock-transfer/{stockTransfer}/cancel', [StockTransferController::class, 'cancel'])->name('stock-transfer.cancel');

    // Stock Transfer Approval Routes
    Route::get('stock-transfer/inbox', [StockTransferController::class, 'inbox'])->name('stock-transfer.inbox');
    Route::post('stock-transfer/{stockTransfer}/accept', [StockTransferController::class, 'accept'])->name('stock-transfer.accept');
    Route::post('stock-transfer/{stockTransfer}/reject', [StockTransferController::class, 'reject'])->name('stock-transfer.reject');

    // Legacy routes for backward compatibility
    Route::post('stock-transfer/request', [StockTransferController::class, 'request'])->name('stock-transfer.request');
    // Legacy immediate API (kept for backward compatibility)
    Route::post('api/stock-transfer', [StockTransferController::class, 'transfer'])->name('api.stock-transfer');

    // Purchase Receipt Routes
    // Export must be defined BEFORE resource to avoid being captured by {purchase_receipt}
    Route::get('purchase-receipts/export', [PurchaseReceiptController::class, 'exportCsv'])->name('purchase-receipts.export');
    Route::resource('purchase-receipts', PurchaseReceiptController::class);
    Route::get('api/purchase-receipts/items-by-purchase-order', [PurchaseReceiptController::class, 'itemsByPurchaseOrder'])
        ->name('api.purchase-receipts.items-by-purchase-order');

    // Legacy Goods Receipt Routes removed - using purchase-receipts directly

    // ===== SALES & OPERATIONAL ROUTES ===== // REMOVED - Sales refactor

    // Sales Management // REMOVED - Sales refactor
    // Route::resource('sales', SaleController::class);
    // Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    // Route::get('api/sales/branch-stock/{branchId}/{productId}', [SaleController::class, 'getBranchStock'])->name('api.sales.branch-stock');

    // Sales Package Management
    Route::resource('sales-packages', SalesPackageController::class);
    Route::patch('sales-packages/{salesPackage}/toggle-status', [SalesPackageController::class, 'toggleStatus'])->name('sales-packages.toggle-status');
    Route::get('api/sales-packages/branch/{branch_id}', [SalesPackageController::class, 'getPackagesForBranch'])->name('api.sales-packages.branch');

    // ===== RAW MATERIALS STOCK MONITORING =====

    // Removed duplicate route

    // ===== PRODUCTION CENTER WORKFLOW =====
    Route::middleware('role:SUPER_ADMIN,KRU_PRODUKSI,KEPALA_PRODUKSI')->group(function () {
        // Production Center Dashboard (deprecated) -> redirect to unified dashboard
        Route::get('production-center', function () {
            return redirect()->to('/dashboard?branch_id=5');
        })->name('production-center.index');

        // Production Requests (Kepala Produksi)
        Route::prefix('production-requests')->name('production-requests.')->group(function () {
            Route::get('/', [ProductionRequestController::class, 'index'])->name('index');
            Route::get('create', [ProductionRequestController::class, 'create'])->name('create');
            Route::post('/', [ProductionRequestController::class, 'store'])->name('store');
            Route::get('{productionRequest}', [ProductionRequestController::class, 'show'])->name('show');
            Route::get('{productionRequest}/edit', [ProductionRequestController::class, 'edit'])->name('edit');
            Route::put('{productionRequest}', [ProductionRequestController::class, 'update'])->name('update');
            Route::delete('{productionRequest}', [ProductionRequestController::class, 'destroy'])->name('destroy');
            // Alternative GET route for delete (debugging)
            Route::get('{productionRequest}/destroy-get', [ProductionRequestController::class, 'destroyGet'])->name('destroy-get');
            // Confirmation page for delete
            Route::get('{productionRequest}/delete-confirm', [ProductionRequestController::class, 'deleteConfirm'])->name('delete-confirm');
        });

        // Production Processes (Kru Produksi)
        Route::prefix('production-processes')->name('production-processes.')->group(function () {
            Route::get('/', [ProductionProcessController::class, 'index'])->name('index');
            Route::get('{productionRequest}', [ProductionProcessController::class, 'show'])->name('show');
            Route::post('{productionRequest}/start', [ProductionProcessController::class, 'start'])->name('start');
            Route::post('{productionRequest}/update-status', [ProductionProcessController::class, 'updateStatus'])->name('update-status');
            Route::post('{productionRequest}/complete', [ProductionProcessController::class, 'complete'])->name('complete');
            Route::get('{productionRequest}/planned-outputs', [ProductionProcessController::class, 'getPlannedOutputs'])->name('planned-outputs');
        });
    });

    // Production Approvals (Manager)
    Route::prefix('production-approvals')->name('production-approvals.')->group(function () {
        Route::get('/', [ProductionApprovalController::class, 'index'])->name('index');
        // Bulk actions
        Route::post('bulk-approve', [ProductionApprovalController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('bulk-reject', [ProductionApprovalController::class, 'bulkReject'])->name('bulk-reject');
        Route::get('{productionRequest}', [ProductionApprovalController::class, 'show'])->name('show');
        Route::post('{productionRequest}/approve', [ProductionApprovalController::class, 'approve'])->name('approve');
        Route::post('{productionRequest}/reject', [ProductionApprovalController::class, 'reject'])->name('reject');
    });

    // Semi-Finished Stock Management (Unified)
    Route::prefix('semi-finished-stock')->name('semi-finished-stock.')->group(function () {
        Route::get('/', [SemiFinishedStockController::class, 'index'])->name('index');
        Route::get('{semiFinishedProduct}', [SemiFinishedStockController::class, 'show'])->name('show');
        Route::post('{semiFinishedProduct}/adjust', [SemiFinishedStockController::class, 'adjustStock'])->name('adjust');
    });

    // Finished Products Stock Management
    Route::prefix('finished-products-stock')->name('finished-products-stock.')->group(function () {
        Route::get('/', [FinishedProductsStockController::class, 'index'])->name('index');
        Route::get('{finishedProduct}', [FinishedProductsStockController::class, 'show'])->name('show');
        Route::post('{finishedProduct}/adjust', [FinishedProductsStockController::class, 'adjustStock'])->name('adjust');
    });

    // Stock Opnames (Stok Opname)
    Route::prefix('stock-opnames')->name('stock-opnames.')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index'])->name('index');
        Route::get('create', [StockOpnameController::class, 'create'])->name('create');
        Route::post('/', [StockOpnameController::class, 'store'])->name('store');
        Route::get('{stockOpname}/edit', [StockOpnameController::class, 'edit'])->name('edit');
        Route::put('{stockOpname}', [StockOpnameController::class, 'update'])->name('update');
        Route::post('{stockOpname}/submit', [StockOpnameController::class, 'submit'])->name('submit');
        Route::get('{stockOpname}', [StockOpnameController::class, 'show'])->name('show');
    });

    // Legacy redirects from old branch-specific routes
    Route::get('branch-semi-finished-stock', function (Request $request) {
        $qs = $request->getQueryString();
        return redirect()->to(route('semi-finished-stock.index') . ($qs ? ('?' . $qs) : ''), 301);
    });
    Route::get('branch-semi-finished-stock/{semiFinishedProduct}', function (Request $request, $semiFinishedProduct) {
        $qs = $request->getQueryString();
        $url = route('semi-finished-stock.show', ['semiFinishedProduct' => $semiFinishedProduct]);
        return redirect()->to($url . ($qs ? ('?' . $qs) : ''), 301);
    });
    Route::post('branch-semi-finished-stock/{semiFinishedProduct}/adjust', function (Request $request, $semiFinishedProduct) {
        $qs = $request->getQueryString();
        $url = route('semi-finished-stock.adjust', ['semiFinishedProduct' => $semiFinishedProduct]);
        return redirect()->to($url . ($qs ? ('?' . $qs) : ''), 307);
    });

    // Semi-Finished Distributions (Kepala Produksi -> Kepala Toko)
    Route::prefix('semi-finished-distributions')->name('semi-finished-distributions.')->group(function () {
        Route::get('/', [SemiFinishedDistributionController::class, 'index'])->name('index');
        Route::get('create', [SemiFinishedDistributionController::class, 'create'])->name('create');
        Route::post('/', [SemiFinishedDistributionController::class, 'store'])->name('store');
        // Branch acceptance inbox
        Route::get('inbox', [SemiFinishedDistributionController::class, 'inbox'])->name('inbox');
        Route::get('{distribution}', [SemiFinishedDistributionController::class, 'show'])->name('show');
        Route::post('{distribution}/accept', [SemiFinishedDistributionController::class, 'accept'])->name('accept');
        Route::post('{distribution}/reject', [SemiFinishedDistributionController::class, 'reject'])->name('reject');
    });

    // Material Usage Requests (Centralized Purchasing Flow)
    Route::prefix('material-usage-requests')->name('material-usage-requests.')->group(function () {
        Route::get('/', [SemiFinishedUsageRequestController::class, 'index'])->name('index');
        Route::get('create', [SemiFinishedUsageRequestController::class, 'create'])->name('create');
        Route::post('/', [SemiFinishedUsageRequestController::class, 'store'])->name('store');
        Route::get('{semiFinishedUsageRequest}', [SemiFinishedUsageRequestController::class, 'show'])->name('show');
        Route::get('{semiFinishedUsageRequest}/edit', [SemiFinishedUsageRequestController::class, 'edit'])->name('edit');
        Route::put('{semiFinishedUsageRequest}', [SemiFinishedUsageRequestController::class, 'update'])->name('update');
        Route::post('{semiFinishedUsageRequest}/approve', [SemiFinishedUsageRequestController::class, 'approve'])->name('approve');
        Route::post('{semiFinishedUsageRequest}/reject', [SemiFinishedUsageRequestController::class, 'reject'])->name('reject');
        Route::post('{semiFinishedUsageRequest}/process', [SemiFinishedUsageRequestController::class, 'process'])->name('process');
        Route::post('{semiFinishedUsageRequest}/complete', [SemiFinishedUsageRequestController::class, 'complete'])->name('complete');
        Route::post('{semiFinishedUsageRequest}/cancel', [SemiFinishedUsageRequestController::class, 'cancel'])->name('cancel');
    });

    // Semi-Finished Usage Requests (alias to Material Usage Requests)
    // This consolidates the feature under the semi-finished section without duplicating logic
    Route::prefix('semi-finished-usage-requests')->name('semi-finished-usage-requests.')->group(function () {
        Route::get('/', [SemiFinishedUsageRequestController::class, 'index'])->name('index');
        Route::get('create', [SemiFinishedUsageRequestController::class, 'create'])->name('create');
        Route::post('/', [SemiFinishedUsageRequestController::class, 'store'])->name('store');
        Route::get('{semiFinishedUsageRequest}', [SemiFinishedUsageRequestController::class, 'show'])->name('show');
        Route::get('{semiFinishedUsageRequest}/edit', [SemiFinishedUsageRequestController::class, 'edit'])->name('edit');
        Route::put('{semiFinishedUsageRequest}', [SemiFinishedUsageRequestController::class, 'update'])->name('update');
        Route::post('{semiFinishedUsageRequest}/approve', [SemiFinishedUsageRequestController::class, 'approve'])->name('approve');
        Route::post('{semiFinishedUsageRequest}/reject', [SemiFinishedUsageRequestController::class, 'reject'])->name('reject');
        Route::post('{semiFinishedUsageRequest}/process', [SemiFinishedUsageRequestController::class, 'process'])->name('process');
        Route::post('{semiFinishedUsageRequest}/complete', [SemiFinishedUsageRequestController::class, 'complete'])->name('complete');
        Route::post('{semiFinishedUsageRequest}/cancel', [SemiFinishedUsageRequestController::class, 'cancel'])->name('cancel');
    });

    // Semi-Finished Usage Processes (Kru Produksi Toko)
    Route::prefix('semi-finished-usage-processes')->name('semi-finished-usage-processes.')->group(function () {
        Route::get('/', [SemiFinishedUsageProcessController::class, 'index'])->name('index');
        Route::get('{semiFinishedUsageRequest}', [SemiFinishedUsageProcessController::class, 'show'])->name('show');
        Route::post('{semiFinishedUsageRequest}/start', [SemiFinishedUsageProcessController::class, 'start'])->name('start');
        Route::post('{semiFinishedUsageRequest}/update-status', [SemiFinishedUsageProcessController::class, 'updateStatus'])->name('update-status');
        Route::post('{semiFinishedUsageRequest}/complete', [SemiFinishedUsageProcessController::class, 'complete'])->name('complete');
    });

    Route::middleware('role:SUPER_ADMIN,KRU_TOKO,KEPALA_TOKO,MANAGER')->group(function () {
        // ===== SALES MANAGEMENT =====
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/', [SaleController::class, 'index'])->name('index');
            Route::get('create', [SaleController::class, 'create'])->name('create');
            Route::post('/', [SaleController::class, 'store'])->name('store');
            Route::get('{sale}', [SaleController::class, 'show'])->name('show');
            Route::delete('{sale}', [SaleController::class, 'destroy'])->name('destroy');
            Route::post('{sale}/send-whatsapp', [SaleController::class, 'sendWhatsApp'])->name('send-whatsapp');
            Route::get('{sale}/receipt/download', [ReceiptController::class, 'downloadPdf'])->name('receipt.download');
        });

        // Sales API Routes
        Route::prefix('api/sales')->name('api.sales.')->group(function () {
            Route::get('products', [SaleController::class, 'apiProducts'])->name('products');
            Route::get('packages', [SaleController::class, 'apiPackages'])->name('packages');
            Route::post('validate-cart-stock', [SaleController::class, 'validateCartStock'])->name('validate-cart-stock');
        });

        // Receipt Download
        Route::get('receipt/{sale}/download', [ReceiptController::class, 'downloadPdf'])->name('receipt.download');

        Route::get('/branches/{branch}/items', [FinishedProductsStockController::class, 'items'])->name('branches.items');
    });

    // Reports routes removed intentionally during redesign phase

    // Destruction Reports (full resource)
    Route::resource('destruction-reports', DestructionReportController::class);
    // Approval route for Destruction Reports
    Route::post('destruction-reports/{destructionReport}/approve', [DestructionReportController::class, 'approve'])->name('destruction-reports.approve');

    // API report routes removed intentionally during redesign phase

    Route::post('/branch/select', function (Request $request) {
        $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $user = auth()->user();
        if ($user && $user->hasRole('Super Admin')) {
            // Align with BranchContext: allow Super Admins to set the current branch explicitly
            session(['current_branch_id' => $request->branch_id]);
        }

        return response()->json(['success' => true, 'branch_id' => $request->branch_id]);
    })->name('branch.select')->middleware('auth');

    // Development routes (only in local environment)
    // if (app()->environment('local')) {
    //     Route::get('/dev/auto-login', [DevController::class, 'autoLogin'])->name('dev.auto-login');
    //     Route::get('/dev/logout', [DevController::class, 'logout'])->name('dev.logout');
    // }

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // General fallback route for 404s (must be the last route)
    Route::fallback(function () {
        abort(404);
    });

    Route::middleware('role:SUPER_ADMIN')->group(function () {

        // Report Controller
        Route::prefix('reports/')->name('reports.')->group(function () {
            Route::get('branches', [BranchReportController::class, 'index'])->name('branches.index');
            Route::get('branches/print', [BranchReportController::class, 'print'])->name('branches.print');

            Route::get('suppliers', [SupplierReportController::class, 'index'])->name('suppliers.index');
            Route::get('suppliers/print', [SupplierReportController::class, 'print'])->name('suppliers.print');

            Route::get('raw-materials', [RawMaterialReportController::class, 'index'])->name('raw-materials.index');
            Route::get('raw-materials/print', [RawMaterialReportController::class, 'print'])->name('raw-materials.print');

            Route::get('semi-finished', [SemiFinishedReportController::class, 'index'])->name('semi-finished.index');
            Route::get('semi-finished/print', [SemiFinishedReportController::class, 'print'])->name('semi-finished.print');

            Route::get('finished', [FinishedReportController::class, 'index'])->name('finished.index');
            Route::get('finished/print', [FinishedReportController::class, 'print'])->name('finished.print');

            Route::get('sale-packages', [SalePackagesReportController::class, 'index'])->name('sale-packages.index');
            Route::get('sale-packages/print', [SalePackagesReportController::class, 'print'])->name('sale-packages.print');

            Route::get('sales', [SalesReportController::class, 'index'])->name('sales.index');
            Route::get('sales/print', [SalesReportController::class, 'print'])->name('sales.print');

            Route::get('stock-transfers', [StockTransferReportController::class, 'index'])->name('stock-transfers.index');
            Route::get('stock-transfers/print', [StockTransferReportController::class, 'print'])->name('stock-transfers.print');

            Route::get('best-selling', [ProductBestSellingReportController::class, 'index'])->name('best-selling.index');
            Route::get('best-selling/print', [ProductBestSellingReportController::class, 'print'])->name('best-selling.print');

            Route::get('semi-finished-usage', [SemiFinishedUsageReportController::class, 'index'])->name('semi-finished-usage.index');
            Route::get('semi-finished-usage/print', [SemiFinishedUsageReportController::class, 'print'])->name('semi-finished-usage.print');
        });
    });
});
