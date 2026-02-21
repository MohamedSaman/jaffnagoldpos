<?php

namespace App\Livewire\Reports;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Reports')]
class ReportDashboard extends Component
{
    public string $reportType = 'daily';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo   = now()->toDateString();
    }

    public function render()
    {
        $sales = Sale::with(['customer', 'items'])
            ->whereBetween('created_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59'])
            ->latest()
            ->get();

        $purchases = Purchase::with('supplier')
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->latest()
            ->get();

        $expenses = Expense::whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->latest('date')
            ->get();

        $totalSales     = $sales->sum('grand_total');
        $totalPurchases = $purchases->sum('total_amount');
        $totalExpenses  = $expenses->sum('amount');
        $totalDue       = $sales->sum('due_amount');
        $grossProfit    = $totalSales - $totalPurchases - $totalExpenses;

        $lowStockProducts = Product::with(['category', 'purity'])
            ->where('stock_quantity', '<=', 5)
            ->where('status', true)
            ->orderBy('stock_quantity')
            ->get();

        // Daily breakdown
        $dailySales = Sale::selectRaw('DATE(created_at) as date, SUM(grand_total) as total, COUNT(*) as count')
            ->whereBetween('created_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('livewire.reports.report-dashboard', compact(
            'sales', 'purchases', 'expenses',
            'totalSales', 'totalPurchases', 'totalExpenses', 'totalDue', 'grossProfit',
            'lowStockProducts', 'dailySales'
        ));
    }
}
