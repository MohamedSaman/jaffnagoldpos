<div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Stock Purchases</h2>
            <button wire:click="openCreate" class="btn btn-gold">
                <span>➕</span> New Purchase Order
            </button>
        </div>

        <div style="display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap;">
            <div class="search-wrap" style="flex: 1; min-width: 250px;">
                <span class="search-icon">🔍</span>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search by invoice or supplier...">
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $p)
                        <tr>
                            <td><span class="badge badge-gray">{{ $p->invoice_no }}</span></td>
                            <td><div style="font-weight: 500;">{{ $p->supplier->name }}</div></td>
                            <td>{{ \Carbon\Carbon::parse($p->date)->format('d M Y') }}</td>
                            <td style="font-weight: 600;">Rs.{{ number_format($p->total_amount, 2) }}</td>
                            <td style="color: var(--success);">Rs.{{ number_format($p->paid_amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $p->due_amount > 0 ? 'badge-red' : 'badge-green' }}">
                                    Rs.{{ number_format($p->due_amount, 2) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <button wire:click="viewPurchase({{ $p->id }})" class="btn btn-outline btn-sm btn-icon" title="View Details">
                                    👁️
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon">📂</div>
                                    <p>No purchase records found.</p>
                                </div>
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

    <!-- Create Modal -->
    @if($showModal)
        <div class="modal-backdrop">
            <div class="modal modal-lg">
                <div class="modal-header">
                    <h3 class="modal-title">Record New Purchase</h3>
                    <button wire:click="$set('showModal', false)" class="btn-close">✕</button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Supplier</label>
                                <select wire:model="supplier_id" class="form-control">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id') <span class="error-text" style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Purchase Date</label>
                                <input type="date" wire:model="date" class="form-control">
                                @error('date') <span class="error-text" style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="divider"></div>
                        <h4 style="font-size: 13px; margin-bottom: 12px; color: var(--gold-dark);">Purchase Items</h4>
                        
                        <div class="table-wrap" style="background: var(--bg3); padding: 12px; border-radius: 8px;">
                            <table style="background: transparent;">
                                <thead>
                                    <tr>
                                        <th style="background: transparent;">Product</th>
                                        <th style="background: transparent;">Weight (g)</th>
                                        <th style="background: transparent;">Cost /g</th>
                                        <th style="background: transparent; text-align: right;">Total</th>
                                        <th style="background: transparent;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $index => $item)
                                        <tr>
                                            <td>
                                                <select wire:model="items.{{ $index }}.product_id" class="form-control">
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $prod)
                                                        <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->product_code }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" step="0.001" wire:model.blur="items.{{ $index }}.weight" class="form-control"></td>
                                            <td><input type="number" step="0.01" wire:model.blur="items.{{ $index }}.cost_per_gram" class="form-control"></td>
                                            <td style="text-align: right; font-weight: 600;">Rs.{{ number_format($item['total'], 2) }}</td>
                                            <td style="width: 40px;">
                                                @if(count($items) > 1)
                                                    <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-outline btn-sm btn-icon" style="color: var(--danger);">✕</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" wire:click="addItem" class="btn btn-outline btn-sm" style="margin-top: 10px;">
                                ➕ Add Item
                            </button>
                        </div>

                        <div class="divider"></div>

                        <div style="display: flex; justify-content: flex-end; gap: 40px; padding: 20px 0;">
                            <div class="text-right">
                                <label class="form-label">Total Amount</label>
                                <div style="font-size: 24px; font-weight: 700; color: var(--text);">Rs.{{ number_format($this->totalAmount, 2) }}</div>
                            </div>
                            <div class="form-group" style="width: 180px;">
                                <label class="form-label">Paid Amount (Rs.)</label>
                                <input type="number" step="0.01" wire:model.live="paid_amount" class="form-control" style="font-size: 18px; font-weight: 600;">
                                <div style="margin-top: 8px; font-size: 12px; color: var(--danger);">Due: Rs.{{ number_format($this->dueAmount, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-gold">Complete Purchase</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- View Modal -->
    @if($showView && $viewingPurchase)
        <div class="modal-backdrop">
            <div class="modal modal-lg">
                <div class="modal-header">
                    <h3 class="modal-title">Purchase Details: {{ $viewingPurchase->invoice_no }}</h3>
                    <button wire:click="$set('showView', false)" class="btn-close">✕</button>
                </div>
                <div class="modal-body">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
                        <div>
                            <div class="form-label">Supplier Information</div>
                            <div style="font-weight: 600;">{{ $viewingPurchase->supplier->name }}</div>
                            <div style="font-size: 13px; color: var(--text-muted);">{{ $viewingPurchase->supplier->phone }}</div>
                            <div style="font-size: 13px; color: var(--text-muted);">{{ $viewingPurchase->supplier->address }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div class="form-label">Purchase Date</div>
                            <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($viewingPurchase->date)->format('d M Y') }}</div>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Purity</th>
                                    <th>Weight</th>
                                    <th>Cost/g</th>
                                    <th style="text-align: right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($viewingPurchase->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td><span class="badge badge-gold">{{ $item->product->purity->name }}</span></td>
                                        <td>{{ number_format($item->weight, 3) }}g</td>
                                        <td>Rs.{{ number_format($item->cost_per_gram, 2) }}</td>
                                        <td style="text-align: right; font-weight: 600;">Rs.{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align: right; font-weight: 600; padding: 20px;">Grand Total:</td>
                                    <td style="text-align: right; font-size: 18px; font-weight: 700; color: var(--gold-dark); padding: 20px;">
                                        Rs.{{ number_format($viewingPurchase->total_amount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="$set('showView', false)" class="btn btn-outline">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>