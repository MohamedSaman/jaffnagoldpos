<div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Purchase Due Payments (Suppliers)</h2>
        </div>

        <div style="margin-bottom: 24px;">
            <div class="search-wrap" style="max-width: 400px;">
                <span class="search-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search by Bill # or Supplier...">
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Bill #</th>
                        <th>Supplier</th>
                        <th style="text-align: right;">Total Cost</th>
                        <th style="text-align: right;">Paid</th>
                        <th style="text-align: right;">Due Amount</th>
                        <th style="text-align: right;">Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $p)
                    <tr>
                        <td>{{ $p->date->format('d M Y') }}</td>
                        <td><span style="font-weight: 700;">{{ $p->invoice_no }}</span></td>
                        <td>
                            <div style="font-weight: 600;">{{ $p->supplier?->name }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ $p->supplier?->phone }}</div>
                        </td>
                        <td style="text-align: right;">Rs.{{ number_format($p->total_amount, 2) }}</td>
                        <td style="text-align: right; color: var(--success);">Rs.{{ number_format($p->paid_amount, 2) }}</td>
                        <td style="text-align: right; color: var(--danger); font-weight: 700;">Rs.{{ number_format($p->due_amount, 2) }}</td>
                        <td style="text-align: right;">
                            <span class="badge badge-red">Pending Payment</span>
                        </td>
                        <td style="text-align: center;">
                            <button wire:click="openPaymentModal({{ $p->id }})" class="btn btn-gold btn-sm">
                                Pay Supplier
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            No pending supplier dues found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">
            {{ $purchases->links() }}
        </div>
    </div>

    @if($showPaymentModal && $selectedPurchase)
    <div class="modal-backdrop">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">Supplier Payment: {{ $selectedPurchase->invoice_no }}</div>
                <button wire:click="closeModal" class="btn-close">✕</button>
            </div>
            <div class="modal-body">
                <div style="background: var(--bg3); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-size: 13px; color: var(--text-muted);">Supplier:</span>
                        <span style="font-weight: 600;">{{ $selectedPurchase->supplier?->name }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 13px; color: var(--text-muted);">Total Outstanding:</span>
                        <span style="font-weight: 700; color: var(--danger);">Rs.{{ number_format($selectedPurchase->due_amount, 2) }}</span>
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
