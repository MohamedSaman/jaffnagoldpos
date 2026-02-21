<div>
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Shop Expenses</h2>
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Track your daily showroom operating costs</div>
            </div>
            <button wire:click="openCreate" class="btn btn-gold">
                <span>➕</span> Record Expense
            </button>
        </div>

        <!-- Filters -->
        <div style="display: flex; gap: 16px; margin-bottom: 24px; align-items: center; flex-wrap: wrap;">
            <div class="search-wrap" style="flex: 1; min-width: 200px;">
                <span class="search-icon">🔍</span>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search expenses...">
            </div>
            <div style="display: flex; align-items: center; gap: 12px; background: var(--bg2); border: 1px solid var(--border); padding: 6px 16px; border-radius: var(--radius-sm);">
                <div class="form-label" style="margin-bottom: 0;">Show for:</div>
                <input type="month" wire:model.live="filterMonth" class="form-control" style="width: 160px; border: none; background: transparent; padding: 4px;">
            </div>
            <div style="background: rgba(239,68,68,0.1); color: #F87171; padding: 10px 18px; border-radius: var(--radius-sm); font-weight: 700;">
                Monthly Total: Rs.{{ number_format($monthTotal, 2) }}
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title / Description</th>
                        <th>Amount</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $e)
                        <tr>
                            <td style="width: 140px;">{{ $e->date->format('d M Y') }}</td>
                            <td>
                                <div style="font-weight: 600;">{{ $e->title }}</div>
                                @if($e->description)
                                    <div style="font-size: 12px; color: var(--text-muted);">{{ $e->description }}</div>
                                @endif
                            </td>
                            <td style="font-size: 15px; font-weight: 700; color: var(--danger);">
                                Rs.{{ number_format($e->amount, 2) }}
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button wire:click="openEdit({{ $e->id }})" class="btn btn-outline btn-sm btn-icon" title="Edit">
                                        ✏️
                                    </button>
                                    <button onclick="confirm('Delete this expense?') || event.stopImmediatePropagation()" wire:click="delete({{ $e->id }})" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="empty-icon">💸</div>
                                    <p>No expenses found for this period.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $expenses->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop">
            <div class="modal">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $editingId ? 'Edit Expense' : 'Record New Expense' }}</h3>
                    <button wire:click="$set('showModal', false)" class="btn-close">✕</button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Expense Title</label>
                            <input type="text" wire:model="title" class="form-control" placeholder="e.g. Electricity Bill, Shop Rent">
                            @error('title') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Amount (Rs.)</label>
                            <input type="number" step="0.01" wire:model="amount" class="form-control" placeholder="0.00">
                            @error('amount') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date</label>
                            <input type="date" wire:model="date" class="form-control">
                            @error('date') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Additional Notes</label>
                            <textarea wire:model="description" class="form-control" rows="3" placeholder="Optional details..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-gold">
                            {{ $editingId ? 'Update Expense' : 'Save Expense' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>