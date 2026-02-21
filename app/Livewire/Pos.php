<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\JewelleryRate;
use App\Models\DailySession;
use App\Models\SalePayment;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.pos-layout')]
#[Title('POS – Point of Sale')]
class Pos extends Component
{
    public string $search = '';
    public array $cart = [];
    public ?int $customerId = null;
    public float $discount = 0;
    
    // Payment UI
    public bool $showPaymentModal = false;
    public string $mainPaymentMethod = 'cash';
    
    // Detailed Payment State
    public array $paymentDetails = [
        'bank_name' => '',
        'reference' => '',
        'amount'    => 0,
    ];
    public array $cheques = []; // For multiple cheques
    
    public bool $showInvoice = false;
    public ?Sale $lastSale = null;

    // Daily Session State
    public bool $hasActiveSession = false;
    public float $openingCashInput = 0;
    public ?DailySession $currentSession = null;
    public float $todayEarnings = 0;

    // Product search
    public array $searchResults = [];
    public bool $showResults = false;

    // Close Session State
    public bool $showCloseSessionModal = false;
    public float $closingBalance = 0;

    // Quick Customer Creation
    public bool $showCustomerModal = false;
    public string $newCustName = '';
    public string $newCustPhone = '';
    public string $newCustAddress = '';

    public function mount()
    {
        $this->checkSession();
        $this->addCheque(); // Initial cheque row
    }

    private function checkSession()
    {
        $this->currentSession = DailySession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest()
            ->first();
        
        $this->hasActiveSession = (bool) $this->currentSession;
        
        if ($this->hasActiveSession) {
            $this->calculateEarnings();
        }
    }

    private function calculateEarnings()
    {
        if (!$this->currentSession) return;
        
        $this->todayEarnings = SalePayment::whereHas('sale', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->where('created_at', '>=', $this->currentSession->opened_at)
            ->sum('amount');
    }

    public function startSession()
    {
        $this->currentSession = DailySession::create([
            'user_id' => auth()->id(),
            'opening_balance' => $this->openingCashInput,
            'status' => 'open',
            'opened_at' => now(),
        ]);
        $this->hasActiveSession = true;
        $this->calculateEarnings();
    }

    public function closeSession()
    {
        if (!$this->currentSession) return;

        $this->currentSession->update([
            'closing_balance' => $this->closingBalance,
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return redirect()->route('dashboard');
    }

    public function saveCustomer()
    {
        $this->validate([
            'newCustName' => 'required|string|max:255',
            'newCustPhone' => 'required|string|max:20',
        ]);

        $customer = Customer::create([
            'name' => $this->newCustName,
            'phone' => $this->newCustPhone,
            'address' => $this->newCustAddress,
        ]);

        $this->customerId = $customer->id;
        $this->showCustomerModal = false;
        $this->reset(['newCustName', 'newCustPhone', 'newCustAddress']);
        
        session()->flash('success', 'Customer added successfully.');
    }

    public function addCheque()
    {
        $this->cheques[] = [
            'bank_name' => '',
            'cheque_no' => '',
            'amount' => 0,
            'date' => date('Y-m-d'),
            'reference' => ''
        ];
    }

    public function removeCheque($index)
    {
        unset($this->cheques[$index]);
        $this->cheques = array_values($this->cheques);
    }

    public function updatedSearch(): void
    {
        if (strlen($this->search) >= 2) {
            $this->searchResults = Product::with(['category', 'purity'])
                ->where('status', true)
                ->where('stock_quantity', '>', 0)
                ->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('product_code', 'like', "%{$this->search}%")
                      ->orWhere('barcode', 'like', "%{$this->search}%");
                })
                ->take(8)
                ->get()
                ->toArray();
            $this->showResults = count($this->searchResults) > 0;
        } else {
            $this->searchResults = [];
            $this->showResults = false;
        }
    }

    public function addToCart(int $productId): void
    {
        $product = Product::with('purity.latestRate')->find($productId);
        if (!$product) return;

        $rate = JewelleryRate::where('purity_id', $product->purity_id)->latest('date')->first();
        $ratePerGram = $rate ? (float) $rate->rate_per_gram : 0;

        $key = 'p_' . $productId;

        if (isset($this->cart[$key])) {
            $this->cart[$key]['qty']++;
        } else {
            $weight = (float) $product->net_weight;
            $calc   = $product->calculatePrice($ratePerGram, $weight);

            $this->cart[$key] = [
                'product_id'   => $product->id,
                'name'         => $product->name,
                'product_code' => $product->product_code,
                'purity'       => $product->purity->name ?? '',
                'weight'       => $weight,
                'rate'         => $ratePerGram,
                'making_charge'=> $calc['making_charge'],
                'wastage_amount'=> $calc['wastage_amount'],
                'total'        => $calc['total'],
                'qty'          => 1,
                'making_charge_type' => $product->making_charge_type,
                'making_charge_raw'  => (float) $product->making_charge,
                'wastage_percentage' => (float) $product->wastage_percentage,
            ];
        }

        $this->search = '';
        $this->searchResults = [];
        $this->showResults = false;
    }

    public function updateWeight(string $key, float $weight): void
    {
        if (!isset($this->cart[$key])) return;
        $item = &$this->cart[$key];
        $item['weight'] = $weight;

        $goldValue = $weight * $item['rate'];
        $wastage   = $goldValue * ($item['wastage_percentage'] / 100);
        $making    = $item['making_charge_type'] === 'per_gram'
            ? $weight * $item['making_charge_raw']
            : $item['making_charge_raw'];

        $item['wastage_amount'] = round($wastage, 2);
        $item['making_charge']  = round($making, 2);
        $item['total']          = round($goldValue + $wastage + $making, 2);
    }

    public function removeFromCart(string $key): void
    {
        unset($this->cart[$key]);
    }

    public function openPayment()
    {
        if (empty($this->cart)) {
            $this->addError('cart', 'Cart is empty.');
            return;
        }
        $this->paymentDetails['amount'] = $this->grandTotal;
        if(isset($this->cheques[0])) $this->cheques[0]['amount'] = $this->grandTotal;
        $this->showPaymentModal = true;
    }

    public function completeSale(): void
    {
        $paidAmount = 0;
        $payments = [];

        if ($this->mainPaymentMethod === 'cash') {
            $paidAmount = (float) $this->paymentDetails['amount'];
            $payments[] = ['method' => 'cash', 'amount' => $paidAmount];
        } elseif ($this->mainPaymentMethod === 'bank_transfer') {
            $paidAmount = (float) $this->paymentDetails['amount'];
            $payments[] = [
                'method' => 'bank_transfer',
                'amount' => $paidAmount,
                'details' => [
                    'bank_name' => $this->paymentDetails['bank_name'],
                    'reference' => $this->paymentDetails['reference']
                ]
            ];
        } elseif ($this->mainPaymentMethod === 'cheque') {
            foreach ($this->cheques as $chq) {
                $paidAmount += (float) $chq['amount'];
                $payments[] = [
                    'method' => 'cheque',
                    'amount' => $chq['amount'],
                    'details' => [
                        'bank_name' => $chq['bank_name'],
                        'cheque_no' => $chq['cheque_no'],
                        'date'      => $chq['date'],
                        'reference' => $chq['reference']
                    ]
                ];
            }
        } elseif ($this->mainPaymentMethod === 'credit') {
            $paidAmount = 0;
            $payments[] = ['method' => 'credit', 'amount' => 0];
        }

        $sale = Sale::create([
            'invoice_no'     => Sale::generateInvoiceNo(),
            'customer_id'    => $this->customerId ?: null,
            'total_amount'   => $this->subtotal,
            'discount'       => $this->discount,
            'grand_total'    => $this->grandTotal,
            'paid_amount'    => $paidAmount,
            'due_amount'     => max(0, $this->grandTotal - $paidAmount),
            'payment_method' => $this->mainPaymentMethod,
            'user_id'        => auth()->id(),
        ]);

        foreach ($this->cart as $item) {
            SaleItem::create([
                'sale_id'        => $sale->id,
                'product_id'     => $item['product_id'],
                'weight'         => $item['weight'],
                'rate'           => $item['rate'],
                'making_charge'  => $item['making_charge'],
                'wastage_amount' => $item['wastage_amount'],
                'total'          => $item['total'],
            ]);
            Product::where('id', $item['product_id'])->decrement('stock_quantity', $item['qty']);
        }

        foreach ($payments as $p) {
            SalePayment::create([
                'sale_id' => $sale->id,
                'method'  => $p['method'],
                'amount'  => $p['amount'],
                'details' => $p['details'] ?? null
            ]);
        }

        $this->lastSale = $sale->load(['items.product', 'customer', 'payments']);
        $this->showInvoice = true;
        $this->showPaymentModal = false;
        $this->calculateEarnings();
    }

    public function newSale(): void
    {
        $this->reset(['cart', 'search', 'discount', 'customerId', 'showInvoice', 'lastSale', 'searchResults', 'showResults', 'showPaymentModal', 'mainPaymentMethod', 'paymentDetails', 'cheques']);
        $this->addCheque();
    }

    public function getSubtotalProperty(): float
    {
        return array_sum(array_column($this->cart, 'total'));
    }

    public function getGrandTotalProperty(): float
    {
        return max(0, $this->subtotal - $this->discount);
    }

    public function getDueAmountProperty(): float
    {
        return max(0, $this->grandTotal);
    }

    public function render()
    {
        $customers = Customer::orderBy('name')->get(['id', 'name', 'phone']);
        return view('livewire.pos', compact('customers'));
    }
}
