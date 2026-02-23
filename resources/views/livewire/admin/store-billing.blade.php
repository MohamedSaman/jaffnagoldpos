<div class="pos-billing-terminal" wire:poll.10s>
    <!-- Load TailWind & Premium Fonts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <style type="text/tailwindcss">
        @layer base {
            :root {
                /* Accent palette - tweak these for different color themes */
                --accent-50: #f0f2ff;
                --accent-100: #e0e4ff;
                --accent-300: #4361ee;
                --accent-500: #161b97; /* logo blue accent */
                --accent-700: #12167d;
                --bg-pos: #fcfcfc;
                --muted: #64748b;
            }

            html, body { background: var(--bg-pos); font-family: 'Inter', sans-serif; color: #0f172a; }

            /* Scrollbar */
            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 12px; }

            /* Material icons tuning */
            .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24; }

            /* Header */
            .pos-billing-terminal header { background: linear-gradient(90deg, var(--accent-50), #ffffff); border-bottom: 1px solid rgba(0,0,0,0.04); }
            .pos-billing-terminal header .bg-blue-gradient { background: linear-gradient(135deg,var(--accent-500),var(--accent-700)); box-shadow: 0 6px 18px rgba(22,27,151,0.12); }

            /* Accent buttons */
            .btn-accent { background: linear-gradient(90deg,var(--accent-500),var(--accent-700)); color: white; }
            .btn-accent:hover { filter: brightness(.95); }

            /* Product cards */
            .product-card { @apply bg-white rounded-xl border border-slate-100 overflow-hidden shadow-md; }
            .product-card .card-body { @apply p-4; }
            .product-card img { @apply object-contain; }
            .product-card .price { color: var(--accent-700); font-weight: 800; }

            /* 'In stock' badge */
            .badge-instock { background: linear-gradient(90deg,#10b981,#06b6d4); color: white; font-weight: 700; padding: .25rem .5rem; border-radius: .375rem; font-size: .625rem; }

            /* Search input */
            .search-input { @apply w-full rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm; }

            /* Cart area */
            .cart-empty { color: var(--muted); }
            .cart-row { @apply transition-shadow; }
            .cart-row:hover { box-shadow: 0 6px 18px rgba(15,23,42,0.03); }

            /* Make the small remove button always visible (overrides hover-hidden) */
            .group\/thumb > button { opacity: 1 !important; transform: translate(0,0) !important; }

            /* Make small control buttons rounder and clearer */
            .qty-btn { @apply w-6 h-6 rounded-full text-[10px] font-bold bg-slate-100 border border-slate-200 flex items-center justify-center; }

            /* Product grid plus button */
            .add-btn { @apply w-9 h-9 rounded-lg bg-white border border-slate-200 shadow-sm flex items-center justify-center; }
            .add-btn:hover { transform: translateY(-3px); transition: transform .15s ease; }

            /* Receipt print styling tweak for in-browser modal */
            .receipt-container { border-radius: 12px; border: 1px solid rgba(15,23,42,0.04); }

            /* Footer action area */
            .pos-footer .btn { @apply rounded-lg px-6 py-3 font-black; }
            .pos-footer .btn-primary { background: linear-gradient(90deg,var(--accent-500),var(--accent-700)); color: white; }
        }
    </style>

    <div class="bg-slate-50 text-slate-800 h-screen flex flex-col overflow-hidden text-sm">

        <!-- Header Section -->
        <header class="bg-white border-b border-slate-200 px-3 py-0 flex items-center justify-between shadow-sm shrink-0">
            <div class="flex items-center gap-2">
                <div class="flex items-center">
                    <img src="{{ asset('images/jg.png') }}" class="h-20 w-auto" alt="Logo">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="bg-slate-100 px-3 py-1 rounded border border-slate-200">
                    <span class="font-mono text-base font-bold text-[#161b97] tracking-widest" id="posClock">00:00:00</span>
                </div>
                <button class="flex items-center gap-2 px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 transition-colors text-xs font-bold text-slate-600 border border-slate-200"
                    wire:click="viewCloseRegisterReport">
                    <span class="material-symbols-outlined text-base">analytics</span>
                    POS REPORT
                </button>
            </div>
        </header>
        <!-- Main Content Area -->
        <main class="flex flex-1 overflow-hidden p-3 gap-3">

            <!-- LEFT SECTION: Search & Cart (50%) -->
            <aside class="w-1/2 flex flex-col bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                {{-- Search Bar with Alpine Keyboard Navigation --}}
                <div class="p-3 border-b border-slate-100 bg-slate-50/50"
                    x-data="{ highlightIndex: -1 }"
                    x-on:product-added-to-cart.window="
                         highlightIndex = -1;
                         $nextTick(() => {
                             const qtyInput = document.getElementById('cart-qty-0');
                             if (qtyInput) { qtyInput.focus(); qtyInput.select(); }
                         })
                     "
                    x-init="$nextTick(() => { if ($refs.searchInput) $refs.searchInput.focus(); })">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                        <input class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-md focus:ring-2 focus:ring-[#161b97]/20 focus:border-[#161b97] outline-none text-sm transition-all"
                            x-ref="searchInput"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Scan barcode or type product name..." type="text"
                            x-on:keydown.arrow-down.prevent="
                                let items = document.querySelectorAll('[data-search-result]');
                                if (items.length > 0) {
                                    highlightIndex = (highlightIndex + 1) % items.length;
                                    items[highlightIndex]?.scrollIntoView({ block: 'nearest' });
                                }
                            "
                            x-on:keydown.arrow-up.prevent="
                                let items = document.querySelectorAll('[data-search-result]');
                                if (items.length > 0) {
                                    highlightIndex = highlightIndex <= 0 ? items.length - 1 : highlightIndex - 1;
                                    items[highlightIndex]?.scrollIntoView({ block: 'nearest' });
                                }
                            "
                            x-on:keydown.enter.prevent="
                                if (highlightIndex >= 0) {
                                    let items = document.querySelectorAll('[data-search-result]');
                                    if (items[highlightIndex]) items[highlightIndex].click();
                                    highlightIndex = -1;
                                }
                            "
                            x-on:keydown.escape.prevent="
                                highlightIndex = -1;
                                $wire.set('search', '');
                            "
                            x-on:input="highlightIndex = -1">

                        <!-- Search Dropdown -->
                        @if($search && count($searchResults) > 0)
                        <div class="absolute w-full mt-2 bg-white border border-slate-200 rounded-lg shadow-2xl z-50 max-h-96 overflow-y-auto custom-scrollbar">
                            @foreach($searchResults as $sIndex => $res)
                            <div class="flex items-center gap-3 p-3 cursor-pointer border-b border-slate-50 last:border-0 transition-colors"
                                data-search-result
                                data-search-index="{{ $sIndex }}"
                                :class="highlightIndex === {{ $sIndex }} ? 'bg-blue-50 border-l-2 !border-l-[#161b97]' : 'hover:bg-slate-50'"
                                wire:click="addToCart({{ json_encode($res) }})"
                                x-on:mouseenter="highlightIndex = {{ $sIndex }}">
                                <img src="{{ $this->getImageUrl($res['image']) }}"
                                    onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrn_80I-lMAa0pVBNmFmQ7VI6l4rr74JW-eQ&s';"
                                    class="w-10 h-10 rounded object-cover border border-slate-100">
                                <div class="flex-1">
                                    <h5 class="text-xs font-bold text-slate-800">{{ $res['name'] }}</h5>
                                    <p class="text-[10px] text-slate-500 font-mono">
                                        {{ $res['code'] }} |
                                        <span class="font-bold {{ $res['stock'] <= 5 ? 'text-amber-500' : 'text-green-600' }}">Available: {{ $res['stock'] }}</span>
                                        @if(($res['pending'] ?? 0) > 0)
                                        | <span class="font-bold text-orange-500">Pending: {{ $res['pending'] }}</span>
                                        @endif
                                    </p>
                                </div>
                                <span class="text-xs font-black text-[#161b97]">Rs. {{ number_format($res['price'], 2) }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Cart Table --}}
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-white z-10 border-b border-slate-200">
                            <tr class="text-[9px] uppercase font-black text-slate-400 tracking-wider bg-slate-50">
                                <th class="px-3 py-2 text-left">Item Details</th>
                                <th class="px-2 py-2 text-center">Qty</th>
                                <th class="px-2 py-2 text-right">Price</th>
                                <th class="px-2 py-2 text-center">Discount</th>
                                <th class="px-3 py-2 text-right">Subtotal</th>
                                <th class="px-1 py-2 w-8">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($cart as $index => $item)
                            @php $cartKey = $item['key'] ?? $index; @endphp
                            <tr class="group hover:bg-amber-50/30 transition-colors border-b border-slate-100" wire:key="cart-{{ $cartKey }}">
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $this->getImageUrl($item['image']) }}"
                                            onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrn_80I-lMAa0pVBNmFmQ7VI6l4rr74JW-eQ&s';"
                                            class="w-8 h-8 rounded-md border border-slate-200 object-cover shrink-0">
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-bold text-slate-700 truncate max-w-[120px]" title="{{ $item['name'] }}">{{ $item['name'] }}</h4>
                                            <p class="text-[9px] text-slate-400 font-mono">{{ $item['code'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-2">
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="flex items-center">
                                            <button class="w-7 h-7 flex items-center justify-center rounded-l border border-r-0 border-slate-200 bg-slate-100 hover:bg-slate-200 text-sm font-bold text-slate-600 transition-all" wire:click="decrementQuantity({{ $index }})">−</button>
                                            <input type="number" min="1" step="1" max="{{ $item['stock'] ?? 0 }}" value="{{ $item['quantity'] }}"
                                                id="cart-qty-{{ $index }}"
                                                wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                                wire:key="qty-{{ $cartKey }}"
                                                @keydown.enter.prevent="
                                                    $wire.updateQuantity({{ $index }}, $event.target.value);
                                                    $nextTick(() => {
                                                        const searchInput = document.querySelector('[x-ref=searchInput]');
                                                        if (searchInput) { 
                                                            searchInput.focus(); 
                                                            searchInput.select();
                                                        }
                                                    });
                                                "
                                                class="w-16 h-7 text-center text-xs font-black bg-white border-y border-slate-200 focus:outline-none focus:border-[#161b97] transition-colors [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none" />
                                            <button class="w-7 h-7 flex items-center justify-center rounded-r border border-l-0 border-slate-200 bg-slate-100 hover:bg-slate-200 text-sm font-bold text-slate-600 transition-all" wire:click="incrementQuantity({{ $index }})">+</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-2">
                                    <div class="flex items-center justify-end gap-1">
                                        <span class="text-[9px] font-bold text-slate-400">Rs.</span>
                                        <input type="number" step="0.01" min="0" value="{{ $item['price'] }}"
                                            id="cart-price-{{ $index }}"
                                            wire:change="updatePrice({{ $index }}, $event.target.value)"
                                            wire:key="price-{{ $cartKey }}"
                                            x-on:keydown.enter.prevent="$wire.updatePrice({{ $index }}, $event.target.value)"
                                            class="w-24 h-7 text-right text-xs font-bold bg-slate-50 border border-slate-200 rounded px-2 focus:outline-none focus:border-[#161b97] transition-colors" />
                                    </div>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    @php
                                    $discountType = $item['discount_type'] ?? 'fixed';
                                    $discountPercent = $item['discount_percentage'] ?? 0;
                                    $discountPerUnit = $item['discount'] ?? 0;
                                    $discountNumericValue = $discountType === 'percentage' ? $discountPercent : $discountPerUnit;
                                    $discountNumericValue = $discountNumericValue > 0 ? rtrim(rtrim(number_format($discountNumericValue, 2, '.', ''), '0'), '.') : '';
                                    @endphp
                                    <div class="flex flex-col items-center gap-0.5"
                                        x-data="{
                                            mode: '{{ $discountType === 'percentage' ? 'pct' : 'fixed' }}',
                                            val: '{{ $discountNumericValue }}',
                                            apply() {
                                                const raw = this.val === '' ? '0' : this.val;
                                                const formatted = this.mode === 'pct' ? raw + '%' : raw;
                                                $wire.updateDiscount({{ $index }}, formatted);
                                            }
                                        }"
                                        wire:key="disc-wrap-{{ $cartKey }}">
                                        {{-- Inline toggle + input on one row --}}
                                        <div class="flex items-center h-7 rounded overflow-hidden border"
                                            :class="val > 0 ? 'border-emerald-300' : 'border-slate-200'">
                                            {{-- Number Input --}}
                                            <input type="number"
                                                step="any"
                                                min="0"
                                                :max="mode === 'pct' ? 100 : 9999999"
                                                x-model="val"
                                                @change="apply()"
                                                @keydown.enter.prevent="apply()"
                                                placeholder="0"
                                                class="w-14 h-full px-2 text-[10px] font-bold text-center focus:outline-none transition-all border-0 bg-transparent"
                                                :class="val > 0 ? 'text-emerald-600 bg-emerald-50' : 'text-slate-500 bg-slate-50'" />
                                            {{-- Rs / % toggle --}}
                                            <button type="button"
                                                @click="mode = mode === 'fixed' ? 'pct' : 'fixed'; apply()"
                                                class="h-full px-1.5 text-[8px] font-black border-l transition-colors whitespace-nowrap"
                                                :class="mode === 'pct' ? 'bg-emerald-500 text-white border-emerald-400' : 'bg-[#161b97] text-white border-yellow-400'"
                                                x-text="mode === 'pct' ? '%' : 'Rs'">
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    <p class="text-sm font-black text-slate-800">Rs. {{ number_format($item['total'], 0) }}</p>
                                </td>
                                <td class="px-1 py-2 w-8 text-center">
                                    <button class="w-6 h-6 flex items-center justify-center bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors mx-auto" wire:click="removeFromCart({{ $index }})" title="Remove">
                                        <span class="material-symbols-outlined text-[11px] font-black leading-none">close</span>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-24 text-center">
                                    <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                        <span class="material-symbols-outlined text-4xl text-slate-200">shopping_cart_off</span>
                                    </div>
                                    <p class="text-slate-400 font-black uppercase tracking-widest text-[10px]">Your cart is empty</p>
                                    <p class="text-slate-300 text-[9px] mt-1">Scan or search products to begin</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Left Footer: Totals --}}
                <div class="p-4 bg-slate-50 border-t border-slate-200 bg-gradient-to-b from-slate-50/50 to-white">
                    <div class="grid grid-cols-2 gap-4 items-end mb-4">
                        <div class="space-y-1.5">
                            @php
                            $originalSubtotal = collect($cart)->sum(function ($item) {
                            return ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
                            });
                            $unitDiscountRs = collect($cart)->sum(function ($item) {
                            return ($item['discount'] ?? 0) * ($item['quantity'] ?? 0);
                            });
                            $globalDiscountAmount = $additionalDiscountAmount ?? 0;
                            $totalDiscountRs = max(0, $unitDiscountRs + $globalDiscountAmount);
                            $totalDiscountPercent = $originalSubtotal > 0 ? (($totalDiscountRs / $originalSubtotal) * 100) : 0;
                            @endphp
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400 font-semibold">Subtotal (Before Discount)</span>
                                <span class="font-bold text-slate-700">Rs. {{ number_format($originalSubtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400 font-semibold">Unit Discount</span>
                                <span class="font-bold text-red-500">- Rs. {{ number_format($unitDiscountRs, 2) }}</span>
                            </div>

                            @if($globalDiscountAmount > 0)
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400 font-semibold">Global Discount</span>
                                <span class="font-bold text-amber-600">
                                    - Rs. {{ number_format($globalDiscountAmount, 2) }}
                                    @if(($additionalDiscountType ?? 'fixed') === 'percentage' && ($additionalDiscount ?? 0) > 0)
                                    <span class="text-slate-400 font-semibold">({{ number_format($additionalDiscount, 2) }}%)</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400 font-semibold">Total Discount</span>
                                <span class="font-bold text-red-500">- Rs. {{ number_format($totalDiscountRs, 2) }} @if($totalDiscountPercent > 0)<span class="text-slate-400 font-semibold">({{ number_format($totalDiscountPercent, 2) }}%)</span>@endif</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400 font-semibold">Tax (0%)</span>
                                <span class="font-bold text-slate-700">Rs. 0.00</span>
                            </div>
                        </div>
                        <div>
                            <button class="w-full py-2 bg-white border border-slate-200 rounded text-[10px] font-black flex items-center justify-center gap-2 hover:bg-slate-50 hover:border-[#161b97]/50 transition-all text-slate-600 shadow-sm uppercase tracking-tighter"
                                wire:click="openSaleDiscountModal">
                                <span class="material-symbols-outlined text-base">sell</span>
                                APPLY GLOBAL DISCOUNT
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 items-center border-t border-slate-200 pt-4">
                        <div class="flex justify-between items-baseline">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</span>
                            <span class="text-3xl font-black text-[#161b97] tracking-tighter">Rs. {{ number_format($grandTotal, 2) }}</span>
                        </div>
                        <button class="w-full bg-[#161b97] hover:bg-blue-800 text-white font-black py-3 rounded-lg flex items-center justify-center gap-2 shadow-xl shadow-blue-500/20 transition-all text-xs uppercase tracking-widest disabled:opacity-40 disabled:grayscale disabled:cursor-not-allowed group"
                            wire:click="validateAndCreateSale" {{ count($cart) == 0 ? 'disabled' : '' }}>
                            <span class="material-symbols-outlined text-xl group-hover:scale-110 transition-transform">payments</span>
                            Complete Sale
                        </button>
                    </div>
                </div>
            </aside>

            <!-- RIGHT SECTION: Selections & Product Grid (50%) -->
            <section class="w-1/2 flex flex-col gap-3 overflow-hidden">
                {{-- Selection Box (Customer/Price) --}}
                <div class="bg-white p-2 rounded-lg shadow-sm border border-slate-200 space-y-1.5">
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="text-[9px] font-black text-slate-400 uppercase mb-1 block tracking-widest">Customer Selection</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg group-focus-within:text-[#161b97] transition-colors">person</span>
                                <select class="w-full pl-9 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-md outline-none text-xs font-bold appearance-none focus:ring-2 focus:ring-[#161b97]/10 focus:border-[#161b97] transition-all" wire:model.live="customerId">
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->business_name ?? $customer->name }} ({{ $customer->phone }})</option>
                                    @endforeach
                                </select>
                                <button class="absolute right-8 top-1/2 -translate-y-1/2 text-[#161b97] p-1.5 hover:bg-yellow-50 rounded-full transition-all" wire:click="openCustomerModal" title="Add Customer">
                                    <span class="material-symbols-outlined text-lg">person_add</span>
                                </button>
                                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-300 text-lg pointer-events-none">expand_more</span>
                            </div>
                        </div>
                        <div class="w-1/3">
                            <label class="text-[9px] font-black text-slate-400 uppercase mb-1 block tracking-widest">Price Type</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">sell</span>
                                <select class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-md outline-none text-xs font-bold appearance-none focus:border-[#161b97] transition-all" wire:model.live="priceType">
                                    <option value="retail">Retail Price</option>
                                    <option value="wholesale">Wholesale</option>
                                    <option value="distribute">Distribute Price</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-300 text-lg pointer-events-none">expand_more</span>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Balance Information (Conditional) --}}
                    @if($customerId && $customerId != '' && $selectedCustomer && $selectedCustomer->type != 'Walking Customer')
                    <div class="flex items-center justify-between px-2 py-1 bg-slate-50 border border-slate-100 rounded">
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Opening Balance</span>
                        <span class="text-xs font-black text-slate-800">{{ number_format($customerOpeningBalanceDisplay, 2) }}</span>
                    </div>
                    @endif

                    {{-- Filter Buttons --}}
                    <div class="grid grid-cols-2 gap-2">
                        <button class="flex items-center justify-center gap-1.5 py-1.5 bg-slate-100 border border-slate-200 rounded-md hover:bg-slate-200 hover:border-slate-300 transition-all font-black text-[9px] text-slate-600 uppercase tracking-tighter shadow-sm"
                            wire:click="toggleCategoryPanel">
                            <span class="material-symbols-outlined text-sm text-[#161b97]">category</span>
                            FILTER BY CATEGORY
                        </button>
                        <button class="flex items-center justify-center gap-1.5 py-1.5 bg-slate-100 border border-slate-200 rounded-md hover:bg-slate-200 hover:border-slate-300 transition-all font-black text-[9px] text-slate-600 uppercase tracking-tighter shadow-sm"
                            wire:click="toggleBrandPanel">
                            <span class="material-symbols-outlined text-sm text-[#161b97]">branding_watermark</span>
                            FILTER BY BRAND
                        </button>
                    </div>
                </div>

                {{-- Product Grid Area --}}
                <div class="flex-1 overflow-y-auto custom-scrollbar pr-1">
                    <div class="grid grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-1.5 pb-4">
                        @forelse($products as $product)
                        @php
                        $isLow = ($product['stock'] ?? 0) <= 5 && ($product['stock'] ?? 0)> 0;
                            $isOut = ($product['stock'] ?? 0) <= 0;
                                @endphp
                                <div class="group bg-white border border-slate-200 rounded-lg shadow-sm hover:border-[#e67e22]/60 hover:shadow-md transition-all cursor-pointer relative flex flex-col h-full overflow-hidden"
                                wire:click="addToCart({{ json_encode($product) }})">

                                {{-- Batch Status --}}
                                <div class="absolute top-1 right-1 z-10">
                                    @if($isOut)
                                    <span class="bg-red-500 text-[6px] text-white font-black px-1 py-0.5 rounded-sm uppercase tracking-tighter">Out</span>
                                    @elseif($isLow)
                                    <span class="bg-amber-500 text-[6px] text-white font-black px-1 py-0.5 rounded-sm uppercase tracking-tighter">Low</span>
                                    @else
                                    <span class="bg-green-500 text-[6px] text-white font-black px-1 py-0.5 rounded-sm uppercase tracking-tighter">In Stock</span>
                                    @endif
                                </div>

                                {{-- Product Image --}}
                                <div class="aspect-[4/3] bg-slate-50 flex items-center justify-center p-2">
                                    <img src="{{ $this->getImageUrl($product['image']) }}"
                                        onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrn_80I-lMAa0pVBNmFmQ7VI6l4rr74JW-eQ&s';"
                                        class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500"
                                        alt="{{ $product['name'] }}">
                                </div>

                                {{-- Product Details --}}
                                <div class="p-1.5 flex flex-col flex-1 bg-white">
                                    <p class="text-[7px] text-slate-400 font-mono uppercase">{{ $product['code'] }}</p>
                                    <h3 class="text-[9px] font-bold text-slate-800 leading-tight mb-1 break-words line-clamp-2" title="{{ $product['name'] }}">{{ $product['name'] }}</h3>

                                    <div class="mt-auto flex items-end justify-between">
                                        <div class="flex flex-col">
                                            <span class="text-[#161b97] font-black text-[11px] leading-none tracking-tighter">Rs. {{ number_format($product['price'], 0) }}</span>
                                            <span class="text-[7px] text-slate-400 font-bold mt-0.5">
                                                <span class="{{ ($product['stock'] ?? 0) <= 5 ? 'text-amber-500' : 'text-green-600' }}">Avail: {{ $product['stock'] }}</span>
                                                @if(($product['pending'] ?? 0) > 0)
                                                | <span class="text-orange-500">Pend: {{ $product['pending'] }}</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="bg-slate-100 p-1 rounded group-hover:bg-[#161b97] group-hover:text-white transition-all">
                                            <span class="material-symbols-outlined text-sm font-black">add</span>
                                        </div>
                                    </div>
                                </div>
                    </div>
                    @empty
                    <div class="col-span-full py-32 text-center">
                        <div class="bg-slate-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                            <span class="material-symbols-outlined text-5xl text-slate-200">inventory_2</span>
                        </div>
                        <p class="text-slate-300 font-black uppercase tracking-widest text-xs">No products in this category</p>
                    </div>
                    @endforelse
                </div>
    </div>
    </section>
    </main>
</div>


{{-- Sliding Category Sidebar (Right to Left, 50% width) --}}
<div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[2000] transition-opacity duration-300 {{ $showCategoryPanel ? 'opacity-100' : 'opacity-0 pointer-events-none' }}" wire:click.self="$set('showCategoryPanel', false)"></div>
<aside class="fixed right-0 top-0 bottom-0 w-1/2 bg-white z-[2001] shadow-2xl transition-transform duration-300 transform {{ $showCategoryPanel ? 'translate-x-0' : 'translate-x-full' }} flex flex-col">
    <div class="p-4 flex justify-between items-center border-b border-slate-100 bg-slate-50">
        <h6 class="mb-0 font-black text-xs text-slate-800 tracking-widest"><i class="material-symbols-outlined align-middle mr-2 text-[#e67e22]">grid_view</i>ALL CATEGORIES</h6>
        <button class="text-slate-400 hover:text-slate-600 transition-colors" wire:click="$set('showCategoryPanel', false)">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <div class="p-2 overflow-y-auto flex-1 custom-scrollbar">
        <button class=" mb-1 text-center  p-3 rounded-lg transition-all border border-slate-100 {{ !$selectedCategory ? 'bg-[#161b97] text-white shadow-lg shadow-blue-500/30' : 'hover:bg-slate-100 text-slate-600' }}"
            wire:click="showAllProducts">
            <span class="font-black text-xs tracking-tight">Show All Items</span>
            <span class="text-[10px] font-bold opacity-70">{{ count($products) }}</span>
        </button>
        @foreach($categories as $category)
        <button class=" mb-1 text-center p-3 rounded-lg transition-all border border-slate-100 {{ $selectedCategory == $category->id ? 'bg-[#161b97] text-white shadow-lg shadow-yellow-500/30' : 'hover:bg-slate-100 text-slate-600' }}"
            wire:click="selectCategory({{ $category->id }})">
            <span class="font-black text-xs tracking-tight">{{ $category->category_name }}</span>
            <span class="text-[10px] font-bold opacity-70">{{ $category->products_count }}</span>
        </button>
        @endforeach
    </div>
</aside>

{{-- Sliding Brand Sidebar (Right to Left, 50% width) --}}
<div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[2000] transition-opacity duration-300 {{ $showBrandPanel ? 'opacity-100' : 'opacity-0 pointer-events-none' }}" wire:click.self="$set('showBrandPanel', false)"></div>
<aside class="fixed right-0 top-0 bottom-0 w-1/2 bg-white z-[2001] shadow-2xl transition-transform duration-300 transform {{ $showBrandPanel ? 'translate-x-0' : 'translate-x-full' }} flex flex-col">
    <div class="p-4 flex justify-between items-center border-b border-slate-100 bg-slate-50">
        <h6 class="mb-0 font-black text-xs text-slate-800 tracking-widest"><i class="material-symbols-outlined align-middle mr-2 text-[#e67e22]">local_offer</i>ALL BRANDS</h6>
        <button class="text-slate-400 hover:text-slate-600 transition-colors" wire:click="$set('showBrandPanel', false)">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <div class="p-2 overflow-y-auto flex-1 custom-scrollbar">
        <button class=" mb-1 text-center  p-3 rounded-lg transition-all border border-slate-100 {{ !$selectedBrand ? 'bg-[#e67e22] text-white shadow-lg shadow-orange-500/30' : 'hover:bg-slate-100 text-slate-600' }}"
            wire:click="showAllBrands">
            <span class="font-black text-xs tracking-tight">Show All Brands</span>
            <span class="text-[10px] font-bold opacity-70">{{ count($products) }}</span>
        </button>
        @foreach($brands as $brand)
        <button class=" mb-1 text-center p-3 rounded-lg transition-all border border-slate-100 {{ $selectedBrand == $brand['id'] ? 'bg-[#e67e22] text-white shadow-lg shadow-orange-500/30' : 'hover:bg-slate-100 text-slate-600' }}"
            wire:click="selectBrand({{ $brand['id'] }})">
            <span class="font-black text-xs tracking-tight">{{ $brand['brand_name'] }}</span>
            <span class="text-[10px] font-bold opacity-70">{{ $brand['products_count'] }}</span>
        </button>
        @endforeach
    </div>
</aside>

{{-- MODALS WRAPPER --}}
@if($showPaymentModal || $showSaleDiscountModal || $showCustomerModal || $showSaleModal || $showCloseRegisterModal || $showWalkingCustomerModal)
<div class="fixed inset-0 z-[3000] flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

    {{-- WALKING CUSTOMER DETAILS MODAL --}}
    @if($showWalkingCustomerModal)
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative transform transition-all">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-5 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#161b97]/20 border border-[#161b97]/30 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#161b97] text-base">person</span>
                </div>
                <div>
                    <h3 class="font-black text-sm text-white uppercase tracking-widest leading-none">Customer Details</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Optional — for this sale only</p>
                </div>
            </div>
            <button class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 transition-colors text-slate-400 hover:text-white"
                wire:click="skipWalkingCustomerDetails">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        {{-- Body --}}
        <div class="p-5 space-y-4">
            <div>
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Customer Name</label>
                <input type="text"
                    class="w-full px-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-xs font-bold outline-none focus:border-[#161b97] transition-colors"
                    wire:model="walkingCustomerName" placeholder="Enter customer name..."
                    x-init="$nextTick(() => $el.focus())">
            </div>
            <div>
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Contact Number</label>
                <input type="text"
                    class="w-full px-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-xs font-bold outline-none focus:border-[#161b97] transition-colors"
                    wire:model="walkingCustomerPhone" placeholder="07xxxxxxxx">
            </div>
        </div>
        {{-- Footer --}}
        <div class="px-5 pb-5 flex items-center justify-between gap-3">
            <button class="px-5 py-2.5 text-[10px] font-black uppercase text-slate-400 hover:text-slate-600 transition-colors"
                wire:click="skipWalkingCustomerDetails">
                Skip
            </button>
            <button class="px-8 py-2.5 bg-[#161b97] hover:bg-yellow-600 text-white rounded-xl text-[10px] font-black uppercase shadow-lg shadow-yellow-500/20 transition-colors"
                wire:click="saveWalkingCustomerDetails">
                Continue to Payment
            </button>
        </div>
    </div>
    @endif

    {{-- CUSTOMER MODAL --}}
    @if($showCustomerModal)
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden relative transform transition-all">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-black text-xs uppercase tracking-widest text-[#e67e22]"><i class="material-symbols-outlined align-middle mr-2">person_add</i>ADD NEW CUSTOMER</h3>
            <button class="text-slate-400 hover:text-slate-600" wire:click="closeCustomerModal"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4">
            <div class="col-span-2 md:col-span-1">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Full Name *</label>
                <input type="text" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" wire:model="customerName" placeholder="Enter name...">
                @error('customerName') <span class="text-red-500 text-[9px] font-bold mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="col-span-2 md:col-span-1">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Phone Number * </label>
                <input type="text" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" wire:model="customerPhone" placeholder="07xxxxxxxx or 07xxxx, 07yyyy / 09zzzz">
                @error('customerPhone') <span class="text-red-500 text-[9px] font-bold mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="col-span-2 md:col-span-1">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Customer Type *</label>
                <select class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" wire:model="customerType">
                    <option value="">-- Select Type --</option>
                    <option value="retail">Retail</option>
                    <option value="wholesale">Wholesale</option>
                    <option value="distributor">Distributor</option>
                </select>
                @error('customerType') <span class="text-red-500 text-[9px] font-bold mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="col-span-2">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Email Address</label>
                <input type="email" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" wire:model="customerEmail" placeholder="email@example.com">
                @error('customerEmail') <span class="text-red-500 text-[9px] font-bold mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="col-span-2">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Billing Address</label>
                <textarea class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" wire:model="customerAddress" rows="2" placeholder="Address..."></textarea>
            </div>
            <div class="col-span-2">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Business Name</label>
                <input type="text" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" wire:model="businessName" placeholder="Business name...">
            </div>

            {{-- More Information Toggle Button --}}
            <div class="col-span-2">
                <button type="button" class="flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 transition-colors text-xs font-bold text-slate-600"
                    wire:click="$toggle('showCustomerMoreInfo')">
                    <span class="material-symbols-outlined text-base transition-transform" style="transform: rotateZ({{ $showCustomerMoreInfo ? '180' : '0' }})deg)">
                        expand_more
                    </span>
                    More Information
                </button>
            </div>

            {{-- More Information Section (Conditional) --}}
            @if($showCustomerMoreInfo)
            <div class="col-span-2 space-y-3 pt-2 border-t border-slate-200">
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Opening Balance</label>
                    <input type="number" step="0.01" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold"
                        wire:model="customerOpeningBalance" placeholder="0.00">
                    @error('customerOpeningBalance') <span class="text-red-500 text-[9px] font-bold mt-1">{{ $message }}</span> @enderror
                    <small class="text-slate-500 text-[8px] mt-1">Amount customer owes at the start</small>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Overpaid Amount</label>
                    <input type="number" step="0.01" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold"
                        wire:model="customerOverpaidAmount" placeholder="0.00">
                    @error('customerOverpaidAmount') <span class="text-red-500 text-[9px] font-bold mt-1">{{ $message }}</span> @enderror
                    <small class="text-slate-500 text-[8px] mt-1">Advance payment from customer</small>
                </div>
            </div>
            @endif
        </div>
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button class="px-6 py-2.5 text-[10px] font-black uppercase text-slate-400" wire:click="closeCustomerModal">Discard</button>
            <button class="px-8 py-2.5 bg-[#e67e22] text-white rounded-lg text-[10px] font-black uppercase shadow-lg shadow-orange-500/20" wire:click="createCustomer">Save Customer</button>
        </div>
    </div>
    @endif

    {{-- SALE DISCOUNT MODAL --}}
    @if($showSaleDiscountModal)
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative transform transition-all">
        <div class="p-6 text-center border-b border-slate-100">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Apply Sale Discount</h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex gap-2 p-1 bg-slate-100 rounded-xl">
                <button class="flex-1 py-2 text-[10px] font-black uppercase rounded-lg transition-all {{ $saleDiscountType == 'fixed' ? 'bg-white text-[#e67e22] shadow-sm' : 'text-slate-400' }}"
                    wire:click="$set('saleDiscountType', 'fixed')">Fixed Amount</button>
                <button class="flex-1 py-2 text-[10px] font-black uppercase rounded-lg transition-all {{ $saleDiscountType == 'percentage' ? 'bg-white text-[#e67e22] shadow-sm' : 'text-slate-400' }}"
                    wire:click="$set('saleDiscountType', 'percentage')">Percentage (%)</button>
            </div>
            <div class="space-y-2">
                <div class="relative">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">{{ $saleDiscountType == 'percentage' ? '%' : 'Rs.' }}</span>
                    <input type="number"
                        step="0.01"
                        min="0"
                        max="{{ $saleDiscountType == 'percentage' ? '100' : '' }}"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xl font-black text-slate-700 outline-none focus:border-[#e67e22]"
                        wire:model.live="saleDiscountValue"
                        placeholder="0">
                </div>
                {{-- Validation Helper Text --}}
                <div class="text-[9px] font-bold text-slate-500 px-1">
                    @if($saleDiscountType == 'percentage')
                    Max: <span class="text-[#e67e22]">100%</span>
                    @else
                    Max: <span class="text-[#e67e22]">Rs. {{ number_format($subtotalAfterItemDiscounts, 2) }}</span> (Sale Total)
                    @endif
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 flex gap-2">
            <button class="flex-1 py-3 text-[10px] font-black uppercase text-slate-400" wire:click="$set('showSaleDiscountModal', false)">Cancel</button>
            <button class="flex-1 py-3 bg-[#e67e22] text-white rounded-xl text-[10px] font-black uppercase shadow-lg shadow-orange-500/10"
                wire:click="applySaleDiscount">Apply Discount</button>
        </div>
    </div>
    @endif

    {{-- PAYMENT MODAL --}}
    @if($showPaymentModal)
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden relative transform transition-all flex flex-col">

        {{-- Header --}}
        <div class="shrink-0 bg-gradient-to-r from-slate-900 to-slate-800 px-5 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#161b97]/20 border border-[#161b97]/30 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#161b97] text-base">payments</span>
                </div>
                <div>
                    <h3 class="font-black text-sm text-white uppercase tracking-widest leading-none">Secure Transaction</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Select payment method to complete sale</p>
                </div>
            </div>
            <button class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 transition-colors text-slate-400 hover:text-white"
                wire:click="closePaymentModal">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex min-h-0">

            {{-- LEFT: Method + Form --}}
            <div class="w-[45%] border-r border-slate-100 p-5 flex flex-col gap-4">

                {{-- Method Toggle --}}
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Payment Method</p>
                    <div class="flex rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                        <button type="button"
                            wire:click="$set('paymentMethod','cash')"
                            class="flex-1 flex items-center justify-center gap-2 py-2.5 text-xs font-black uppercase transition-all {{ $paymentMethod === 'cash' ? 'bg-[#161b97] text-white shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                            <span class="material-symbols-outlined text-base">payments</span>
                            Cash
                        </button>
                        <button type="button"
                            wire:click="$set('paymentMethod','bank_transfer')"
                            class="flex-1 flex items-center justify-center gap-2 py-2.5 text-xs font-black uppercase transition-all border-l border-slate-200 {{ $paymentMethod === 'bank_transfer' ? 'bg-[#161b97] text-white shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                            <span class="material-symbols-outlined text-base">account_balance</span>
                            Bank
                        </button>
                    </div>
                </div>

                {{-- Cash Form --}}
                @if($paymentMethod === 'cash')
                <div class="space-y-3">
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Amount Received</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-[10px] font-black text-slate-400 pointer-events-none">Rs.</span>
                            <input type="number"
                                class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-xl font-black text-slate-800 outline-none focus:border-[#161b97] transition-colors"
                                wire:model.live="amountReceived">
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-emerald-600 text-base">currency_exchange</span>
                            <span class="text-[9px] font-black text-emerald-700 uppercase tracking-widest">Balance to Return</span>
                        </div>
                        <span class="text-lg font-black text-emerald-700">Rs. {{ number_format(max(0, ($amountReceived ?? 0) - $grandTotal), 2) }}</span>
                    </div>
                </div>
                @endif

                {{-- Bank Transfer Form --}}
                @if($paymentMethod === 'bank_transfer')
                <div class="space-y-2.5">
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Amount</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-[10px] font-black text-slate-400 pointer-events-none">Rs.</span>
                            <input type="number"
                                class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-xl font-black text-slate-800 outline-none focus:border-[#161b97] transition-colors"
                                wire:model.live="bankTransferAmount">
                        </div>
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Bank Name</label>
                        <input type="text"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none focus:border-[#161b97] transition-colors"
                            wire:model="bankTransferBankName" placeholder="e.g. Commercial Bank">
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Reference / Slip No.</label>
                        <input type="text"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none focus:border-[#161b97] transition-colors"
                            wire:model="bankTransferReferenceNumber" placeholder="Transaction reference">
                    </div>
                </div>
                @endif

                {{-- Notes --}}
                <div class="mt-auto">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Notes</label>
                    <textarea class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none focus:border-[#161b97] transition-colors resize-none"
                        wire:model="paymentNotes" placeholder="Optional transaction notes..." rows="2"></textarea>
                </div>
            </div>

            {{-- RIGHT: Order Summary --}}
            <div class="w-[55%] bg-slate-50/60 p-5 flex flex-col gap-4">

                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Order Breakdown</p>

                {{-- Summary rows --}}
                <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100 overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-4 py-2.5">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Customer</span>
                        <span class="text-xs font-black text-slate-800">{{ $selectedCustomer->name ?? 'Walking Customer' }}</span>
                    </div>
                    <div class="flex items-center justify-between px-4 py-2.5">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Items</span>
                        <span class="text-xs font-bold text-slate-700">{{ count($cart) }} item{{ count($cart) != 1 ? 's' : '' }}</span>
                    </div>
                    <div class="flex items-center justify-between px-4 py-2.5">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Subtotal</span>
                        <span class="text-xs font-bold text-slate-700">Rs. {{ number_format($subtotal, 2) }}</span>
                    </div>
                    @if(($additionalDiscountAmount + $totalDiscount) > 0)
                    <div class="flex items-center justify-between px-4 py-2.5">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Discounts</span>
                        <span class="text-xs font-bold text-emerald-600">-Rs. {{ number_format($additionalDiscountAmount + $totalDiscount, 2) }}</span>
                    </div>
                    @endif
                </div>

                {{-- Grand Total --}}
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl px-5 py-4 flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Grand Total</p>
                        <p class="text-3xl font-black text-[#161b97] tracking-tight mt-0.5">Rs. {{ number_format($grandTotal, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-[#161b97]/15 border border-[#161b97]/30 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#161b97] text-xl">paid</span>
                    </div>
                </div>

                {{-- Method Badge --}}
                <div class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 rounded-xl">
                    <span class="material-symbols-outlined text-[#161b97] text-base">{{ $paymentMethod === 'cash' ? 'payments' : 'account_balance' }}</span>
                    <div class="flex-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Paying via</p>
                        <p class="text-xs font-black text-slate-700">{{ $paymentMethod === 'cash' ? 'Cash Payment' : 'Bank Transfer' }}</p>
                    </div>
                    @if($paymentMethod === 'cash' && ($amountReceived ?? 0) >= $grandTotal)
                    <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">Ready</span>
                    @elseif($paymentMethod === 'bank_transfer' && $bankTransferAmount >= $grandTotal)
                    <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">Ready</span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="mt-auto flex gap-3">
                    <button class="flex-1 py-3 bg-white border-2 border-slate-200 text-slate-500 font-black rounded-xl uppercase tracking-wider hover:bg-slate-50 transition-all text-xs"
                        wire:click="closePaymentModal">Cancel</button>
                    <button class="flex-[2] py-3 bg-[#161b97] hover:bg-yellow-600 text-white font-black rounded-xl uppercase tracking-wider shadow-lg shadow-yellow-500/20 text-xs flex items-center justify-center gap-2 transition-all"
                        wire:click="completeSaleWithPaymentAndPrint">
                        <span class="material-symbols-outlined text-base">print_connect</span>
                        Process & Print
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- SALE PREVIEW MODAL (Invoice) --}}
    @if($showSaleModal && $createdSale)
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden relative transform transition-all flex flex-col">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-5 py-3.5 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#161b97]/20 border border-[#161b97]/30 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#161b97] text-base">receipt_long</span>
                </div>
                <div>
                    <h3 class="font-black text-sm text-white uppercase tracking-widest leading-none">Transaction Finalized</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Invoice #{{ $createdSale->invoice_number }} &bull; {{ $createdSale->created_at->format('d/m/Y h:i A') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-emerald-500/20 border border-emerald-500/30 rounded-full text-[9px] font-black text-emerald-400 uppercase tracking-widest">{{ ucfirst($createdSale->payment_status ?? 'paid') }}</span>
                <button class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 transition-colors text-slate-400 hover:text-white" wire:click="closeModal">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
        </div>

        {{-- Invoice Content --}}
        <div class="p-5 bg-white" id="printableInvoice">
            {{-- Company Header --}}
            <div style="text-align:center; border-bottom:2px solid #1e293b; padding-bottom:10px; margin-bottom:10px;">
                <div style="font-size:20px; font-weight:900; letter-spacing:2px; color:#1e293b;">JaffnaGold (PVT) LTD</div>
                <div style="font-size:10px; color:#94a3b8; font-weight:700; letter-spacing:3px; text-transform:uppercase;">Gold Shop</div>
                <div style="font-size:11px; font-weight:700; color:#334155; margin-top:3px;">421/2, Doolmala, Thihariya, Kalagedihena.</div>
                <div style="font-size:11px; color:#334155;"><strong>TEL:</strong> (077) 9752950 &nbsp;&bull;&nbsp; <strong>EMAIL:</strong> JaffnaGoldlanka@gmail.com</div>
            </div>

            {{-- Customer / Invoice Meta --}}
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; gap:12px;">
                <div style="font-size:11px; line-height:1.7;">
                    @if($createdSale->customer)
                    <div><strong>Name:</strong>
                        @if($createdSale->customer->name === 'Walking Customer' && $createdSale->walking_customer_name)
                            {{ $createdSale->walking_customer_name }}
                        @else
                            {{ $createdSale->customer->name }}
                        @endif
                    </div>
                    <div><strong>Phone:</strong>
                        @if($createdSale->customer->name === 'Walking Customer' && $createdSale->walking_customer_phone)
                            {{ $createdSale->walking_customer_phone }}
                        @else
                            {{ $createdSale->customer->phone ?? '—' }}
                        @endif
                    </div>
                    <div><strong>Type:</strong> {{ ucfirst($createdSale->customer_type) }}</div>
                    @else
                    <div><strong>Name:</strong> Walk-in Customer</div>
                    @endif
                </div>
                <div style="font-size:11px; text-align:right; line-height:1.7;">
                    <div><strong>Invoice:</strong> {{ $createdSale->invoice_number }}</div>
                    <div><strong>Date:</strong> {{ $createdSale->created_at->format('d/m/Y h:i A') }}</div>
                    <div><strong>Status:</strong> <span style="color:#161b97; font-weight:800;">{{ ucfirst($createdSale->payment_status ?? 'Paid') }}</span></div>
                </div>
            </div>

            {{-- Items Table --}}
            <table style="width:100%; border-collapse:collapse; font-size:11px; margin-bottom:12px;">
                <thead>
                    <tr style="border-bottom:2px solid #1e293b; border-top:1px solid #e2e8f0;">
                        <th style="padding:5px 4px; text-align:left; font-weight:900; color:#1e293b;">#</th>
                        <th style="padding:5px 4px; text-align:left; font-weight:900; color:#1e293b;">Code</th>
                        <th style="padding:5px 4px; text-align:left; font-weight:900; color:#1e293b;">Item</th>
                        <th style="padding:5px 4px; text-align:right; font-weight:900; color:#1e293b;">Price</th>
                        <th style="padding:5px 4px; text-align:center; font-weight:900; color:#1e293b;">Qty</th>
                        <th style="padding:5px 4px; text-align:right; font-weight:900; color:#1e293b;">Discount</th>
                        <th style="padding:5px 4px; text-align:right; font-weight:900; color:#1e293b;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($createdSale->items as $index => $item)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:4px 4px; color:#64748b;">{{ $index + 1 }}</td>
                        <td style="padding:4px 4px; color:#64748b;">{{ $item->product_code ?? '' }}</td>
                        <td style="padding:4px 4px; font-weight:700; color:#1e293b;">{{ $item->product_name }}</td>
                        <td style="padding:4px 4px; text-align:right;">Rs.{{ number_format($item->unit_price, 2) }}</td>
                        <td style="padding:4px 4px; text-align:center;">{{ $item->quantity }}</td>
                        <td style="padding:4px 4px; text-align:right; color:#64748b;">
                            @php $discountAmount = $item->discount_per_unit ?? 0; @endphp
                            @if($item->discount_type === 'percentage' && $item->discount_percentage > 0)
                                {{ number_format($item->discount_percentage, 0) }}%
                            @elseif($discountAmount > 0)
                                Rs.{{ number_format($discountAmount * $item->quantity, 2) }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="padding:4px 4px; text-align:right; font-weight:700;">Rs.{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Payment Info + Order Summary --}}
            @php
                $originalSubtotal = $createdSale->items->sum(fn($i) => $i->unit_price * $i->quantity);
                $totalDiscountRs = $originalSubtotal - $createdSale->total_amount;
                $discountPercentage = $originalSubtotal > 0 ? ($totalDiscountRs / $originalSubtotal) * 100 : 0;
            @endphp
            <div style="display:flex; gap:16px; border-top:2px solid #1e293b; padding-top:10px;">
                {{-- Payment Info --}}
                <div style="flex:1; font-size:11px;">
                    <div style="font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:2px; color:#94a3b8; margin-bottom:6px;">Payment Information</div>
                    @if($createdSale->payments && $createdSale->payments->count() > 0)
                        @foreach($createdSale->payments as $payment)
                        <div style="padding:6px 8px; border-left:3px solid {{ $payment->is_completed ? '#161b97' : '#fbbf24' }}; background:#f8fafc; margin-bottom:4px; border-radius:0 4px 4px 0;">
                            <div><strong>{{ $payment->is_completed ? 'Paid' : 'Scheduled' }}:</strong> Rs.{{ number_format($payment->amount, 2) }}</div>
                            <div style="color:#64748b;">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</div>
                        </div>
                        @endforeach
                    @else
                        <div style="color:#94a3b8;">No payment info available</div>
                    @endif
                </div>
                {{-- Order Summary --}}
                <div style="flex:1; font-size:11px;">
                    <div style="font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:2px; color:#94a3b8; margin-bottom:6px; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">Order Summary</div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;"><span>Subtotal</span><span>Rs.{{ number_format($originalSubtotal, 2) }}</span></div>
                    @if($totalDiscountRs > 0)
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px; color:#64748b;"><span>Discount ({{ number_format($discountPercentage, 1) }}%)</span><span>- Rs.{{ number_format($totalDiscountRs, 2) }}</span></div>
                    @endif
                    <div style="display:flex; justify-content:space-between; font-weight:900; font-size:13px; border-top:1px solid #1e293b; padding-top:5px; margin-top:5px; color:#1e293b;">
                        <span>Grand Total</span><span>Rs.{{ number_format($createdSale->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Thank You --}}
            <div style="text-align:center; margin-top:10px; padding-top:8px; border-top:1px dashed #cbd5e1; font-size:10px; color:#94a3b8; font-weight:700; letter-spacing:1px; text-transform:uppercase;">
                Thank you for your business &mdash; www.JaffnaGold.lk
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="px-5 pb-5 pt-3 bg-slate-50 border-t border-slate-100 flex justify-center gap-3 shrink-0">
            <button class="px-6 py-2.5 bg-white border-2 border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition-colors"
                wire:click="createNewSale">Close &amp; New</button>
            <button class="px-8 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-slate-300 flex items-center gap-2 transition-colors"
                onclick="printInvoice()">
                <span class="material-symbols-outlined text-base">print</span> Print Invoice
            </button>
            <button class="px-8 py-2.5 bg-[#161b97] hover:bg-yellow-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-yellow-500/20 transition-colors"
                wire:click="downloadInvoice">Download PDF</button>
        </div>
    </div>
    @endif

    {{-- POS REPORT MODAL (Close Register) --}}
    @if($showCloseRegisterModal)
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden relative transform transition-all border border-slate-100">
        <div class="bg-slate-900 p-8 text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 bg-[url('https://JaffnaGold.lk/logo.png')] bg-center bg-no-repeat bg-contain scale-150"></div>
            <h3 class="text-xl font-black text-white uppercase tracking-[0.2em] relative z-10">Terminal Summary</h3>
            <p class="text-slate-400 text-[10px] font-bold mt-2 relative z-10">{{ date('d M Y | H:i') }}</p>
        </div>
        <div class="p-8 space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                    <label class="text-[8px] font-black text-slate-300 uppercase tracking-widest block mb-1">Session Inflow</label>
                    <span class="text-lg font-black text-slate-800">Rs. {{ number_format($sessionSummary['opening_cash'] ?? 0, 2) }}</span>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                    <label class="text-[8px] font-black text-slate-300 uppercase tracking-widest block mb-1">Total Turnover</label>
                    <span class="text-lg font-black text-[#e67e22]">Rs. {{ number_format($sessionSummary['total_pos_sales'] ?? 0, 2) }}</span>
                </div>
            </div>
            <div class="space-y-3 pt-4 border-t border-slate-50">
                <div class="flex justify-between items-center text-xs font-bold text-slate-500 py-1 transition-all hover:bg-slate-50 px-2 rounded">
                    <span>Terminal Cash Offset:</span>
                    <span class="text-slate-800">Rs. {{ number_format($sessionSummary['pos_cash_sales'] ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between items-center text-xs font-bold text-slate-500 py-1 transition-all hover:bg-slate-50 px-2 rounded">
                    <span>Internal Expenses:</span>
                    <span class="text-red-500">Rs. {{ number_format($sessionSummary['expenses'] ?? 0, 2) }}</span>
                </div>
            </div>
            <div class="p-6 bg-[#e67e22] rounded-2xl flex items-center justify-between text-white shadow-xl shadow-orange-500/20">
                <span class="text-[10px] font-black uppercase tracking-[0.2em]">Liquid Cash in Hand</span>
                <span class="text-2xl font-black tracking-tighter">Rs. {{ number_format($sessionSummary['expected_cash'] ?? 0, 2) }}</span>
            </div>
            <div class="pt-2">
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Managerial Notes</label>
                <textarea class="w-full p-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-xs font-bold outline-none italic" rows="2" wire:model="closeRegisterNotes" placeholder="Log terminal anomalies..."></textarea>
            </div>
        </div>
        <div class="p-6 bg-slate-50 border-t border-slate-100 flex gap-3">
            <button class="flex-1 py-4 bg-white border border-slate-200 text-slate-400 font-black rounded-xl uppercase tracking-widest text-[10px] shadow-sm" wire:click="$set('showCloseRegisterModal', false)">Lock Review</button>
            <button class="flex-1 py-4 bg-slate-800 text-white font-black rounded-xl uppercase tracking-widest text-[10px] shadow-xl shadow-slate-200" wire:click="closeRegisterAndRedirect">Finalize Close</button>
        </div>
    </div>
    @endif
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('POS System Loaded');

        // Real-time Clock Implementation
        function updateClock() {
            const el = document.getElementById('posClock');
            if (!el) return;
            const now = new Date();
            el.innerText = now.getHours().toString().padStart(2, '0') + ':' +
                now.getMinutes().toString().padStart(2, '0') + ':' +
                now.getSeconds().toString().padStart(2, '0');
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Native Post-load adjustments
        const initLayout = () => {
            const grid = document.getElementById('productGridContainer');
            if (grid) {
                grid.style.scrollBehavior = 'smooth';
            }
        };
        initLayout();

        // Keyboard Logic
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                if (@json(count($cart)) > 0) {
                    @this.validateAndCreateSale();
                }
            }
            if (e.key === 'F10') {
                e.preventDefault();
                @this.validateAndCreateSale();
            }
        });
    });

    // Print Invoice Function - Make it globally available
    function printInvoice() {
        console.log('=== Print Invoice Function Called ===');

        const printEl = document.getElementById('printableInvoice');
        if (!printEl) {
            console.error('ERROR: Printable invoice element not found');
            setTimeout(function() {
                console.log('Retrying print after 1 second...');
                const retryEl = document.getElementById('printableInvoice');
                if (retryEl) {
                    printInvoice();
                } else {
                    alert('Invoice not ready for printing. Please use the Print Invoice button.');
                }
            }, 1000);
            return;
        }

        console.log('Print element found:', printEl);

        // Get the actual receipt container
        const receiptContainer = printEl.querySelector('.receipt-container');
        if (!receiptContainer) {
            console.error('ERROR: Receipt container not found inside printableInvoice');
            alert('Invoice content not ready. Please try again.');
            return;
        }

        console.log('Receipt container found, preparing content...');

        // Clone the content to avoid modifying the original
        let content = receiptContainer.cloneNode(true);

        // Remove any buttons or interactive elements from print
        content.querySelectorAll('button, .no-print').forEach(el => el.remove());

        // Ensure footer is anchored to bottom: add a class and inline style to footer block
        const footerEl = content.querySelector('div[style*="border-top:2px solid #000"]') || content.querySelector('div:last-child');
        if (footerEl) {
            footerEl.classList.add('receipt-footer');
            // Use auto margin so it pushes to bottom inside the flex layout
            footerEl.style.marginTop = 'auto';
        }

        // Get the HTML string
        let htmlContent = content.outerHTML;

        console.log('Content prepared, opening print window...');

        // Open a new window
        const printWindow = window.open('', '_blank', 'width=800,height=600');

        if (!printWindow) {
            console.error('ERROR: Print window blocked by popup blocker');
            alert('Popup blocked. Please allow pop-ups for this site or use the Print Invoice button below.');
            return;
        }

        console.log('Print window opened successfully');

        // Complete HTML document with styles
        const fullHtml = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Invoice - JaffnaGold (PVT) LTD</title>
                <style>
                    @page { 
                        size: letter portrait; 
                        margin: 6mm; 
                    }

                    html, body { height: 100%; }

                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }

                    body { 
                        font-family: sans-serif; 
                        color: #000; 
                        background: #fff; 
                        padding: 10mm;
                        font-size: 12px;
                        line-height: 1.4;
                    }

                    .receipt-container { 
                        max-width: 800px; 
                        margin: 0 auto;
                        padding: 20px;
                        background: white;
                        display: flex;
                        flex-direction: column;
                        min-height: 100vh;
                        page-break-inside: avoid;
                    }

                    .receipt-footer { 
                        margin-top: auto !important; 
                        page-break-inside: avoid;
                    }
                    
                    .receipt-header { 
                        border-bottom: 3px solid #000; 
                        padding-bottom: 12px; 
                        margin-bottom: 12px; 
                    }
                    
                    .receipt-row { 
                        display: flex; 
                        align-items: center; 
                        justify-content: space-between; 
                    }
                    
                    .receipt-center { 
                        flex: 1; 
                        text-align: center; 
                    }
                    
                    .receipt-center h2 { 
                        margin: 0 0 4px 0; 
                        font-size: 2rem; 
                        letter-spacing: 2px;
                        font-weight: bold;
                    }
                    
                    table.receipt-table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin-top: 12px; 
                    }
                    
                    table.receipt-table th {
                        border-bottom: 1px solid #000; 
                        padding: 8px; 
                        text-align: left;
                        font-weight: bold;
                        background: none;
                    }
                    
                    table.receipt-table td { 
                        padding: 2px; 
                        text-align: left;
                        border: none;
                    }
                    
                    .text-end { 
                        text-align: right; 
                    }
                    
                    .text-muted {
                        color: #000000;
                    }
                    
                    p {
                        margin: 4px 0;
                    }
                    
                    strong {
                        font-weight: bold;
                    }
                    
                    hr {
                        border: none;
                        border-top: 1px solid #000;
                        margin: 8px 0;
                    }
                    
                    @media print {
                        body {
                            padding: 0;
                        }
                        
                        .receipt-container {
                            box-shadow: none !important;
                        }
                        
                        .receipt-container {
                            page-break-inside: avoid;
                        }
                    }
                </style>
            </head>
            <body>
                ${htmlContent}
                <script>
                    console.log('Print window document loaded');
                    window.onload = function() {
                        console.log('Print window fully loaded, triggering print dialog...');
                        setTimeout(function() {
                            try {
                                window.print();
                                console.log('Print dialog triggered');
                            } catch(e) {
                                console.error('Print failed:', e);
                                alert('Print failed: ' + e.message);
                            }
                        }, 500);
                    };
                <\/script>
            </body>
            </html>
        `;

        // Write the content
        try {
            printWindow.document.open();
            printWindow.document.write(fullHtml);
            printWindow.document.close();
            console.log('=== Content written to print window successfully ===');
        } catch (e) {
            console.error('ERROR writing to print window:', e);
            alert('Failed to prepare print: ' + e.message);
        }

        // Focus the print window
        printWindow.focus();
    }

    // Make printInvoice available globally
    window.printInvoice = printInvoice;
    console.log('printInvoice function registered globally');
</script>
</div>