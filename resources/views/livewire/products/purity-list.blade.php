<div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Gold Purities</h2>
            <button wire:click="openCreate" class="btn btn-gold">
                <span>➕</span> Add Purity
             </button>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Purity Name</th>
                        <th>Fine Gold %</th>
                        <th style="text-align: center;">Products</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purities as $p)
                        <tr>
                            <td style="font-weight: 600;">{{ $p->name }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="flex: 1; height: 6px; background: var(--bg3); border-radius: 3px; max-width: 100px;">
                                        <div style="width: {{ $p->percentage }}%; height: 100%; background: var(--gold); border-radius: 3px;"></div>
                                    </div>
                                    <span>{{ number_format($p->percentage, 2) }}%</span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-gray">{{ $p->products_count }}</span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button wire:click="openEdit({{ $p->id }})" class="btn btn-outline btn-sm btn-icon">✏️</button>
                                    <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $p->id }})" class="btn btn-danger btn-sm btn-icon">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <p>No purities defined.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal-backdrop">
            <div class="modal">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $editingId ? 'Edit Purity' : 'Add Purity' }}</h3>
                    <button wire:click="$set('showModal', false)" class="btn-close">✕</button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Purity Name (e.g. 22K, 18K)</label>
                            <input type="text" wire:model="name" class="form-control" placeholder="e.g. 22K (916)">
                            @error('name') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Percentage (Fine Gold %)</label>
                            <input type="number" step="0.01" wire:model="percentage" class="form-control" placeholder="e.g. 91.67">
                            @error('percentage') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-gold">Save Purity</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>