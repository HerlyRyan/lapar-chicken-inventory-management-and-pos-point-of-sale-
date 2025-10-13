<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DestructionReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes for Purchase Orders
Route::get('/materials-by-supplier/{supplier_id}', [PurchaseOrderController::class, 'getMaterialsBySupplier'])->name('api.materials-by-supplier');
Route::get('/purchase-orders/pending', [PurchaseOrderController::class, 'getPendingOrders'])->name('api.purchase-orders.pending');
Route::get('/purchase-orders/{id}', [PurchaseOrderController::class, 'getPurchaseOrderDetails'])->name('api.purchase-orders.show');
Route::post('/validate-material-prices', [PurchaseOrderController::class, 'validateMaterialPrices'])->name('api.validate-material-prices');

// API routes for Sales POS
Route::get('/sales/products', [SaleController::class, 'apiProducts'])->name('api.sales.products');
Route::get('/sales/packages', [SaleController::class, 'apiPackages'])->name('api.sales.packages');

// API routes for Destruction Reports
Route::get('/destruction/finished-products', [DestructionReportController::class, 'apiFinishedProducts'])->name('api.destruction.finished-products');
