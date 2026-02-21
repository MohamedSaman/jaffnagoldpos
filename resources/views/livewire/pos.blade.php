<div>
    <style>
        .pos-grid { display: grid; grid-template-columns: 1fr 340px; gap: 15px; height: calc(100vh - 140px); }
        .pos-left { display: flex; flex-direction: column; gap: 15px; height: 100%; }
        .pos-right { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); display: flex; flex-direction: column; height: 100%; box-shadow: var(--shadow); }
        .pos-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 12px; }
        .pos-scroll { flex: 1; overflow-y: auto; padding: 12px; }
        .pos-item-row { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 40px; gap: 12px; align-items: center; padding: 10px 12px; border-bottom: 1px solid var(--bg3); }
        .pos-header-row { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 40px; gap: 12px; align-items: center; padding: 10px 12px; border-bottom: 2px solid var(--border); background: var(--bg); position: sticky; top: 0; z-index: 10; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); }
        .summary-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .summary-row.total { border-top: 2px solid var(--border); margin-top: 10px; padding-top: 15px; font-weight: 800; font-size: 18px; color: var(--gold-dark); }
        .search-results { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm); z-index: 50; box-shadow: var(--shadow); margin-top: 5px; }
        .search-item { padding: 12px 16px; border-bottom: 1px solid var(--bg3); cursor: pointer; transition: background .2s; }
        .session-stats { display: flex; gap: 15px; margin-bottom: 12px; background: #fff; padding: 8px 15px; border-radius: 10px; border: 1px solid var(--border); }
        .payment-method-btn { padding: 15px; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; text-align: center; transition: all 0.2s; }
        .payment-method-btn.active { border-color: var(--gold); background: rgba(201,168,76,0.1); }
        .invoice-print { font-family: 'Inter', sans-serif; color: #000; width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; }
        @media print { .no-print { display: none !important; } .modal-backdrop { position: static; background: none; display: block; padding: 0; } .modal { box-shadow: none; border: none; max-width: 100% !important; } }
    </style>

    @if(!$hasActiveSession)
    <!-- SESSION START OVERLAY -->
    <div class="modal-backdrop" style="background: rgba(15,23,42,0.9);">
        <div class="modal" style="max-width: 400px; text-align: center; padding: 40px 20px;">
            <div style="font-size: 40px; margin-bottom: 20px;">🏦</div>
            <h2 style="margin-bottom: 10px;">Day Opening Cash</h2>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Please enter the starting cash balance in the drawer for today.</p>
            <div class="form-group">
                <input type="number" wire:model="openingCashInput" class="form-control" style="text-align: center; font-size: 24px; font-weight: 700;" step="0.01">
            </div>
            <button wire:click="startSession" class="btn btn-gold" style="width: 100%; padding: 15px; font-size: 16px;">START TODAY'S BUSINESS</button>
        </div>
    </div>
    @endif

    <div class="session-stats no-print">
        <div style="flex:1;">
            <span style="font-size:10px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Opening Cash</span>
            <div style="font-weight:700; font-size:15px;">Rs.{{ number_format($currentSession?->opening_balance ?? 0, 2) }}</div>
        </div>
        <div style="flex:1; border-left:1px solid var(--border); padding-left:15px;">
            <span style="font-size:10px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Earned Today</span>
            <div style="font-weight:700; font-size:15px; color:var(--success);">Rs.{{ number_format($todayEarnings, 2) }}</div>
        </div>
        <div style="flex:1; border-left:1px solid var(--border); padding-left:15px;">
            <span style="font-size:10px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Drawer Total</span>
            <div style="font-weight:700; font-size:15px; color:var(--gold-dark);">Rs.{{ number_format(($currentSession?->opening_balance ?? 0) + $todayEarnings, 2) }}</div>
        </div>
        <div style="display:flex; align-items:center;">
            <button wire:click="$set('showCloseSessionModal', true)" class="btn btn-danger btn-sm" style="padding: 6px 12px; font-size: 11px;">CLOSE SESSION</button>
        </div>
    </div>

    <div class="pos-grid no-print">
        <div class="pos-left">
            <div class="pos-card" style="position:relative;">
                <div style="position:relative;">
                    <span style="position:absolute;left:15px;top:50%;transform:translateY(-50%);color:var(--text-muted);">🔍</span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search product..." style="padding-left:45px;height:45px;">
                </div>
                @if($showResults)
                <div class="search-results">
                    @foreach($searchResults as $result)
                    <div wire:click="addToCart({{ $result['id'] }})" class="search-item">
                        <div style="font-weight:700;">{{ $result['name'] }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $result['product_code'] }} | Stock: {{ $result['stock_quantity'] }}</div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="pos-card" style="flex:1; padding:0; display:flex; flex-direction:column; overflow:hidden;">
                <div class="pos-header-row">
                    <div>Product Details</div>
                    <div style="text-align: center;">Weight (g)</div>
                    <div style="text-align: right;">Rate (Per g)</div>
                    <div style="text-align: right;">Total</div>
                    <div style="text-align: center;">-</div>
                </div>
                <div class="pos-scroll">
                    @forelse($cart as $key => $item)
                    <div class="pos-item-row">
                        <div>
                            <div style="font-weight:700;">{{ $item['name'] }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $item['purity'] }}</div>
                        </div>
                        <input type="number" step="0.001" value="{{ $item['weight'] }}" wire:change="updateWeight('{{ $key }}', $event.target.value)" class="form-control">
                        <div style="text-align:right;">Rs.{{ number_format($item['rate'], 2) }}</div>
                        <div style="text-align:right; font-weight:700;">Rs.{{ number_format($item['total'], 2) }}</div>
                        <button wire:click="removeFromCart('{{ $key }}')" class="btn btn-danger btn-sm">✕</button>
                    </div>
                    @empty
                    <div style="text-align:center; padding:50px; opacity:0.3;">🛒 Your cart is empty</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="pos-right">
            <div style="padding:15px; font-weight:700; border-bottom:1px solid var(--border); font-size:14px;">Summary</div>
            <div class="pos-scroll">
                <div class="form-group">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <label class="form-label" style="margin-bottom:0;">Customer</label>
                        <button wire:click="$set('showCustomerModal', true)" class="btn btn-outline" style="padding:2px 8px; font-size:10px; height:auto;">+ New</button>
                    </div>
                    <select wire:model="customerId" class="form-control">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Discount (Rs.)</label>
                    <input type="number" wire:model.live="discount" class="form-control">
                </div>
                <div style="background:var(--bg3); padding:15px; border-radius:8px;">
                    <div class="summary-row"><span>Subtotal</span><span>Rs.{{ number_format($this->subtotal, 2) }}</span></div>
                    <div class="summary-row total"><span>Total</span><span>Rs.{{ number_format($this->grandTotal, 2) }}</span></div>
                </div>
            </div>
            <div style="padding:15px; border-top:1px solid var(--border);">
                <button wire:click="openPayment" class="btn btn-gold" style="width:100%; height:50px; font-size:16px;">PROCEED TO PAYMENT</button>
            </div>
        </div>
    </div>

    @if($showPaymentModal)
    <div class="modal-backdrop">
        <div class="modal modal-lg">
            <div class="modal-header">
                <div class="modal-title">Payment - Rs.{{ number_format($this->grandTotal, 2) }}</div>
                <button wire:click="$set('showPaymentModal', false)" class="btn-close">✕</button>
            </div>
            <div class="modal-body">
                <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:15px; margin-bottom:25px;">
                    <div wire:click="$set('mainPaymentMethod', 'cash')" class="payment-method-btn {{ $mainPaymentMethod === 'cash' ? 'active' : '' }}">💵 Cash</div>
                    <div wire:click="$set('mainPaymentMethod', 'bank_transfer')" class="payment-method-btn {{ $mainPaymentMethod === 'bank_transfer' ? 'active' : '' }}">🏦 Bank</div>
                    <div wire:click="$set('mainPaymentMethod', 'cheque')" class="payment-method-btn {{ $mainPaymentMethod === 'cheque' ? 'active' : '' }}">📝 Cheque</div>
                    <div wire:click="$set('mainPaymentMethod', 'credit')" class="payment-method-btn {{ $mainPaymentMethod === 'credit' ? 'active' : '' }}">📋 Credit</div>
                </div>

                @if($mainPaymentMethod === 'cash')
                <div class="form-group" style="max-width: 250px;">
                    <label class="form-label">Cash Amount Received</label>
                    <input type="number" wire:model="paymentDetails.amount" class="form-control" style="font-size:18px; font-weight:700;">
                </div>
                @elseif($mainPaymentMethod === 'bank_transfer')
                <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px;">
                    <div class="form-group"><label class="form-label">Bank Name</label><input type="text" wire:model="paymentDetails.bank_name" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Amount</label><input type="number" wire:model="paymentDetails.amount" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Referance/UTR</label><input type="text" wire:model="paymentDetails.reference" class="form-control"></div>
                </div>
                @elseif($mainPaymentMethod === 'cheque')
                <div style="max-height: 300px; overflow-y:auto; border:1px solid var(--border); border-radius:8px; padding:15px; margin-bottom:15px;">
                    @foreach($cheques as $index => $chq)
                    <div style="display:grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr 40px; gap:10px; margin-bottom:10px; border-bottom:1px solid #eee; padding-bottom:10px;">
                        <input type="text" wire:model="cheques.{{$index}}.bank_name" placeholder="Bank" class="form-control">
                        <input type="text" wire:model="cheques.{{$index}}.cheque_no" placeholder="Chq #" class="form-control">
                        <input type="number" wire:model="cheques.{{$index}}.amount" placeholder="Amt" class="form-control">
                        <input type="date" wire:model="cheques.{{$index}}.date" class="form-control">
                        <input type="text" wire:model="cheques.{{$index}}.reference" placeholder="Ref" class="form-control">
                        @if(count($cheques) > 1) <button wire:click="removeCheque({{$index}})" class="btn btn-danger btn-sm">✕</button> @endif
                    </div>
                    @endforeach
                    <button wire:click="addCheque" class="btn btn-outline btn-sm">+ Add Multiple Cheques</button>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button wire:click="completeSale" class="btn btn-gold" style="width:200px; height:50px;">FINALIZE SALE</button>
            </div>
        </div>
    </div>
    @endif

    @if($showInvoice && $lastSale)
    <div class="modal-backdrop">
        <div class="modal modal-lg">
            <div class="modal-header no-print">
                <div class="modal-title">Sale Complete</div>
                <button wire:click="newSale" class="btn-close">✕</button>
            </div>
            <div class="modal-body">
                <div class="invoice-print">
                    <h1 style="text-align:center;">Five Finger Jewellery</h1>
                    <div style="display:flex; justify-content:space-between; margin:20px 0; border-bottom:1px solid #000; padding-bottom:10px;">
                        <div><b>Invoice:</b> {{ $lastSale->invoice_no }}</div>
                        <div><b>Date:</b> {{ $lastSale->created_at->format('d M Y') }}</div>
                    </div>
                    <table style="width:100%; border-collapse:collapse;">
                        <thead><tr style="border-bottom:2px solid #000; text-align:left;"><th>Item</th><th>Weight</th><th>Total</th></tr></thead>
                        <tbody>
                            @foreach($lastSale->items as $item)
                            <tr style="border-bottom:1px solid #eee;"><td>{{ $item->product->name }}</td><td>{{ $item->weight }}g</td><td>Rs.{{ number_format($item->total, 2) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-left:auto; width:200px; margin-top:20px;">
                        <div class="summary-row"><b>Grand Total:</b> <b>Rs.{{ number_format($lastSale->grand_total, 2) }}</b></div>
                        <div class="summary-row"><b>Paid:</b> <b>Rs.{{ number_format($lastSale->paid_amount, 2) }}</b></div>
                        @if($lastSale->due_amount > 0) <div class="summary-row" style="color:red;"><b>Due:</b> <b>Rs.{{ number_format($lastSale->due_amount, 2) }}</b></div> @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer no-print">
                <button wire:click="newSale" class="btn btn-outline">Close</button>
                <button onclick="window.print()" class="btn btn-gold">🖨️ Print</button>
            </div>
        </div>
    </div>
    @endif
    @if($showCloseSessionModal)
    <div class="modal-backdrop">
        <div class="modal" style="max-width: 400px;">
            <div class="modal-header">
                <div class="modal-title">Close Daily Session</div>
                <button wire:click="$set('showCloseSessionModal', false)" class="btn-close">✕</button>
            </div>
            <div class="modal-body">
                <div style="background: var(--bg3); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-size: 13px; color: var(--text-muted);">Expected Cash:</span>
                        <span style="font-weight: 700; color: var(--gold-dark);">Rs.{{ number_format(($currentSession?->opening_balance ?? 0) + $todayEarnings, 2) }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Actual Cash in Drawer</label>
                    <input type="number" wire:model="closingBalance" class="form-control" style="font-size: 20px; font-weight: 700;">
                    <p style="font-size: 11px; color: var(--text-muted); margin-top: 8px;">Please count the physical cash in the drawer before closing.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button wire:click="$set('showCloseSessionModal', false)" class="btn btn-outline">Cancel</button>
                <button wire:click="closeSession" class="btn btn-danger">Confirm & Close Register</button>
            </div>
        </div>
    </div>
    @endif

    @if($showCustomerModal)
    <div class="modal-backdrop">
        <div class="modal" style="max-width: 400px;">
            <div class="modal-header">
                <div class="modal-title">Add New Customer</div>
                <button wire:click="$set('showCustomerModal', false)" class="btn-close">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" wire:model="newCustName" class="form-control" placeholder="Customer Name">
                    @error('newCustName') <span style="font-size:11px; color:var(--danger);">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" wire:model="newCustPhone" class="form-control" placeholder="Phone Number">
                    @error('newCustPhone') <span style="font-size:11px; color:var(--danger);">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea wire:model="newCustAddress" class="form-control" rows="2" placeholder="Optional"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button wire:click="$set('showCustomerModal', false)" class="btn btn-outline">Cancel</button>
                <button wire:click="saveCustomer" class="btn btn-gold">Save Customer</button>
            </div>
        </div>
    </div>
    @endif
</div>
