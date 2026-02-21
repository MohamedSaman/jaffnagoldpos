<?php

use App\Livewire\Dashboard;
use App\Livewire\Pos;
use App\Livewire\Products\ProductList;
use App\Livewire\Products\CategoryList;
use App\Livewire\Products\PurityList;
use App\Livewire\JewelleryRates;
use App\Livewire\Customers\CustomerList;
use App\Livewire\Suppliers\SupplierList;
use App\Livewire\Purchases\PurchaseList;
use App\Livewire\Expenses\ExpenseList;
use App\Livewire\Reports\ReportDashboard;
use App\Livewire\Reports\SessionReport;
use App\Livewire\Sales\SaleList;
use App\Livewire\Sales\SaleDuePayments;
use App\Livewire\Purchases\PurchaseDuePayments;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Auth Routes
Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/pos', Pos::class)->name('pos');
    Route::get('/sales', SaleList::class)->name('sales');
    Route::get('/sales/dues', SaleDuePayments::class)->name('sales.dues');
    
    // Inventory
    Route::get('/products', ProductList::class)->name('products');
    Route::get('/categories', CategoryList::class)->name('categories');
    Route::get('/purities', PurityList::class)->name('purities');
    Route::get('/rates', JewelleryRates::class)->name('rates');
    
    // People
    Route::get('/customers', CustomerList::class)->name('customers');
    Route::get('/suppliers', SupplierList::class)->name('suppliers');
    
    // Finance
    Route::get('/purchases', PurchaseList::class)->name('purchases');
    Route::get('/purchases/dues', PurchaseDuePayments::class)->name('purchases.dues');
    Route::get('/expenses', ExpenseList::class)->name('expenses');
    Route::get('/reports', ReportDashboard::class)->name('reports');
    Route::get('/reports/sessions', SessionReport::class)->name('reports.sessions');
    
    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
