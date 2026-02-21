<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Sales List')]
class SaleList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showView = false;
    public ?int $viewingId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function viewDetails(int $id): void
    {
        $this->viewingId = $id;
        $this->showView = true;
    }

    public function deleteSale(int $id): void
    {
        // For a real app, we might want to restock items here
        Sale::findOrFail($id)->delete();
        $this->dispatch('notify', ['message' => 'Sale deleted successfully', 'type' => 'success']);
    }

    public function render()
    {
        $sales = Sale::with(['customer'])
            ->when($this->search, function ($q) {
                $q->where('invoice_no', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
            })
            ->latest()
            ->paginate(15);

        $viewingSale = $this->viewingId 
            ? Sale::with(['customer', 'items.product.purity'])->find($this->viewingId) 
            : null;

        return view('livewire.sales.sale-list', compact('sales', 'viewingSale'));
    }
}
