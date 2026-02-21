<div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Product Categories</h2>
            <button wire:click="openCreate" class="btn btn-gold">
                <span>➕</span> Add Category
            </button>
        </div>

        <div class="search-wrap" style="margin-bottom: 20px;">
            <span class="search-icon">🔍</span>
            <input type="text" wire:model.live="search" class="form-control" placeholder="Search categories...">
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th style="text-align: center;">Products Count</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td style="font-weight: 500;">{{ $cat->name }}</td>
                            <td style="text-align: center;">
                                <span class="badge badge-gray">{{ $cat->products_count }}</span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button wire:click="openEdit({{ $cat->id }})" class="btn btn-outline btn-sm btn-icon">✏️</button>
                                    <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $cat->id }})" class="btn btn-danger btn-sm btn-icon">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <p>No categories found.</p>
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
                    <h3 class="modal-title">{{ $editingId ? 'Edit Category' : 'Add Category' }}</h3>
                    <button wire:click="$set('showModal', false)" class="btn-close">✕</button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Category Name</label>
                            <input type="text" wire:model="name" class="form-control" placeholder="e.g. Rings, Necklaces...">
                            @error('name') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-gold">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>