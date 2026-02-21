<div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Product Inventory</h2>
            <button wire:click="openCreate" class="btn btn-gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add New Product
            </button>
        </div>

        <!-- Filters & Search -->
        <div style="display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap;">
            <div class="search-wrap" style="flex: 1; min-width: 250px;">
                <span class="search-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search by name, code or barcode...">
            </div>
            
            <select wire:model.live="filterCategory" class="form-control" style="width: 200px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterPurity" class="form-control" style="width: 200px;">
                <option value="">All Purities</option>
                @foreach($purities as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Table -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Purity</th>
                        <th>Weight (G/S/N)</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        <tr>
                            <td><span class="badge badge-gray">{{ $p->product_code }}</span></td>
                            <td>
                                <div style="font-weight: 600;">{{ $p->name }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $p->barcode }}</div>
                            </td>
                            <td>{{ $p->category->name }}</td>
                            <td><span class="badge badge-gold">{{ $p->purity->name }}</span></td>
                            <td>
                                <div style="font-size: 12px;">
                                    {{ number_format($p->gross_weight, 3) }} / 
                                    {{ number_format($p->stone_weight, 3) }} / 
                                    <strong>{{ number_format($p->net_weight, 3) }}g</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $p->stock_quantity > 0 ? 'badge-green' : 'badge-red' }}">
                                    {{ $p->stock_quantity }}
                                </span>
                            </td>
                            <td>
                                @if($p->status)
                                    <span class="badge badge-green">Active</span>
                                @else
                                    <span class="badge badge-red">Inactive</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button wire:click="openEdit({{ $p->id }})" class="btn btn-outline btn-sm btn-icon" title="Edit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $p->id }})" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:40px;height:40px;"><path d="M12 22.08l-9-5.12a2 2 0 0 1-1-1.73V7.27a2 2 0 0 1 1-1.74l9-5.2a2 2 0 0 1 2 0l9 5.2a2 2 0 0 1 1 1.74v7.96a2 2 0 0 1-1 1.73l-9 5.12a2 2 0 0 1-2 0z"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></div>
                                    <p>No products found matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop">
            <div class="modal modal-lg">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $editingId ? 'Edit Product' : 'Add New Product' }}</h3>
                    <button wire:click="$set('showModal', false)" class="btn-close">✕</button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Product Code</label>
                                <input type="text" wire:model="product_code" class="form-control" placeholder="e.g. RING-001">
                                @error('product_code') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Product Name</label>
                                <input type="text" wire:model="name" class="form-control" placeholder="e.g. Gold Wedding Ring">
                                @error('name') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Category</label>
                                <select wire:model="category_id" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Purity</label>
                                <select wire:model="purity_id" class="form-control">
                                    <option value="">Select Purity</option>
                                    @foreach($purities as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                @error('purity_id') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="divider"></div>

                        <div class="form-grid" style="grid-template-columns: repeat(3, 1fr);">
                            <div class="form-group">
                                <label class="form-label">Gross Weight (g)</label>
                                <input type="number" step="0.001" wire:model="gross_weight" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Stone Weight (g)</label>
                                <input type="number" step="0.001" wire:model="stone_weight" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Net Weight (g)</label>
                                <input type="number" step="0.001" wire:model="net_weight" class="form-control">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Making Charge Type</label>
                                <select wire:model="making_charge_type" class="form-control">
                                    <option value="per_gram">Per Gram</option>
                                    <option value="fixed">Fixed Amount</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Making Charge (Rs.)</label>
                                <input type="number" step="0.01" wire:model="making_charge" class="form-control">
                                @error('making_charge') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Wastage %</label>
                                <input type="number" step="0.01" wire:model="wastage_percentage" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" wire:model="stock_quantity" class="form-control">
                                @error('stock_quantity') <span style="color:var(--danger); font-size:11px;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Barcode</label>
                                <input type="text" wire:model="barcode" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select wire:model="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-gold">
                            {{ $editingId ? 'Update Product' : 'Create Product' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="modal-backdrop">
            <div class="modal">
                <div class="modal-header">
                    <h3 class="modal-title">Confirm Delete</h3>
                    <button wire:click="$set('showDeleteModal', false)" class="btn-close">✕</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="$set('showDeleteModal', false)" class="btn btn-outline">Cancel</button>
                    <button type="button" wire:click="delete" class="btn btn-danger">Delete Product</button>
                </div>
            </div>
        </div>
    @endif
</div>