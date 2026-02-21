<?php

namespace App\Livewire;

use App\Models\JewelleryRate;
use App\Models\Purity;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Gold Rates')]
class JewelleryRates extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $purity_id = '';
    public string $rate_per_gram = '';
    public string $date = '';

    public function mount(): void
    {
        $this->date = now()->toDateString();
    }

    public function openCreate(): void
    {
        $this->purity_id = '';
        $this->rate_per_gram = '';
        $this->date = now()->toDateString();
        $this->editingId = null;
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $r = JewelleryRate::findOrFail($id);
        $this->editingId = $id;
        $this->purity_id = $r->purity_id;
        $this->rate_per_gram = $r->rate_per_gram;
        $this->date = $r->date->toDateString();
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate([
            'purity_id'    => 'required|exists:purities,id',
            'rate_per_gram'=> 'required|numeric|min:0',
            'date'         => 'required|date',
        ]);

        $data = [
            'purity_id'     => $this->purity_id,
            'rate_per_gram' => $this->rate_per_gram,
            'date'          => $this->date,
        ];

        if ($this->editingId) {
            JewelleryRate::findOrFail($this->editingId)->update($data);
        } else {
            JewelleryRate::create($data);
        }
        $this->showModal = false;
    }

    public function delete(int $id): void
    {
        JewelleryRate::findOrFail($id)->delete();
    }

    public function render()
    {
        $rates   = JewelleryRate::with('purity')->latest('date')->paginate(20);
        $purities = Purity::orderBy('name')->get();
        return view('livewire.jewellery-rates', compact('rates', 'purities'));
    }
}
