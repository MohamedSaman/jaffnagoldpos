<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Suppliers')]
class SupplierList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showHistory = false;
    public ?int $editingId = null;
    public ?int $viewingId = null;

    public string $name = '';
    public string $phone = '';
    public string $address = '';
    public string $balance = '0';

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $s = Supplier::findOrFail($id);
        $this->editingId = $id;
        $this->name = $s->name;
        $this->phone = $s->phone ?? '';
        $this->address = $s->address ?? '';
        $this->balance = $s->balance;
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openHistory(int $id): void
    {
        $this->viewingId = $id;
        $this->showHistory = true;
    }

    public function save(): void
    {
        $this->validate([
            'name'    => 'required|string|max:200',
            'phone'   => 'nullable|string|max:20',
            'balance' => 'required|numeric|min:0',
        ]);

        $data = [
            'name'    => $this->name,
            'phone'   => $this->phone ?: null,
            'address' => $this->address ?: null,
            'balance' => $this->balance,
        ];

        if ($this->editingId) {
            Supplier::findOrFail($this->editingId)->update($data);
        } else {
            Supplier::create($data);
        }
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Supplier::findOrFail($id)->delete();
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'phone', 'address']);
        $this->balance = '0';
        $this->resetErrorBag();
    }

    public function render()
    {
        $suppliers = Supplier::withCount('purchases')
            ->withSum('purchases', 'total_amount')
            ->withSum('purchases', 'due_amount')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(15);

        $viewingSupplier = $this->viewingId
            ? Supplier::with(['purchases' => fn($q) => $q->latest()->take(10)])->find($this->viewingId)
            : null;

        return view('livewire.suppliers.supplier-list', compact('suppliers', 'viewingSupplier'));
    }
}
