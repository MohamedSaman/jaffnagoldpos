<div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Supplier Management</h2>
            <button wire:click="openCreate" class="btn btn-gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add New Supplier
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
                        <th>Company Name</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th style="text-align: center;">Orders</th>
                        <th>Purchases Value</th>
                        <th>Pending Due</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $s)
                        <tr>
                            <td><div style="font-weight: 600;">{{ $s->name }}</div></td>
                            <td>{{ $s->phone ?? 'N/A' }}</td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $s->address ?? 'N/A' }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-blue">{{ $s->purchases_count }}</span>
                            </td>
                            <td style="font-weight: 600;">Rs.{{ number_format($s->purchases_sum_total_amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $s->purchases_sum_due_amount > 0 ? 'badge-red' : 'badge-green' }}">
                                    Rs.{{ number_format($s->purchases_sum_due_amount, 2) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button wire:click="openHistory({{ $s->id }})" class="btn btn-outline btn-sm btn-icon" title="View Purchase History">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                    </button>
                                    <button wire:click="openEdit({{ $s->id }})" class="btn btn-outline btn-sm btn-icon" title="Edit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $s->id }})" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:40px;height:40px;"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="2"/><line x1="15" y1="22" x2="15" y2="2"/></svg></div>
                                    <p>No suppliers found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $suppliers->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop">
            <div class="modal">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $editingId ? 'Edit Supplier' : 'Add New Supplier' }}</h3>
                    <button wire:click="$set('showModal', false)" class="btn-close">✕</button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Supplier/Company Name</label>
                            <input type="text" wire:model="name" class="form-control" placeholder="ABC Jewellery Wholesalers">
                            @error('name') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" wire:model="phone" class="form-control" placeholder="Contact number">
                            @error('phone') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Full Address</label>
                            <textarea wire:model="address" class="form-control" rows="3" placeholder="Office/Warehouse address"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Current Balance (Rs.)</label>
                            <input type="number" step="0.01" wire:model="balance" class="form-control">
                            @error('balance') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-gold">
                            {{ $editingId ? 'Update Supplier' : 'Save Supplier' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- History Modal -->
    @if($showHistory && $viewingSupplier)
        <div class="modal-backdrop">
            <div class="modal modal-lg">
                <div class="modal-header">
                    <h3 class="modal-title">Purchase History: {{ $viewingSupplier->name }}</h3>
                    <button wire:click="$set('showHistory', false)" class="btn-close">✕</button>
                </div>
                <div class="modal-body">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>PO #</th>
                                    <th>Total Value</th>
                                    <th>Paid</th>
                                    <th>Remaining Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($viewingSupplier->purchases as $p)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($p->date)->format('d M Y') }}</td>
                                        <td><span class="badge badge-gray">#PO-{{ $p->id }}</span></td>
                                        <td style="font-weight: 600;">Rs.{{ number_format($p->total_amount, 2) }}</td>
                                        <td style="color: var(--success);">Rs.{{ number_format($p->paid_amount, 2) }}</td>
                                        <td style="color: var(--danger);">Rs.{{ number_format($p->due_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                            No purchase records found for this supplier.
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