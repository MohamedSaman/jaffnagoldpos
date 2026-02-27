<?php

namespace App\Livewire\ShopStaff;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Sale;

#[Layout('components.layouts.shop-staff')]
#[Title('Delivery Packing')]
class DeliveryPacking extends Component
{
    public $sales;
    public $search = '';
    public $todayOnly = false;
    public $filterStatus = 'pending';

    // overview counts
    public $totalCount = 0;
    public $pendingCount = 0;
    public $packedCount = 0;
    public $canceledCount = 0;

    // Modal state
    public $showModal = false;
    public $modalSale = null;

    public function mount()
    {
        $this->loadOverview();
        $this->loadSales();
    }

    public function updatedSearch()
    {
        $this->loadSales();
    }

    public function updatedTodayOnly()
    {
        $this->loadSales();
    }

    public function loadSales()
    {
        $query = Sale::query();

        // Filter by delivery status or special 'canceled' (uses sale.status)
        if ($this->filterStatus && $this->filterStatus !== 'all') {
            if ($this->filterStatus === 'canceled') {
                $query->where('status', 'rejected');
            } else {
                $query->where('delivery_status', $this->filterStatus);
            }
        } else {
            // default to pending when not 'all'
            if (!$this->filterStatus) {
                $query->where('delivery_status', 'pending');
            }
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_no', 'like', '%' . $this->search . '%')
                    ->orWhere('walking_customer_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($cq) {
                        $cq->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }
        if ($this->todayOnly) {
            $query->whereDate('created_at', now()->toDateString());
        }
        $this->sales = $query->with(['items.product', 'customer', 'deliverySale'])->orderBy('created_at', 'desc')->get();
    }

    protected function loadOverview()
    {
        $this->totalCount = Sale::count();
        $this->pendingCount = Sale::where('delivery_status', 'pending')->count();
        $this->packedCount = Sale::where('delivery_status', 'packed')->count();
        $this->canceledCount = Sale::where('status', 'rejected')->count();
    }

    public function setFilter($status)
    {
        $this->filterStatus = $status;
        $this->loadSales();
    }

    public function showPackingModal($saleId)
    {
        $this->modalSale = Sale::with(['items.product', 'customer', 'deliverySale'])->find($saleId);
        $this->showModal = true;
    }

    /**
     * Get display customer name for a sale.
     * Checks: customer relationship -> walking_customer_name -> deliverySale->customer_details -> fallback
     */
    public function getCustomerDisplayName($sale)
    {
        // 1. If there is a linked customer record (not Walking Customer)
        if ($sale->customer && $sale->customer->name && $sale->customer->name !== 'Walking Customer') {
            return $sale->customer->name;
        }

        // 2. If walking_customer_name is filled
        if (!empty($sale->walking_customer_name)) {
            return $sale->walking_customer_name;
        }

        // 3. If deliverySale has customer_details
        if ($sale->deliverySale && !empty($sale->deliverySale->customer_details)) {
            // Extract just the name from "Customer: Name | Phone: xxx" format or return as-is
            $details = $sale->deliverySale->customer_details;
            if (preg_match('/Customer:\s*([^|]+)/i', $details, $matches)) {
                return trim($matches[1]);
            }
            return $details;
        }

        // 4. Walking Customer with name
        if ($sale->customer && $sale->customer->name === 'Walking Customer') {
            return 'Walk-in Customer';
        }

        return 'Walk-in Customer';
    }

    /**
     * Get customer phone for a sale
     */
    public function getCustomerPhone($sale)
    {
        if ($sale->customer && $sale->customer->phone) {
            return $sale->customer->phone;
        }

        if (!empty($sale->walking_customer_phone)) {
            return $sale->walking_customer_phone;
        }

        if ($sale->deliverySale && !empty($sale->deliverySale->customer_details)) {
            if (preg_match('/Phone:\s*([^|\n]+)/i', $sale->deliverySale->customer_details, $matches)) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    /**
     * Get delivery info for a sale
     */
    public function getDeliveryInfo($sale)
    {
        if (!$sale->deliverySale) {
            return null;
        }

        return [
            'method' => $sale->deliverySale->delivery_method ?? 'N/A',
            'payment' => $sale->deliverySale->payment_method ?? 'N/A',
            'barcode' => $sale->deliverySale->delivery_barcode ?? null,
            'charge' => $sale->deliverySale->delivery_charge ?? 0,
            'customer_details' => $sale->deliverySale->customer_details ?? null,
        ];
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalSale = null;
    }

    public function markPacked()
    {
        if ($this->modalSale) {
            $this->modalSale->delivery_status = 'packed';
            $this->modalSale->save();
            $this->closeModal();
            $this->loadOverview();
            $this->loadSales();
            session()->flash('success', 'Sale marked as packed!');
        }
    }

    public function render()
    {
        return view('livewire.shop-staff.delivery-packing', [
            'sales' => $this->sales,
            'showModal' => $this->showModal,
            'modalSale' => $this->modalSale
        ]);
    }
}


