<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\Sale;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Customers')]
class CustomerList extends Component
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
    public string $opening_balance = '0';

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $c = Customer::findOrFail($id);
        $this->editingId = $id;
        $this->name = $c->name;
        $this->phone = $c->phone ?? '';
        $this->address = $c->address ?? '';
        $this->opening_balance = $c->opening_balance;
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
            'name'            => 'required|string|max:200',
            'phone'           => 'nullable|string|max:20',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $data = [
            'name'            => $this->name,
            'phone'           => $this->phone ?: null,
            'address'         => $this->address ?: null,
            'opening_balance' => $this->opening_balance,
        ];

        if ($this->editingId) {
            Customer::findOrFail($this->editingId)->update($data);
        } else {
            Customer::create($data);
        }
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Customer::findOrFail($id)->delete();
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'phone', 'address']);
        $this->opening_balance = '0';
        $this->resetErrorBag();
    }

    public function render()
    {
        $customers = Customer::withCount('sales')
            ->withSum('sales', 'grand_total')
            ->withSum('sales', 'due_amount')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(15);

        $viewingCustomer = $this->viewingId
            ? Customer::with(['sales' => fn($q) => $q->latest()->take(10)])->find($this->viewingId)
            : null;

        return view('livewire.customers.customer-list', compact('customers', 'viewingCustomer'));
    }
}
