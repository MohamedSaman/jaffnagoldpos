<?php

namespace App\Livewire;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Customer;
use App\Models\JewelleryRate;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $today = now()->toDateString();
        $month = now()->format('Y-m');

        $todaySales       = Sale::whereDate('created_at', $today)->sum('grand_total');
        $todaySalesCount  = Sale::whereDate('created_at', $today)->count();
        $monthlySales     = Sale::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('grand_total');
        $totalDue         = Sale::sum('due_amount');
        $lowStock         = Product::where('stock_quantity', '<=', 2)->where('status', true)->count();
        $totalCustomers   = Customer::count();
        $totalExpenses    = Expense::whereDate('date', $today)->sum('amount');
        $currentRate      = JewelleryRate::with('purity')->latest('date')->first();

        $recentSales = Sale::with(['customer', 'items'])
            ->latest()
            ->take(8)
            ->get();

        $lowStockProducts = Product::with(['category', 'purity'])
            ->where('stock_quantity', '<=', 2)
            ->where('status', true)
            ->take(5)
            ->get();

        return view('livewire.dashboard', compact(
            'todaySales', 'todaySalesCount', 'monthlySales', 'totalDue',
            'lowStock', 'totalCustomers', 'totalExpenses', 'currentRate',
            'recentSales', 'lowStockProducts'
        ))->title('Dashboard');
    }
}
