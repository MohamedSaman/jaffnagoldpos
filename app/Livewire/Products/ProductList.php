<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\Purity;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Products')]
class ProductList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';
    public string $filterPurity = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Form fields
    public string $product_code = '';
    public string $name = '';
    public string $category_id = '';
    public string $purity_id = '';
    public string $gross_weight = '';
    public string $stone_weight = '';
    public string $net_weight = '';
    public string $making_charge_type = 'per_gram';
    public string $making_charge = '';
    public string $wastage_percentage = '';
    public int $stock_quantity = 1;
    public string $barcode = '';
    public bool $status = true;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterCategory(): void { $this->resetPage(); }
    public function updatingFilterPurity(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $p = Product::findOrFail($id);
        $this->editingId = $id;
        $this->product_code = $p->product_code;
        $this->name = $p->name;
        $this->category_id = $p->category_id;
        $this->purity_id = $p->purity_id;
        $this->gross_weight = $p->gross_weight ?? '';
        $this->stone_weight = $p->stone_weight ?? '';
        $this->net_weight = $p->net_weight ?? '';
        $this->making_charge_type = $p->making_charge_type;
        $this->making_charge = $p->making_charge;
        $this->wastage_percentage = $p->wastage_percentage ?? '';
        $this->stock_quantity = $p->stock_quantity;
        $this->barcode = $p->barcode ?? '';
        $this->status = $p->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'product_code'       => 'required|string|max:50',
            'name'               => 'required|string|max:200',
            'category_id'        => 'required|exists:categories,id',
            'purity_id'          => 'required|exists:purities,id',
            'making_charge_type' => 'required|in:fixed,per_gram',
            'making_charge'      => 'required|numeric|min:0',
            'stock_quantity'     => 'required|integer|min:0',
        ]);

        $data = [
            'product_code'       => $this->product_code,
            'name'               => $this->name,
            'category_id'        => $this->category_id,
            'purity_id'          => $this->purity_id,
            'gross_weight'       => $this->gross_weight ?: null,
            'stone_weight'       => $this->stone_weight ?: null,
            'net_weight'         => $this->net_weight ?: null,
            'making_charge_type' => $this->making_charge_type,
            'making_charge'      => $this->making_charge,
            'wastage_percentage' => $this->wastage_percentage ?: null,
            'stock_quantity'     => $this->stock_quantity,
            'barcode'            => $this->barcode ?: null,
            'status'             => $this->status,
        ];

        if ($this->editingId) {
            Product::findOrFail($this->editingId)->update($data);
        } else {
            Product::create($data);
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Product::findOrFail($this->deletingId)->delete();
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    private function resetForm(): void
    {
        $this->reset([
            'product_code', 'name', 'category_id', 'purity_id',
            'gross_weight', 'stone_weight', 'net_weight',
            'making_charge_type', 'making_charge', 'wastage_percentage',
            'stock_quantity', 'barcode', 'status',
        ]);
        $this->making_charge_type = 'per_gram';
        $this->stock_quantity = 1;
        $this->status = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $products = Product::with(['category', 'purity'])
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('product_code', 'like', "%{$this->search}%")
                  ->orWhere('barcode', 'like', "%{$this->search}%");
            }))
            ->when($this->filterCategory, fn($q) => $q->where('category_id', $this->filterCategory))
            ->when($this->filterPurity, fn($q) => $q->where('purity_id', $this->filterPurity))
            ->latest()
            ->paginate(15);

        $categories = Category::orderBy('name')->get();
        $purities   = Purity::orderBy('name')->get();

        return view('livewire.products.product-list', compact('products', 'categories', 'purities'));
    }
}
