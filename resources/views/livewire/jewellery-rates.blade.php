<div>
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Gold Rates</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">Manage daily jewellery rates per gram</div>
            </div>
            <button wire:click="openCreate" class="btn btn-gold" id="btn-add-rate"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Rate</button>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Purity</th>
                        <th>Rate / Gram</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rates as $rate)
                    <tr>
                        <td>{{ $rate->date->format('d M Y') }}</td>
                        <td><span class="badge badge-gold">{{ $rate->purity->name }}</span></td>
                        <td style="font-size:16px;font-weight:700;color:var(--gold-dark);">Rs.{{ number_format($rate->rate_per_gram, 2) }}</td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button wire:click="openEdit({{ $rate->id }})" class="btn btn-outline btn-sm btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                                <button wire:click="delete({{ $rate->id }})" class="btn btn-danger btn-sm btn-icon" wire:confirm="Delete this rate?"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4"><div class="empty-state"><div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:40px;height:40px;"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div><p>No rates added yet</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:16px;">{{ $rates->links() }}</div>
    </div>

    @if($showModal)
    <div class="modal-backdrop" wire:click.self="$set('showModal', false)">
        <div class="modal" style="max-width:420px;">
            <div class="modal-header">
                <div class="modal-title">{{ $editingId ? 'Edit Rate' : 'Add Gold Rate' }}</div>
                <button class="btn-close" wire:click="$set('showModal', false)">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Purity *</label>
                    <select wire:model="purity_id" class="form-control" id="rate-purity">
                        <option value="">Select Purity</option>
                        @foreach($purities as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->percentage }}%)</option>
                        @endforeach
                    </select>
                    @error('purity_id') <span style="color:#F87171;font-size:12px;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Rate per Gram (Rs.) *</label>
                    <input type="number" step="0.01" wire:model="rate_per_gram" class="form-control" id="rate-value" placeholder="e.g. 6500.00">
                    @error('rate_per_gram') <span style="color:#F87171;font-size:12px;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Date *</label>
                    <input type="date" wire:model="date" class="form-control" id="rate-date">
                    @error('date') <span style="color:#F87171;font-size:12px;">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button wire:click="$set('showModal', false)" class="btn btn-outline">Cancel</button>
                <button wire:click="save" class="btn btn-gold">Save Rate</button>
            </div>
        </div>
    </div>
    @endif
</div>
