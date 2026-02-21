<?php

namespace App\Livewire\Products;

use App\Models\Purity;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Purities')]
class PurityList extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $percentage = '';

    public function openCreate(): void
    {
        $this->name = '';
        $this->percentage = '';
        $this->editingId = null;
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $p = Purity::findOrFail($id);
        $this->editingId = $id;
        $this->name = $p->name;
        $this->percentage = $p->percentage;
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate([
            'name'       => 'required|string|max:50',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $data = ['name' => $this->name, 'percentage' => $this->percentage];

        if ($this->editingId) {
            Purity::findOrFail($this->editingId)->update($data);
        } else {
            Purity::create($data);
        }
        $this->showModal = false;
    }

    public function delete(int $id): void
    {
        Purity::findOrFail($id)->delete();
    }

    public function render()
    {
        $purities = Purity::withCount('products')->latest()->get();
        return view('livewire.products.purity-list', compact('purities'));
    }
}
