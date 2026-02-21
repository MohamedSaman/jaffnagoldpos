<?php

namespace App\Livewire\Purchases;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Purchase Due Payments')]
class PurchaseDuePayments extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showPaymentModal = false;
    public ?Purchase $selectedPurchase = null;

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

    public function openPaymentModal(int $purchaseId)
    {
        $this->selectedPurchase = Purchase::with('supplier')->find($purchaseId);
        $this->paymentAmount = (float) $this->selectedPurchase->due_amount;
        $this->paymentDetails['date'] = date('Y-m-d');
        $this->showPaymentModal = true;
    }

    public function closeModal()
    {
        $this->showPaymentModal = false;
        $this->reset(['selectedPurchase', 'paymentAmount', 'paymentMethod', 'paymentDetails']);
    }

    public function savePayment()
    {
        $this->validate();

        if ($this->paymentAmount > $this->selectedPurchase->due_amount) {
            $this->addError('paymentAmount', 'Payment amount cannot exceed due amount.');
            return;
        }

        PurchasePayment::create([
            'purchase_id' => $this->selectedPurchase->id,
            'method'      => $this->paymentMethod,
            'amount'      => $this->paymentAmount,
            'details'     => $this->paymentDetails,
        ]);

        $this->selectedPurchase->paid_amount += $this->paymentAmount;
        $this->selectedPurchase->due_amount -= $this->paymentAmount;
        $this->selectedPurchase->save();

        session()->flash('success', 'Supplier payment recorded successfully.');
        $this->closeModal();
    }

    public function render()
    {
        $purchases = Purchase::with('supplier')
            ->where('due_amount', '>', 0)
            ->where(function($q) {
                $q->where('invoice_no', 'like', '%' . $this->search . '%')
                  ->orWhereHas('supplier', function($sq) {
                      $sq->where('name', 'like', '%' . $this->search . '%');
                  });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.purchases.purchase-due-payments', compact('purchases'));
    }
}
