<?php

namespace App\Livewire\Reports;

use App\Models\DailySession;
use App\Models\SalePayment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('POS Sessions History')]
class SessionReport extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        $this->dateFrom = date('Y-m-d', strtotime('-30 days'));
        $this->dateTo = date('Y-m-d');
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function render()
    {
        $sessions = DailySession::with('user')
            ->whereBetween('opened_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59'])
            ->latest()
            ->paginate(15);

        // For each session, we calculate the total payments received during that window
        $sessions->getCollection()->transform(function ($session) {
            $session->total_payments = SalePayment::whereHas('sale', function($q) use ($session) {
                    $q->where('user_id', $session->user_id);
                })
                ->where('created_at', '>=', $session->opened_at)
                ->when($session->closed_at, function($q) use ($session) {
                    $q->where('created_at', '<=', $session->closed_at);
                })
                ->sum('amount');
            
            $session->expected_closing = $session->opening_balance + $session->total_payments;
            $session->difference = $session->status === 'closed' ? ($session->closing_balance - $session->expected_closing) : 0;
            
            return $session;
        });

        return view('livewire.reports.session-report', compact('sessions'));
    }
}
