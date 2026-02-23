<div class="container-fluid py-3" x-data="salesPage()" x-init="init()">

    {{-- Flash Messages --}}
    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3 premium-alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3 premium-alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Main Billing Section --}}
    <div class="premium-card">
        <div class="premium-card-header bg-black py-2">
            <div class="d-flex align-items-center justify-content-between px-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="header-icon">
                        <i class="bi bi-gem"></i>
                    </div>
                    <h6 class="mb-0 fw-bold text-gold" style="letter-spacing: 0.05em; text-transform: uppercase;">Billing System</h6>
                </div>
                <div class="d-block d-md-flex align-items-center gap-3">
                    <span class="badge text-gold border border-gold px-3 py-2 rounded-pill">
                        <i class="bi bi-calendar3 me-1"></i> {{ date('D, M d Y') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="premium-card-body p-3">
            {{-- Top Section: Customer & Search --}}
            <div class="row g-3 mb-3">
                {{-- Customer Textarea (Left) --}}
                <div class="col-md-6">
                    <div class="input-header mb-1">
                        <i class="bi bi-person-lines-fill me-2 text-gold small"></i>
                        <span class="fw-bold small">Customer Details</span>
                        <span class="text-danger small">*</span>
                    </div>
                    <div class="customer-textarea-wrapper">
                        <textarea
                            class="form-control premium-textarea"
                            wire:model="customerInfo"
                            rows="3"
                            placeholder="Enter customer name and full delivery address... (e.g., Mohamed Saman, No. 42/A, Main Street, Jaffna)"
                            required></textarea>
                        @error('customerInfo')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Product Search (Right) --}}
                <div class="col-md-6">
                    <div class="input-header mb-1">
                        <i class="bi bi-search me-2 text-gold small"></i>
                        <span class="fw-bold small">Product Search / Scan Barcode</span>
                    </div>
                    <div class="search-input-wrapper">
                        <i class="bi bi-upc-scan search-icon"></i>
                        <input type="text"
                            class="form-control premium-search-input"
                            wire:model.live="search"
                            id="productSearchInput"
                            placeholder="Scan barcode or type product name..."
                            autocomplete="off">
                        @if($search)
                        <button class="search-clear-btn" wire:click="$set('search', '')">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        @endif

                        {{-- Search Results Dropdown --}}
                        @if($search && count($searchResults) > 0)
                        <div class="search-results-dropdown">
                            @foreach($searchResults as $product)
                            <div class="search-result-item"
                                wire:key="product-{{ $product['id'] }}"
                                wire:click="addToCart({{ json_encode($product) }})"
                                style="{{ $product['stock'] <= 0 ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                                <div class="product-info">
                                    <div class="product-name">{{ $product['name'] }}</div>
                                    <div class="product-meta">
                                        <span class="meta-tag"><i class="bi bi-upc me-1"></i>{{ $product['code'] }}</span>
                                        <span class="meta-tag"><i class="bi bi-box me-1"></i>{{ $product['model'] }}</span>
                                    </div>
                                </div>
                                <div class="product-price-stock text-end">
                                    <div class="product-price">Rs.{{ number_format($product['price'], 2) }}</div>
                                    <div class="product-stock {{ $product['stock'] <= 0 ? 'out-of-stock' : '' }}">
                                        <i class="bi bi-boxes me-1"></i>Stock: {{ $product['stock'] }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @elseif($search && count($searchResults) === 0)
                        <div class="search-results-dropdown">
                            <div class="no-results">
                                <i class="bi bi-search display-6 d-block mb-2 text-muted"></i>
                                <span>No products found for "<strong>{{ $search }}</strong>"</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Middle Section: Cart (Full Width) --}}
            <div class="row g-0 mb-3">
                <div class="col-12">
                    <div class="cart-section-wrapper border rounded-3 overflow-hidden">
                        <div class="bg-light p-2 border-bottom d-flex justify-content-between align-items-center">
                            <div class="fw-bold small"><i class="bi bi-cart3 me-2 text-gold"></i>Cart Items</div>
                            @if(count($cart) > 0)
                            <span class="badge bg-black text-gold">{{ count($cart) }} Items</span>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table premium-table mb-0">
                                <thead>
                                    <tr>
                                        <th width="35">#</th>
                                        <th>Product Details</th>
                                        <th width="140">Unit Price</th>
                                        <th width="140">Quantity</th>
                                        <th width="120">Discount</th>
                                        <th width="130" class="text-end">Item Total</th>
                                        <th width="50" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $index => $item)
                                    <tr wire:key="{{ $item['key'] ?? 'cart_' . $index }}" class="cart-item-row">
                                        <td><span class="row-num">{{ $index + 1 }}</span></td>
                                        <td>
                                            <div class="cart-product-info">
                                                <strong class="d-block text-dark">{{ $item['name'] }}</strong>
                                                <small class="text-muted">{{ $item['code'] }} | {{ $item['model'] }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white border-end-0 text-muted">Rs</span>
                                                <input type="number" class="form-control form-control-sm border-start-0 ps-0 fw-bold text-gold"
                                                    wire:change="updatePrice({{ $index }}, $event.target.value)"
                                                    value="{{ $item['price'] }}" min="0" step="0.01">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="qty-controls">
                                                <button class="qty-btn" type="button" wire:click="decrementQuantity({{ $index }})">-</button>
                                                <input type="number" class="qty-input"
                                                    wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                                    value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}">
                                                <button class="qty-btn" type="button" wire:click="incrementQuantity({{ $index }})">+</button>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white border-end-0 text-muted">Rs</span>
                                                <input type="number" class="form-control form-control-sm border-start-0 ps-0 text-danger"
                                                    wire:change="updateDiscount({{ $index }}, $event.target.value)"
                                                    value="{{ $item['discount'] }}" min="0" step="0.01">
                                            </div>
                                        </td>
                                        <td class="text-end align-middle">
                                            <span class="fw-bold text-dark">Rs.{{ number_format($item['total'], 2) }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-sm btn-outline-danger border-0" wire:click="removeFromCart({{ $index }})">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @if(count($cart) === 0)
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bi bi-cart-x display-6 d-block mb-3 opacity-25"></i>
                                            Your cart is empty. Scan products or search to add items.
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        {{-- Subtotal and Additional Discount Bar --}}
                        @if(count($cart) > 0)
                        <div class="bg-light p-2 d-flex flex-wrap justify-content-between align-items-center border-top gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-bold text-muted" style="font-size: 0.75rem; text-transform: uppercase;">Additional Discount:</span>
                                <div class="d-flex align-items-center gap-1 bg-white p-1 rounded border">
                                    <input type="number"
                                        class="form-control form-control-sm border-0 shadow-none p-0 px-2 fw-bold text-dark"
                                        wire:model.live="additionalDiscount"
                                        min="0"
                                        step="{{ $additionalDiscountType === 'percentage' ? '1' : '0.01' }}"
                                        @if($additionalDiscountType === 'percentage') max="100" @endif
                                        placeholder="0"
                                        style="width: 70px; font-size: 0.85rem; background: transparent;">
                                    <span class="badge bg-dark text-gold py-1" style="font-size: 0.7rem; min-width: 30px;">
                                        {{ $additionalDiscountType === 'percentage' ? '%' : 'Rs' }}
                                    </span>
                                    <button type="button" class="btn btn-sm btn-light border p-0 px-2"
                                        wire:click="toggleDiscountType" style="height: 24px;">
                                        <i class="bi bi-arrow-repeat" style="font-size: 0.75rem;"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="text-dark fw-bold" style="font-size: 1rem; letter-spacing: -0.01em;">Subtotal: <span class="text-muted fw-normal" style="font-size: 0.85rem;">Rs.</span>{{ number_format($subtotal, 2) }}</div>
                                @if($additionalDiscountAmount > 0)
                                <div class="text-danger fw-bold" style="font-size: 0.8rem;">Discount: -Rs.{{ number_format($additionalDiscountAmount, 2) }}</div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bottom Section: Delivery, Payment & Create Sale --}}
            <div class="row g-3 pt-3 border-top">
                <div class="col-md-9">
                    <div class="row g-3">
                        {{-- Delivery Method --}}
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-muted text-uppercase mb-2" style="font-size: 0.7rem;">
                                <i class="bi bi-truck me-2 text-gold"></i>Delivery Method
                            </label>
                            <div class="d-flex gap-2">
                                <label class="delivery-radio-card flex-fill">
                                    <input type="radio" wire:model.live="deliveryMethod" value="Post" class="d-none">
                                    <div class="radio-content">
                                        <i class="bi bi-mailbox2"></i>
                                        <span>Post</span>
                                    </div>
                                </label>
                                <label class="delivery-radio-card flex-fill">
                                    <input type="radio" wire:model.live="deliveryMethod" value="Domestic" class="d-none">
                                    <div class="radio-content">
                                        <i class="bi bi-house-heart"></i>
                                        <span>Domestic</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-muted text-uppercase mb-2" style="font-size: 0.7rem;">
                                <i class="bi bi-credit-card me-2 text-gold"></i>Payment Method
                            </label>
                            <div class="d-flex gap-2">
                                <label class="delivery-radio-card flex-fill">
                                    <input type="radio" wire:model.live="paymentMethod" value="Cash on Delivery" class="d-none">
                                    <div class="radio-content">
                                        <i class="bi bi-cash-coin"></i>
                                        <span>Cash on Delivery</span>
                                    </div>
                                </label>
                                <label class="delivery-radio-card flex-fill">
                                    <input type="radio" wire:model.live="paymentMethod" value="Online Payment" class="d-none">
                                    <div class="radio-content">
                                        <i class="bi bi-phone"></i>
                                        <span>Online Payment</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Grand Total & Create Button --}}
                <div class="col-md-3">
                    <div class="bg-black p-3 rounded-4 h-100 d-flex flex-column justify-content-between border border-gold border-opacity-25">
                        <div class="text-center mb-2">
                            <div class="text-gold opacity-50 fw-bold mb-0" style="font-size: 0.65rem; text-transform: uppercase;">Total Payable</div>
                            <div class="h4 fw-bold text-gold mb-0">Rs.{{ number_format($grandTotal, 2) }}</div>
                        </div>
                        <button class="btn btn-gold-premium w-100 py-2 rounded-3 small fw-bold"
                            style="font-size: 0.8rem;"
                            wire:click="createSale"
                            {{ count($cart) == 0 ? 'disabled' : '' }}>
                            <i class="bi bi-bag-check-fill me-1"></i>CREATE SALE
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sale Preview Modal --}}
    @if($showSaleModal && $createdSale)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.8); backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-gold border-opacity-50 overflow-hidden">
                <div class="modal-header bg-black text-gold border-gold border-opacity-25">
                    <h5 class="modal-title fw-bold">SALE COMPLETED SUCCESSFULLY</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="createNewSale"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-4" id="saleReceiptPrintContent" style="background: white;">
                        {{-- Receipt Header --}}
                        <div class="text-center mb-4 border-bottom pb-3">
                            <img src="{{ asset('images/JaffnaGold.webp') }}" alt="Logo" class="mb-2" style="max-height: 80px;">
                            <h3 class="fw-bold mb-0">JaffnaGold (PVT) Ltd</h3>
                            <p class="text-muted small mb-0">Premium Jewellery Billing</p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="text-muted small uppercase fw-bold mb-1">Customer Info</div>
                                <div class="fw-bold text-dark" style="white-space: pre-wrap;">{{ $createdSale->deliverySale->customer_details ?? '' }}</div>
                                <div class="mt-2 small">
                                    <span class="badge bg-dark text-gold me-1">{{ $createdSale->deliverySale->delivery_method }}</span>
                                    <span class="badge bg-gold text-dark">{{ $createdSale->deliverySale->payment_method }}</span>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="text-muted small uppercase fw-bold mb-1">Invoice Info</div>
                                <div><strong>Invoice:</strong> {{ $createdSale->invoice_number }}</div>
                                <div><strong>Date:</strong> {{ $createdSale->created_at->format('M d, Y') }}</div>
                                <div><strong>Status:</strong> Processing</div>
                            </div>
                        </div>

                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($createdSale->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">Rs.{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end">Subtotal</td>
                                    <td class="text-end">Rs.{{ number_format($createdSale->subtotal, 2) }}</td>
                                </tr>
                                @if($createdSale->discount_amount > 0)
                                <tr>
                                    <td colspan="2" class="text-end">Discount</td>
                                    <td class="text-end">-Rs.{{ number_format($createdSale->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="fw-bold h5">
                                    <td colspan="2" class="text-end">Grand Total</td>
                                    <td class="text-end">Rs.{{ number_format($createdSale->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-black border-top-0 gap-2">
                    <!-- <button type="button" class="btn btn-outline-gold" onclick="openPrintWindow({{ $createdSale->id }})">
                        <i class="bi bi-printer me-2"></i>PRINT RECEIPT
                    </button> -->
                    <button type="button" class="btn btn-outline-gold" onclick="openDeliveryPrintWindow({{ $createdSale->id }})">
                        <i class="bi bi-upc-scan me-2"></i>PRINT LABEL
                    </button>
                    <button type="button" class="btn btn-gold-premium" wire:click="createNewSale">
                        NEW SALE
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    :root {
        --primary-gold: #D4AF37;
        --secondary-gold: #B8860B;
        --dark-bg: #000000;
        --light-gold: #fffcf0;
    }

    .text-gold { color: var(--primary-gold) !important; }
    .bg-light-gold { background-color: var(--light-gold) !important; }
    .border-gold { border-color: var(--primary-gold) !important; }

    .premium-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid rgba(212, 175, 55, 0.2);
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .premium-textarea {
        border: 2px solid #eee;
        border-radius: 8px;
        padding: 10px !important;
        font-size: 0.85rem !important;
        transition: all 0.3s;
        background: #fbfbfb;
    }

    .premium-textarea:focus {
        border-color: var(--primary-gold);
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
        background: #fff;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-gold);
        font-size: 1rem;
    }

    .premium-search-input {
        padding: 12px 40px 12px 45px !important;
        border: 2px solid #eee;
        border-radius: 10px;
        font-size: 0.9rem !important;
        transition: all 0.3s;
        background: #fbfbfb;
    }

    .premium-search-input:focus {
        border-color: var(--primary-gold);
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
        background: #fff;
    }

    .search-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        z-index: 1000;
        margin-top: 5px;
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #eee;
    }

    .search-result-item {
        padding: 10px 15px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f5f5f5;
        font-size: 0.85rem;
    }

    .search-result-item:hover {
        background: var(--light-gold);
    }

    .qty-controls {
        display: flex;
        align-items: center;
        background: #f0f0f0;
        border-radius: 8px;
        padding: 2px;
        width: fit-content;
    }

    .qty-btn {
        width: 24px;
        height: 24px;
        border: none;
        background: none;
        font-weight: bold;
        color: #555;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-input {
        width: 35px;
        text-align: center;
        border: none;
        background: none;
        font-weight: 700;
        font-size: 0.8rem;
    }

    .delivery-radio-card {
        cursor: pointer;
    }

    .delivery-radio-card .radio-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 8px;
        background: #fbfbfb;
        border: 2px solid #eee;
        border-radius: 10px;
        transition: all 0.3s;
        color: #888;
    }

    .delivery-radio-card input:checked + .radio-content {
        border-color: var(--primary-gold);
        background: var(--light-gold);
        color: var(--secondary-gold);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.1);
    }

    .delivery-radio-card .radio-content i {
        font-size: 1.1rem;
        margin-bottom: 2px;
    }

    .delivery-radio-card .radio-content span {
        font-weight: 600;
        font-size: 0.75rem;
    }

    .btn-gold-premium {
        background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%);
        color: #000;
        font-weight: 800;
        border: none;
        letter-spacing: 0.1em;
        transition: all 0.3s;
    }

    .btn-gold-premium:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(212, 175, 55, 0.5);
    }

    .btn-outline-gold {
        border: 2px solid var(--primary-gold);
        color: var(--primary-gold);
        background: transparent;
        font-weight: 700;
    }

    .btn-outline-gold:hover {
        background: var(--primary-gold);
        color: #000;
    }

    .modal-content {
        border-radius: 20px;
        border: 2px solid var(--primary-gold);
    }

    .premium-alert {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    /* Table Styles */
    .premium-table thead th {
        background: #fbfbfb;
        color: #888;
        font-weight: 600;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 10px 15px;
        border-bottom: 2px solid #eee;
    }

    .cart-item-row td {
        padding: 8px 15px !important;
        vertical-align: middle;
        font-size: 0.85rem;
    }

    .row-num {
        color: var(--primary-gold);
        font-weight: 800;
        font-size: 0.8rem;
    }

    .header-icon {
        width: 32px;
        height: 32px;
        background: var(--primary-gold);
        color: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 1rem;
    }

    .search-clear-btn {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #ccc;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .premium-search-input {
            padding: 15px 40px 15px 45px !important;
        }
        .search-icon {
            left: 15px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function salesPage() {
        return {
            init() {
                Livewire.on('focus-search', () => {
                    this.$nextTick(() => {
                        const searchInput = document.getElementById('productSearchInput');
                        if (searchInput) searchInput.focus();
                    });
                });

                this.$nextTick(() => {
                    const searchInput = document.getElementById('productSearchInput');
                    if (searchInput) searchInput.focus();
                });
            }
        };
    }

    function openPrintWindow(saleId) {
        const printUrl = '/admin/print/sale/' + saleId;
        window.open(printUrl, '_blank', 'width=800,height=600');
    }

    function openDeliveryPrintWindow(saleId) {
        const printUrl = '/admin/print/delivery-label/' + saleId;
        window.open(printUrl, '_blank', 'width=500,height=700');
    }

    document.addEventListener('keydown', function(e) {
        if (e.target.id === 'productSearchInput' && e.key === 'Enter') {
            e.preventDefault();
        }
    });

    window.addEventListener('refreshPage', () => {
        window.location.reload();
    });
</script>
@endpush