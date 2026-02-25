<?php

namespace App\Livewire\ShopStaff;

use App\Models\StaffExpense;
use App\Models\Expense;
use App\Models\POSSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Carbon\Carbon;

#[Layout('components.layouts.shop-staff')]
#[Title('Daily Expenses')]
class ShopStaffExpenses extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $expense_type = '';
    public $amount = '';
    public $description = '';
    public $expense_date = '';
    public $search = '';

    // Modal states
    public $showAddModal = false;
    public $showDeleteModal = false;
    public $expenseToDelete = null;

    protected $rules = [
        'expense_type' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string',
        'expense_date' => 'required|date',
    ];

    public function mount()
    {
        $this->expense_date = date('Y-m-d');
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->resetForm();
    }

    public function addExpense()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Create staff expense with auto-approval
            $staffExpense = StaffExpense::create([
                'staff_id' => Auth::id(),
                'expense_type' => $this->expense_type,
                'amount' => $this->amount,
                'description' => $this->description,
                'expense_date' => $this->expense_date,
                'status' => 'approved',
                'admin_notes' => 'Auto-approved (Shop Staff)',
            ]);

            // Create a regular expense record
            Expense::create([
                'category' => 'Staff Expense - ' . $this->expense_type,
                'amount' => $this->amount,
                'description' => 'Staff: ' . Auth::user()->name . ' - ' . ($this->description ?? $this->expense_type),
                'date' => $this->expense_date,
                'expense_type' => 'daily',
            ]);

            // Update cash in hands — subtract expense amount
            $cashInHandRecord = DB::table('cash_in_hands')->where('key', 'cash_amount')->first();

            if ($cashInHandRecord) {
                DB::table('cash_in_hands')
                    ->where('key', 'cash_amount')
                    ->update([
                        'value' => $cashInHandRecord->value - $this->amount,
                        'updated_at' => now()
                    ]);
            }

            // Update today's POS session if expense is for today
            try {
                if (Carbon::parse($this->expense_date)->isToday()) {
                    $session = POSSession::getTodaySession(Auth::id());
                    if ($session) {
                        $session->expenses = ($session->expenses ?? 0) + $this->amount;
                        $session->save();
                        $session->calculateDifference();
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to update POS session after staff expense: ' . $e->getMessage());
            }

            DB::commit();

            $this->showToast('success', 'Expense added successfully!');
            $this->closeAddModal();
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->showToast('error', 'Error adding expense: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->expense_type = '';
        $this->amount = '';
        $this->description = '';
        $this->expense_date = date('Y-m-d');
        $this->resetValidation();
    }

    public function confirmDelete($expenseId)
    {
        $this->expenseToDelete = $expenseId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->expenseToDelete = null;
        $this->showDeleteModal = false;
    }

    public function deleteExpense()
    {
        try {
            $expense = StaffExpense::where('staff_id', Auth::id())
                ->where('id', $this->expenseToDelete)
                ->first();

            if ($expense) {
                // If approved, add back the amount to cash in hand
                if ($expense->status === 'approved') {
                    $cashInHandRecord = DB::table('cash_in_hands')->where('key', 'cash_amount')->first();
                    if ($cashInHandRecord) {
                        DB::table('cash_in_hands')
                            ->where('key', 'cash_amount')
                            ->update([
                                'value' => $cashInHandRecord->value + $expense->amount,
                                'updated_at' => now()
                            ]);
                    }
                }

                $expense->delete();
                $this->showToast('success', 'Expense deleted successfully.');
            } else {
                $this->showToast('error', 'Expense not found.');
            }
        } catch (\Exception $e) {
            $this->showToast('error', 'Error deleting expense: ' . $e->getMessage());
        }

        $this->cancelDelete();
    }

    public function render()
    {
        $query = StaffExpense::where('staff_id', Auth::id());

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('expense_type', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $expenses = $query->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate totals
        $todayExpenses = StaffExpense::where('staff_id', Auth::id())
            ->whereDate('expense_date', now()->toDateString())
            ->sum('amount');

        $monthExpenses = StaffExpense::where('staff_id', Auth::id())
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        return view('livewire.shop-staff.shop-staff-expenses', [
            'expenses' => $expenses,
            'todayExpenses' => $todayExpenses,
            'monthExpenses' => $monthExpenses,
        ]);
    }

    private function showToast($type, $message)
    {
        $bgColors = [
            'success' => '#10b981',
            'error' => '#ef4444',
            'warning' => '#f59e0b',
            'info' => '#3b82f6',
        ];

        $icons = [
            'success' => '✓',
            'error' => '✕',
            'warning' => '⚠',
            'info' => 'ℹ',
        ];

        $bg = $bgColors[$type] ?? $bgColors['info'];
        $icon = $icons[$type] ?? $icons['info'];
        $escapedMessage = addslashes($message);

        $this->js("
            const toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;top:20px;right:20px;background:{$bg};color:white;padding:16px 24px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:9999;font-size:14px;font-weight:600;display:flex;align-items:center;gap:12px;animation:slideIn 0.3s ease;min-width:300px;max-width:500px;';
            toast.innerHTML = '<span style=\"font-size:20px;font-weight:bold;\">{$icon}</span><span>{$escapedMessage}</span>';
            document.body.appendChild(toast);
            const style = document.createElement('style');
            style.textContent = '@keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } } @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }';
            document.head.appendChild(style);
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        ");
    }
}
