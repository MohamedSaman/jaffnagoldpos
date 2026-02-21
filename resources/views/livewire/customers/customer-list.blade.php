<div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Customer Directory</h2>
            <button wire:click="openCreate" class="btn btn-gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add New Customer
            </button>
        </div>

        <!-- Filters & Search -->
        <div style="display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap;">
            <div class="search-wrap" style="flex: 1; min-width: 250px;">
                <span class="search-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search by name or phone...">
            </div>
        </div>

        <!-- Table -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th style="text-align: center;">Sales</th>
                        <th>Total Spent</th>
                        <th>Due Amount</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr>
                            <td><div style="font-weight: 600;">{{ $c->name }}</div></td>
                            <td>{{ $c->phone ?? 'N/A' }}</td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $c->address ?? 'N/A' }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-blue">{{ $c->sales_count }}</span>
                            </td>
                            <td style="font-weight: 600;">Rs.{{ number_format($c->sales_sum_grand_total + $c->opening_balance, 2) }}</td>
                            <td>
                                <span class="badge {{ $c->sales_sum_due_amount > 0 ? 'badge-red' : 'badge-green' }}">
                                    Rs.{{ number_format($c->sales_sum_due_amount, 2) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button wire:click="openHistory({{ $c->id }})" class="btn btn-outline btn-sm btn-icon" title="View Sales History">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                    </button>
                                    <button wire:click="openEdit({{ $c->id }})" class="btn btn-outline btn-sm btn-icon" title="Edit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $c->id }})" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:40px;height:40px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                                    <p>No customers found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop">
            <div class="modal">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $editingId ? 'Edit Customer' : 'Add New Customer' }}</h3>
                    <button wire:click="$set('showModal', false)" class="btn-close">✕</button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Customer Name</label>
                            <input type="text" wire:model="name" class="form-control" placeholder="Full Name">
                            @error('name') <span class="error-text" style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" wire:model="phone" class="form-control" placeholder="10-digit number">
                            @error('phone') <span class="error-text" style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <textarea wire:model="address" class="form-control" rows="3" placeholder="Residential/Shipping address"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Initial Opening Balance (Rs.)</label>
                            <input type="number" step="0.01" wire:model="opening_balance" class="form-control">
                            @error('opening_balance') <span class="error-text" style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-gold">
                            {{ $editingId ? 'Update Customer' : 'Save Customer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- History Modal -->
    @if($showHistory && $viewingCustomer)
        <div class="modal-backdrop">
            <div class="modal modal-lg">
                <div class="modal-header">
                    <h3 class="modal-title">Sales History: {{ $viewingCustomer->name }}</h3>
                    <button wire:click="$set('showHistory', false)" class="btn-close">✕</button>
                </div>
                <div class="modal-body">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Bill #</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Items</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($viewingCustomer->sales as $sale)
                                    <tr>
                                        <td>{{ $sale->created_at->format('d M Y') }}</td>
                                        <td><span class="badge badge-gray">#{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                                        <td style="font-weight: 600;">Rs.{{ number_format($sale->grand_total, 2) }}</td>
                                        <td style="color: var(--success);">Rs.{{ number_format($sale->paid_amount, 2) }}</td>
                                        <td style="color: var(--danger);">Rs.{{ number_format($sale->due_amount, 2) }}</td>
                                        <td>{{ $sale->items->count() }} items</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                            No billing history found for this customer.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="$set('showHistory', false)" class="btn btn-outline">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>