<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Expenses')]
class ExpenseList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterMonth = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $title = '';
    public string $amount = '';
    public string $date = '';
    public string $description = '';

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->filterMonth = now()->format('Y-m');
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $e = Expense::findOrFail($id);
        $this->editingId = $id;
        $this->title = $e->title;
        $this->amount = $e->amount;
        $this->date = $e->date->toDateString();
        $this->description = $e->description ?? '';
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate([
            'title'  => 'required|string|max:200',
            'amount' => 'required|numeric|min:0',
            'date'   => 'required|date',
        ]);

        $data = [
            'title'       => $this->title,
            'amount'      => $this->amount,
            'date'        => $this->date,
            'description' => $this->description ?: null,
        ];

        if ($this->editingId) {
            Expense::findOrFail($this->editingId)->update($data);
        } else {
            Expense::create($data);
        }
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Expense::findOrFail($id)->delete();
    }

    private function resetForm(): void
    {
        $this->reset(['title', 'amount', 'description']);
        $this->date = now()->toDateString();
        $this->resetErrorBag();
    }

    public function render()
    {
        $expenses = Expense::when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->filterMonth, fn($q) => $q->whereYear('date', substr($this->filterMonth, 0, 4))
                ->whereMonth('date', substr($this->filterMonth, 5, 2)))
            ->latest('date')
            ->paginate(15);

        $monthTotal = Expense::when($this->filterMonth, fn($q) => $q->whereYear('date', substr($this->filterMonth, 0, 4))
            ->whereMonth('date', substr($this->filterMonth, 5, 2)))->sum('amount');

        return view('livewire.expenses.expense-list', compact('expenses', 'monthTotal'));
    }
}
