<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Models\SalePayment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Sales Due Payments')]
class SaleDuePayments extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showPaymentModal = false;
    public ?Sale $selectedSale = null;

    // Payment Form
    public float $paymentAmount = 0;
    public string $paymentMethod = 'cash';
    public array $paymentDetails = ['bank_name' => '', 'reference' => '', 'cheque_no' => '', 'date' => ''];

    protected $rules = [
        'paymentAmount' => 'required|numeric|min:1',
        'paymentMethod' => 'required|string',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openPaymentModal(int $saleId)
    {
        $this->selectedSale = Sale::with('customer')->find($saleId);
        $this->paymentAmount = (float) $this->selectedSale->due_amount;
        $this->paymentDetails['date'] = date('Y-m-d');
        $this->showPaymentModal = true;
    }

    public function closeModal()
    {
        $this->showPaymentModal = false;
        $this->reset(['selectedSale', 'paymentAmount', 'paymentMethod', 'paymentDetails']);
    }

    public function savePayment()
    {
        $this->validate();

        if ($this->paymentAmount > $this->selectedSale->due_amount) {
            $this->addError('paymentAmount', 'Payment amount cannot exceed due amount.');
            return;
        }

        SalePayment::create([
            'sale_id' => $this->selectedSale->id,
            'method'  => $this->paymentMethod,
            'amount'  => $this->paymentAmount,
            'details' => $this->paymentDetails,
        ]);

        $this->selectedSale->paid_amount += $this->paymentAmount;
        $this->selectedSale->due_amount -= $this->paymentAmount;
        $this->selectedSale->save();

        session()->flash('success', 'Payment recorded successfully.');
        $this->closeModal();
    }

    public function render()
    {
        $sales = Sale::with('customer')
            ->where('due_amount', '>', 0)
            ->where(function($q) {
                $q->where('invoice_no', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function($sq) {
                      $sq->where('name', 'like', '%' . $this->search . '%');
                  });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.sales.sale-due-payments', compact('sales'));
    }
}
