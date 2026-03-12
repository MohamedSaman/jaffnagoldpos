<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\ProductDetail;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Concerns\WithDynamicLayout;

#[Title("Barcode Print")]
class BarcodePrint extends Component
{
    use WithDynamicLayout;
    use WithPagination;

    public $search = '';
    public $perPage = 25;

    // Selected product IDs for printing
    public $selectedProducts = [];
    public $selectAll = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Select all unprinted barcode products (current page visible IDs)
            $this->selectedProducts = $this->getUnprintedProducts()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedProducts = [];
        }
    }

    /**
     * Get products with unprinted barcodes (barcode_printed = 'No')
     */
    private function getUnprintedProducts()
    {
        return ProductDetail::leftJoin('product_prices', function ($join) {
            $join->on('product_details.id', '=', 'product_prices.product_id')
                ->where('product_prices.pricing_mode', '=', 'single')
                ->whereNull('product_prices.variant_id');
        })
            ->leftJoin('product_stocks', function ($join) {
                $join->on('product_details.id', '=', 'product_stocks.product_id')
                    ->whereNull('product_stocks.variant_id');
            })
            ->select('product_details.*', 'product_prices.retail_price', 'product_stocks.available_stock')
            ->where('product_details.barcode_printed', 'No')
            ->whereNotNull('product_details.barcode')
            ->where('product_details.barcode', '!=', '')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('product_details.name', 'like', '%' . $this->search . '%')
                        ->orWhere('product_details.code', 'like', '%' . $this->search . '%')
                        ->orWhere('product_details.barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('product_details.created_at', 'desc')
            ->get();
    }

    /**
     * Mark selected products as printed
     */
    public function markAsPrinted()
    {
        if (empty($this->selectedProducts)) {
            $this->js("Swal.fire({icon: 'warning', title: 'No Selection', text: 'Please select at least one product to mark as printed.', timer: 2000, showConfirmButton: false})");
            return;
        }

        ProductDetail::whereIn('id', $this->selectedProducts)
            ->update(['barcode_printed' => 'Yes']);

        $count = count($this->selectedProducts);
        $this->selectedProducts = [];
        $this->selectAll = false;

        $this->js("Swal.fire({icon: 'success', title: 'Marked as Printed', text: '{$count} product(s) barcode marked as printed.', timer: 2000, showConfirmButton: false})");
    }

    /**
     * Mark a single product as printed
     */
    public function markSingleAsPrinted($productId)
    {
        ProductDetail::where('id', $productId)->update(['barcode_printed' => 'Yes']);

        // Remove from selection if it was selected
        $this->selectedProducts = array_values(array_diff($this->selectedProducts, [(string) $productId]));

        $this->js("Swal.fire({icon: 'success', title: 'Done', text: 'Barcode marked as printed.', timer: 1500, showConfirmButton: false})");
    }

    public function render()
    {
        $products = ProductDetail::leftJoin('product_prices', function ($join) {
            $join->on('product_details.id', '=', 'product_prices.product_id')
                ->where('product_prices.pricing_mode', '=', 'single')
                ->whereNull('product_prices.variant_id');
        })
            ->leftJoin('product_stocks', function ($join) {
                $join->on('product_details.id', '=', 'product_stocks.product_id')
                    ->whereNull('product_stocks.variant_id');
            })
            ->select('product_details.*', 'product_prices.retail_price', 'product_stocks.available_stock')
            ->where('product_details.barcode_printed', 'No')
            ->whereNotNull('product_details.barcode')
            ->where('product_details.barcode', '!=', '')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('product_details.name', 'like', '%' . $this->search . '%')
                        ->orWhere('product_details.code', 'like', '%' . $this->search . '%')
                        ->orWhere('product_details.barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('product_details.created_at', 'desc')
            ->paginate($this->perPage);

        $totalUnprinted = ProductDetail::where('barcode_printed', 'No')
            ->whereNotNull('barcode')
            ->where('barcode', '!=', '')
            ->count();

        return view('livewire.admin.barcode-print', [
            'products' => $products,
            'totalUnprinted' => $totalUnprinted,
        ])->layout($this->layout);
    }
}
