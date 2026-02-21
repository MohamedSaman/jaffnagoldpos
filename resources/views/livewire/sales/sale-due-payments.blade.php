<div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Sales Due Payments</h2>
        </div>

        <div style="margin-bottom: 24px;">
            <div class="search-wrap" style="max-width: 400px;">
                <span class="search-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search by Invoice # or Customer...">
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th style="text-align: right;">Total Bill</th>
                        <th style="text-align: right;">Paid</th>
                        <th style="text-align: right;">Due Amount</th>
                        <th style="text-align: right;">Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $s)
                    <tr>
                        <td>{{ $s->created_at->format('d M Y') }}</td>
                        <td><span style="font-weight: 700;">{{ $s->invoice_no }}</span></td>
                        <td>
                            <div style="font-weight: 600;">{{ $s->customer?->name ?? 'Walk-in' }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ $s->customer?->phone }}</div>
                        </td>
                        <td style="text-align: right;">Rs.{{ number_format($s->grand_total, 2) }}</td>
                        <td style="text-align: right; color: var(--success);">Rs.{{ number_format($s->paid_amount, 2) }}</td>
                        <td style="text-align: right; color: var(--danger); font-weight: 700;">Rs.{{ number_format($s->due_amount, 2) }}</td>
                        <td style="text-align: right;">
                            <span class="badge badge-red">Partial Paid</span>
                        </td>
                        <td style="text-align: center;">
                            <button wire:click="openPaymentModal({{ $s->id }})" class="btn btn-gold btn-sm">
                                Record Payment
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            No pending due amounts found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">
            {{ $sales->links() }}
        </div>
    </div>

    @if($showPaymentModal && $selectedSale)
    <div class="modal-backdrop">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">Record Payment: {{ $selectedSale->invoice_no }}</div>
                <button wire:click="closeModal" class="btn-close">✕</button>
            </div>
            <div class="modal-body">
                <div style="background: var(--bg3); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-size: 13px; color: var(--text-muted);">Customer:</span>
                        <span style="font-weight: 600;">{{ $selectedSale->customer?->name ?? 'Walk-in' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 13px; color: var(--text-muted);">Total Pending:</span>
                        <span style="font-weight: 700; color: var(--danger);">Rs.{{ number_format($selectedSale->due_amount, 2) }}</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Payment Amount (Rs.)</label>
                    <input type="number" wire:model="paymentAmount" class="form-control" step="0.01">
                    @error('paymentAmount') <span style="font-size: 11px; color: var(--danger);">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select wire:model.live="paymentMethod" class="form-control">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>

                @if($paymentMethod === 'bank_transfer')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Bank Name</label>
                        <input type="text" wire:model="paymentDetails.bank_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reference #</label>
                        <input type="text" wire:model="paymentDetails.reference" class="form-control">
                    </div>
                </div>
                @elseif($paymentMethod === 'cheque')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Bank Name</label>
                        <input type="text" wire:model="paymentDetails.bank_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cheque #</label>
                        <input type="text" wire:model="paymentDetails.cheque_no" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Cheque Date</label>
                    <input type="date" wire:model="paymentDetails.date" class="form-control">
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button wire:click="closeModal" class="btn btn-outline">Cancel</button>
                <button wire:click="savePayment" class="btn btn-gold">Confirm Payment</button>
            </div>
        </div>
    </div>
    @endif
</div>
