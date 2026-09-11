<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashAccountController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FixedAssetController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PurchaseBillController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\UserController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

Route::get('/health', static function (): JsonResponse {
    return response()->json(['status' => 'ok', 'app' => config('app.name')]);
})->name('health');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', [AccountingController::class, 'dashboard'])->middleware('permission:dashboard_view')->name('dashboard');
    Route::post('/journal-entries', [AccountingController::class, 'storeJournal'])->middleware('permission:journals_manage')->name('journal.store');

    Route::get('/journals', [JournalController::class, 'index'])->middleware('permission:journals_view')->name('journals.index');
    Route::post('/journals', [JournalController::class, 'store'])->middleware('permission:journals_manage')->name('journals.store');

    Route::get('/accounts', [AccountController::class, 'index'])->middleware('permission:accounts_view')->name('accounts.index');
    Route::post('/accounts', [AccountController::class, 'store'])->middleware('permission:accounts_manage')->name('accounts.store');

    Route::get('/reports', [ReportController::class, 'index'])->middleware('permission:reports_view')->name('reports.index');

    Route::get('/sales', [SalesInvoiceController::class, 'index'])->middleware('permission:sales_view')->name('sales.index');
    Route::post('/sales', [SalesInvoiceController::class, 'store'])->middleware('permission:sales_manage')->name('sales.store');
    Route::post('/sales/{salesInvoice}/pay', [SalesInvoiceController::class, 'pay'])->middleware('permission:sales_manage')->name('sales.pay');

    Route::get('/purchases', [PurchaseBillController::class, 'index'])->middleware('permission:purchases_view')->name('purchases.index');
    Route::post('/purchases', [PurchaseBillController::class, 'store'])->middleware('permission:purchases_manage')->name('purchases.store');
    Route::post('/purchases/{purchaseBill}/pay', [PurchaseBillController::class, 'pay'])->middleware('permission:purchases_manage')->name('purchases.pay');

    Route::get('/products', [ProductController::class, 'index'])->middleware('permission:products_view')->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->middleware('permission:products_manage')->name('products.store');
    Route::post('/products/{product}/stock', [ProductController::class, 'adjustStock'])->middleware('permission:products_manage')->name('products.stock');
    Route::post('/products/{product}/toggle', [ProductController::class, 'toggle'])->middleware('permission:products_manage')->name('products.toggle');

    Route::get('/contacts', [ContactController::class, 'index'])->middleware('permission:contacts_view')->name('contacts.index');
    Route::post('/contacts', [ContactController::class, 'store'])->middleware('permission:contacts_manage')->name('contacts.store');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->middleware('permission:contacts_manage')->name('contacts.update');

    Route::get('/cash', [CashAccountController::class, 'index'])->middleware('permission:cash_view')->name('cash.index');
    Route::post('/cash', [CashAccountController::class, 'store'])->middleware('permission:cash_manage')->name('cash.store');
    Route::post('/cash/transfer', [CashAccountController::class, 'transfer'])->middleware('permission:cash_manage')->name('cash.transfer');
    Route::post('/cash/{cashAccount}/adjust', [CashAccountController::class, 'adjust'])->middleware('permission:cash_manage')->name('cash.adjust');

    Route::get('/taxes', [TaxRateController::class, 'index'])->middleware('permission:taxes_view')->name('taxes.index');
    Route::post('/taxes', [TaxRateController::class, 'store'])->middleware('permission:taxes_manage')->name('taxes.store');
    Route::post('/taxes/{taxRate}/toggle', [TaxRateController::class, 'toggle'])->middleware('permission:taxes_manage')->name('taxes.toggle');

    Route::get('/assets', [FixedAssetController::class, 'index'])->middleware('permission:assets_view')->name('assets.index');
    Route::post('/assets', [FixedAssetController::class, 'store'])->middleware('permission:assets_manage')->name('assets.store');
    Route::post('/assets/{fixedAsset}/depreciate', [FixedAssetController::class, 'depreciate'])->middleware('permission:assets_manage')->name('assets.depreciate');
    Route::post('/assets/{fixedAsset}/dispose', [FixedAssetController::class, 'dispose'])->middleware('permission:assets_manage')->name('assets.dispose');

    Route::get('/projects', [ProjectController::class, 'index'])->middleware('permission:projects_view')->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->middleware('permission:projects_manage')->name('projects.store');
    Route::post('/projects/{project}/status', [ProjectController::class, 'updateStatus'])->middleware('permission:projects_manage')->name('projects.status');

    Route::get('/users', [UserController::class, 'index'])->middleware('permission:users_view')->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:users_manage')->name('users.store');
    Route::post('/users/{managedUser}/role', [UserController::class, 'updateRole'])->middleware('permission:users_manage')->name('users.role');

    Route::get('/integrations', [IntegrationController::class, 'index'])->middleware('permission:integrations_view')->name('integrations.index');
    Route::post('/integrations', [IntegrationController::class, 'update'])->middleware('permission:integrations_manage')->name('integrations.update');

    Route::get('/settings', [SettingController::class, 'index'])->middleware('permission:settings_view')->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->middleware('permission:settings_manage')->name('settings.update');

    Route::get('/roles', [RolePermissionController::class, 'index'])->middleware('permission:roles_manage')->name('roles.index');
    Route::post('/roles', [RolePermissionController::class, 'update'])->middleware('permission:roles_manage')->name('roles.update');
    Route::post('/roles/reset', [RolePermissionController::class, 'reset'])->middleware('permission:roles_manage')->name('roles.reset');
});
