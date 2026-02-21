<?php

namespace App\Livewire\Purchases;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Purchases')]
class PurchaseList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showView = false;
    public ?int $viewingId = null;

    // Form
    public string $supplier_id = '';
    public string $date = '';
    public string $paid_amount = '0';
    public string $payment_note = '';
    public array $items = [];

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->addItem();
    }

    public function openCreate(): void
    {
        $this->supplier_id = '';
        $this->date = now()->toDateString();
        $this->paid_amount = '0';
        $this->items = [];
        $this->addItem();
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'product_id'   => '',
            'weight'       => '',
            'cost_per_gram'=> '',
            'total'        => 0,
        ];
    }

    public function removeItem(int $index): void
    {
        array_splice($this->items, $index, 1);
    }

    public function updatedItems(): void
    {
        foreach ($this->items as $i => $item) {
            if ($item['weight'] && $item['cost_per_gram']) {
                $this->items[$i]['total'] = round((float)$item['weight'] * (float)$item['cost_per_gram'], 2);
            }
        }
    }

    public function getTotalAmountProperty(): float
    {
        return array_sum(array_column($this->items, 'total'));
    }

    public function getDueAmountProperty(): float
    {
        return max(0, $this->totalAmount - (float)$this->paid_amount);
    }

    public function save(): void
    {
        $this->validate([
            'supplier_id'    => 'required|exists:suppliers,id',
            'date'           => 'required|date',
            'paid_amount'    => 'required|numeric|min:0',
            'items'          => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.weight'        => 'required|numeric|min:0.001',
            'items.*.cost_per_gram' => 'required|numeric|min:0',
        ]);

        $purchase = Purchase::create([
            'supplier_id'  => $this->supplier_id,
            'invoice_no'   => Purchase::generateInvoiceNo(),
            'total_amount' => $this->totalAmount,
            'paid_amount'  => $this->paid_amount,
            'due_amount'   => $this->dueAmount,
            'date'         => $this->date,
        ]);

        foreach ($this->items as $item) {
            PurchaseItem::create([
                'purchase_id'  => $purchase->id,
                'product_id'   => $item['product_id'],
                'weight'       => $item['weight'],
                'cost_per_gram'=> $item['cost_per_gram'],
                'total'        => $item['total'],
            ]);
            // Add stock
            Product::where('id', $item['product_id'])->increment('stock_quantity');
        }

        $this->showModal = false;
    }

    public function viewPurchase(int $id): void
    {
        $this->viewingId = $id;
        $this->showView = true;
    }

    public function render()
    {
        $purchases = Purchase::with('supplier')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('invoice_no', 'like', "%{$this->search}%")
                  ->orWhereHas('supplier', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
            }))
            ->latest()
            ->paginate(15);

        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $products  = Product::with('purity')->where('status', true)->orderBy('name')->get(['id', 'name', 'product_code', 'purity_id']);

        $viewingPurchase = $this->viewingId
            ? Purchase::with(['supplier', 'items.product'])->find($this->viewingId)
            : null;

        return view('livewire.purchases.purchase-list', compact('purchases', 'suppliers', 'products', 'viewingPurchase'));
    }
}
