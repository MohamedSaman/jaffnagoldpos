<?php

namespace App\Livewire\Products;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Categories')]
class CategoryList extends Component
{
    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';

    public function openCreate(): void
    {
        $this->name = '';
        $this->editingId = null;
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->editingId = $id;
        $this->name = $cat->name;
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate(['name' => 'required|string|max:100']);
        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update(['name' => $this->name]);
        } else {
            Category::create(['name' => $this->name]);
        }
        $this->showModal = false;
        $this->name = '';
    }

    public function delete(int $id): void
    {
        Category::findOrFail($id)->delete();
    }

    public function render()
    {
        $categories = Category::withCount('products')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->get();

        return view('livewire.products.category-list', compact('categories'));
    }
}
