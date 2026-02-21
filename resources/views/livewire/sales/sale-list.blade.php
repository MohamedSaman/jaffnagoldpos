<div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Sales Invoices</h2>
            <a href="{{ route('pos') }}" class="btn btn-gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> New Sale (POS)
            </a>
        </div>

        <!-- Filters & Search -->
        <div style="display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap;">
            <div class="search-wrap" style="flex: 1; min-width: 250px;">
                <span class="search-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search by invoice # or customer name...">
            </div>
        </div>

        <!-- Table -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th style="text-align: right;">Total Amount</th>
                        <th style="text-align: right;">Paid</th>
                        <th style="text-align: right;">Due</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $s)
                        <tr>
                            <td><span class="badge badge-gray">{{ $s->invoice_no }}</span></td>
                            <td>{{ $s->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <div style="font-weight: 600;">{{ $s->customer->name ?? 'Walk-in Customer' }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $s->customer->phone ?? '' }}</div>
                            </td>
                            <td style="text-align: right; font-weight: 700;">Rs.{{ number_format($s->grand_total, 2) }}</td>
                            <td style="text-align: right; color: var(--success);">Rs.{{ number_format($s->paid_amount, 2) }}</td>
                            <td style="text-align: right; color: var(--danger);">Rs.{{ number_format($s->due_amount, 2) }}</td>
                            <td style="text-align: center;">
                                @if($s->due_amount <= 0)
                                    <span class="badge badge-green">Paid</span>
                                @else
                                    <span class="badge badge-red">Due</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button wire:click="viewDetails({{ $s->id }})" class="btn btn-outline btn-sm btn-icon" title="View Details">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <button onclick="confirm('Delete this invoice? This action cannot be undone.') || event.stopImmediatePropagation()" wire:click="deleteSale({{ $s->id }})" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:40px;height:40px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></div>
                                    <p>No sales records found.</p>
                                </div>
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

    <!-- View Details Modal -->
    @if($showView && $viewingSale)
        <div class="modal-backdrop">
            <div class="modal modal-lg">
                <div class="modal-header">
                    <h3 class="modal-title">Invoice Details: {{ $viewingSale->invoice_no }}</h3>
                    <button wire:click="$set('showView', false)" class="btn-close">✕</button>
                </div>
                <div class="modal-body">
                    <div style="display: flex; justify-content: space-between; border-bottom: 2px solid var(--bg3); padding-bottom: 20px; margin-bottom: 20px;">
                        <div>
                            <div class="form-label" style="margin-bottom: 4px;">Customer</div>
                            <div style="font-size: 18px; font-weight: 700;">{{ $viewingSale->customer->name ?? 'Walk-in' }}</div>
                            <div style="color: var(--text-muted);">{{ $viewingSale->customer->phone ?? '' }}</div>
                            <div style="color: var(--text-muted); font-size: 12px; max-width: 250px;">{{ $viewingSale->customer->address ?? '' }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div class="form-label" style="margin-bottom: 4px;">Invoice Date</div>
                            <div style="font-size: 14px; font-weight: 600;">{{ $viewingSale->created_at->format('d M Y, h:i A') }}</div>
                            <div style="margin-top: 10px;">
                                <span class="badge {{ $viewingSale->due_amount <= 0 ? 'badge-green' : 'badge-red' }}" style="font-size: 14px; padding: 6px 16px;">
                                    {{ $viewingSale->due_amount <= 0 ? 'FULLY PAID' : 'PENDING DUE' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table style="background: var(--bg3); border-radius: 8px; border-collapse: separate; border-spacing: 0;">
                            <thead>
                                <tr>
                                    <th style="padding: 15px; background: rgba(0,0,0,0.03);">Product</th>
                                    <th style="padding: 15px; background: rgba(0,0,0,0.03);">Weight</th>
                                    <th style="padding: 15px; background: rgba(0,0,0,0.03);">Rate/g</th>
                                    <th style="padding: 15px; background: rgba(0,0,0,0.03); text-align: right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($viewingSale->items as $item)
                                    <tr>
                                        <td style="padding: 12px 15px; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                            <div style="font-weight: 600;">{{ $item->product->name }}</div>
                                            <div style="font-size: 11px; color: var(--gold-dark);">{{ $item->product->purity->name }}</div>
                                        </td>
                                        <td style="padding: 12px 15px; border-bottom: 1px solid rgba(0,0,0,0.05);">{{ number_format($item->weight, 3) }}g</td>
                                        <td style="padding: 12px 15px; border-bottom: 1px solid rgba(0,0,0,0.05);">Rs.{{ number_format($item->rate_per_gram, 2) }}</td>
                                        <td style="padding: 12px 15px; border-bottom: 1px solid rgba(0,0,0,0.05); text-align: right; font-weight: 600;">
                                            Rs.{{ number_format($item->total, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 30px;">
                        <div style="width: 300px; display: grid; gap: 12px;">
                            <div style="display: flex; justify-content: space-between; font-size: 14px;">
                                <span style="color: var(--text-muted);">Subtotal:</span>
                                <span style="font-weight: 600;">Rs.{{ number_format($viewingSale->total_amount, 2) }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--danger); border-bottom: 1px solid var(--border); padding-bottom: 12px;">
                                <span>Discount:</span>
                                <span>-Rs.{{ number_format($viewingSale->discount, 2) }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 20px; font-weight: 800; color: var(--text);">
                                <span>Grand Total:</span>
                                <span style="color: var(--gold-dark);">Rs.{{ number_format($viewingSale->grand_total, 2) }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--success); margin-top: 8px;">
                                <span>Paid Amount:</span>
                                <span>Rs.{{ number_format($viewingSale->paid_amount, 2) }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 16px; font-weight: 700; color: var(--danger);">
                                <span>Due Remaining:</span>
                                <span>Rs.{{ number_format($viewingSale->due_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="$set('showView', false)" class="btn btn-outline">Close Invoice</button>
                    <button type="button" class="btn btn-gold">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print Receipt
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
