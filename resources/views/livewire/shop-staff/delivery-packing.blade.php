<div wire:keydown.escape.window="closeModal">
    <!-- Load TailWind & Premium Fonts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <style>
        :root {
            --accent-500: #161b97;
            --accent-700: #12167d;
        }

        html,
        body {
            font-family: 'Inter', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 12px;
        }

        /* Smooth transitions for all interactive elements */
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-card.active {
            transform: translateY(-2px);
        }

        .order-card {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
        }

        /* Pulse animation for pending badge */
        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        .pulse-dot {
            animation: pulse-dot 2s ease-in-out infinite;
        }

        /* Fade-in animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.4s ease-out both;
        }

        /* Staggered animation */
        .stagger-1 {
            animation-delay: 0.05s;
        }

        .stagger-2 {
            animation-delay: 0.1s;
        }

        .stagger-3 {
            animation-delay: 0.15s;
        }

        .stagger-4 {
            animation-delay: 0.2s;
        }
    </style>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50/30">

        {{-- ═══════════════ HEADER ═══════════════ --}}
        <div class="bg-white/80 backdrop-blur-xl border-b border-slate-200/60 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-11 h-11 rounded-xl bg-gradient-to-br from-[#161b97] to-[#4361ee] flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <span class="material-symbols-outlined text-white text-xl">local_shipping</span>
                        </div>
                        <div>
                            <h1 class="text-xl font-black text-slate-800 tracking-tight">Delivery Packing</h1>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Manage &
                                pack delivery orders</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="bg-slate-100/80 border border-slate-200/60 px-4 py-2 rounded-xl flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400 text-base">calendar_today</span>
                            <span class="text-xs font-bold text-slate-600">{{ now()->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

            {{-- ═══════════════ SUCCESS ALERT ═══════════════ --}}
            @if (session()->has('success'))
                <div
                    class="mb-6 flex items-center gap-3 px-5 py-4 bg-emerald-50 border border-emerald-200/60 rounded-2xl shadow-sm animate-fade-in">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white text-lg">check_circle</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                        <p class="text-[10px] text-emerald-600 font-medium mt-0.5">The order status has been updated
                            successfully.</p>
                    </div>
                </div>
            @endif

            {{-- ═══════════════ STATS OVERVIEW CARDS ═══════════════ --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">

                {{-- All Orders --}}
                <button wire:click="setFilter('all')"
                    class="stat-card group relative overflow-hidden rounded-2xl border p-5 text-left
                    {{ $filterStatus === 'all' ? 'bg-gradient-to-br from-[#161b97] to-[#4361ee] border-transparent shadow-xl shadow-blue-500/20 active' : 'bg-white border-slate-200/60 hover:border-blue-200 hover:shadow-lg' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center {{ $filterStatus === 'all' ? 'bg-white/15' : 'bg-blue-50 group-hover:bg-blue-100' }} transition-colors">
                            <span
                                class="material-symbols-outlined {{ $filterStatus === 'all' ? 'text-white' : 'text-[#161b97]' }} text-xl">inventory_2</span>
                        </div>
                        <span
                            class="material-symbols-outlined {{ $filterStatus === 'all' ? 'text-white/40' : 'text-slate-200' }} text-sm">arrow_forward</span>
                    </div>
                    <p
                        class="text-[10px] font-black uppercase tracking-widest {{ $filterStatus === 'all' ? 'text-blue-200' : 'text-slate-400' }}">
                        All Orders</p>
                    <p class="text-3xl font-black mt-1 {{ $filterStatus === 'all' ? 'text-white' : 'text-slate-800' }}">
                        {{ $totalCount }}</p>
                </button>

                {{-- Pending --}}
                <button wire:click="setFilter('pending')"
                    class="stat-card group relative overflow-hidden rounded-2xl border p-5 text-left
                    {{ $filterStatus === 'pending' ? 'bg-gradient-to-br from-amber-500 to-orange-500 border-transparent shadow-xl shadow-amber-500/20 active' : 'bg-white border-slate-200/60 hover:border-amber-200 hover:shadow-lg' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center {{ $filterStatus === 'pending' ? 'bg-white/15' : 'bg-amber-50 group-hover:bg-amber-100' }} transition-colors">
                            <span
                                class="material-symbols-outlined {{ $filterStatus === 'pending' ? 'text-white' : 'text-amber-500' }} text-xl">pending_actions</span>
                        </div>
                        @if($pendingCount > 0 && $filterStatus !== 'pending')
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 pulse-dot"></span>
                        @endif
                    </div>
                    <p
                        class="text-[10px] font-black uppercase tracking-widest {{ $filterStatus === 'pending' ? 'text-amber-100' : 'text-slate-400' }}">
                        Pending</p>
                    <p
                        class="text-3xl font-black mt-1 {{ $filterStatus === 'pending' ? 'text-white' : 'text-slate-800' }}">
                        {{ $pendingCount }}</p>
                </button>

                {{-- Packed --}}
                <button wire:click="setFilter('packed')"
                    class="stat-card group relative overflow-hidden rounded-2xl border p-5 text-left
                    {{ $filterStatus === 'packed' ? 'bg-gradient-to-br from-emerald-500 to-teal-500 border-transparent shadow-xl shadow-emerald-500/20 active' : 'bg-white border-slate-200/60 hover:border-emerald-200 hover:shadow-lg' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center {{ $filterStatus === 'packed' ? 'bg-white/15' : 'bg-emerald-50 group-hover:bg-emerald-100' }} transition-colors">
                            <span
                                class="material-symbols-outlined {{ $filterStatus === 'packed' ? 'text-white' : 'text-emerald-500' }} text-xl">check_box</span>
                        </div>
                        <span
                            class="material-symbols-outlined {{ $filterStatus === 'packed' ? 'text-white/40' : 'text-slate-200' }} text-sm">arrow_forward</span>
                    </div>
                    <p
                        class="text-[10px] font-black uppercase tracking-widest {{ $filterStatus === 'packed' ? 'text-emerald-100' : 'text-slate-400' }}">
                        Packed</p>
                    <p
                        class="text-3xl font-black mt-1 {{ $filterStatus === 'packed' ? 'text-white' : 'text-slate-800' }}">
                        {{ $packedCount }}</p>
                </button>

                {{-- Canceled --}}
                <button wire:click="setFilter('canceled')"
                    class="stat-card group relative overflow-hidden rounded-2xl border p-5 text-left
                    {{ $filterStatus === 'canceled' ? 'bg-gradient-to-br from-red-500 to-rose-500 border-transparent shadow-xl shadow-red-500/20 active' : 'bg-white border-slate-200/60 hover:border-red-200 hover:shadow-lg' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center {{ $filterStatus === 'canceled' ? 'bg-white/15' : 'bg-red-50 group-hover:bg-red-100' }} transition-colors">
                            <span
                                class="material-symbols-outlined {{ $filterStatus === 'canceled' ? 'text-white' : 'text-red-500' }} text-xl">cancel</span>
                        </div>
                        <span
                            class="material-symbols-outlined {{ $filterStatus === 'canceled' ? 'text-white/40' : 'text-slate-200' }} text-sm">arrow_forward</span>
                    </div>
                    <p
                        class="text-[10px] font-black uppercase tracking-widest {{ $filterStatus === 'canceled' ? 'text-red-100' : 'text-slate-400' }}">
                        Canceled</p>
                    <p
                        class="text-3xl font-black mt-1 {{ $filterStatus === 'canceled' ? 'text-white' : 'text-slate-800' }}">
                        {{ $canceledCount }}</p>
                </button>
            </div>

            {{-- ═══════════════ SEARCH & FILTERS ═══════════════ --}}
            <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-4 mb-6">
                <div class="flex flex-col sm:flex-row gap-4 items-center">
                    <div class="relative flex-1 w-full">
                        <span
                            class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                        <input type="text" wire:model.live.debounce.500ms="search"
                            placeholder="Search by order number or customer name..."
                            class="w-full pl-12 pr-4 py-3 bg-slate-50/80 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 outline-none focus:ring-2 focus:ring-[#161b97]/15 focus:border-[#161b97] transition-all placeholder:text-slate-400" />
                    </div>
                    <label
                        class="flex items-center gap-3 px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors shrink-0">
                        <div class="relative">
                            <input type="checkbox" wire:model.live="todayOnly" class="sr-only peer" />
                            <div class="w-10 h-5 bg-slate-300 rounded-full peer-checked:bg-[#161b97] transition-colors">
                            </div>
                            <div
                                class="absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full shadow-sm transition-transform peer-checked:translate-x-5">
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-600 whitespace-nowrap">Today Only</span>
                    </label>
                </div>
            </div>

            {{-- ═══════════════ CURRENT FILTER INDICATOR ═══════════════ --}}
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider">
                        @if($filterStatus === 'all') All Orders
                        @elseif($filterStatus === 'pending') Pending Orders
                        @elseif($filterStatus === 'packed') Packed Orders
                        @elseif($filterStatus === 'canceled') Canceled Orders
                        @else Orders
                        @endif
                    </h2>
                    <span
                        class="bg-slate-100 text-slate-500 text-[10px] font-black px-2.5 py-1 rounded-full">{{ count($sales) }}
                        results</span>
                </div>
            </div>

            {{-- ═══════════════ ORDERS LIST ═══════════════ --}}
            <div class="space-y-4">
                @forelse($sales as $index => $sale)
                    @php
                        $status = $sale->delivery_status ?? 'N/A';
                        $statusConfig = match ($status) {
                            'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'schedule', 'dot' => 'bg-amber-500'],
                            'packed' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'check_circle', 'dot' => 'bg-emerald-500'],
                            'delivered' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'local_shipping', 'dot' => 'bg-blue-500'],
                            default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-200', 'icon' => 'info', 'dot' => 'bg-slate-400'],
                        };
                    @endphp

                    <div
                        class="order-card bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden animate-fade-in stagger-{{ min($index + 1, 4) }}">
                        {{-- Order Header --}}
                        <div
                            class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-slate-500 text-xl">receipt_long</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-black text-slate-800">Order #{{ $sale->order_no }}</h3>
                                        <div
                                            class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg {{ $statusConfig['bg'] }} {{ $statusConfig['border'] }} border">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }} {{ $status === 'pending' ? 'pulse-dot' : '' }}"></span>
                                            <span
                                                class="text-[10px] font-black uppercase tracking-wider {{ $statusConfig['text'] }}">{{ ucfirst($status) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="material-symbols-outlined text-slate-300 text-sm">schedule</span>
                                        <span
                                            class="text-xs text-slate-400 font-medium">{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y, h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                            <button
                                class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#161b97] to-[#4361ee] text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-blue-500/15 hover:shadow-xl hover:shadow-blue-500/25 transition-all group"
                                wire:click="showPackingModal({{ $sale->id }})">
                                <span
                                    class="material-symbols-outlined text-base group-hover:scale-110 transition-transform">visibility</span>
                                View Details
                            </button>
                        </div>

                        {{-- Order Body --}}
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-blue-500 text-base">person</span>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Customer
                                        </p>
                                        <p class="text-sm font-bold text-slate-700">
                                            {{ $this->getCustomerDisplayName($sale) }}</p>
                                        @php $phone = $this->getCustomerPhone($sale); @endphp
                                        @if($phone)
                                            <p class="text-[10px] text-slate-400 font-medium">{{ $phone }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-emerald-500 text-base">payments</span>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total
                                            Amount</p>
                                        <p class="text-sm font-black text-slate-800">Rs.
                                            {{ number_format($sale->total_amount, 2) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg {{ ($sale->due_amount ?? 0) > 0 ? 'bg-red-50' : 'bg-slate-50' }} flex items-center justify-center shrink-0">
                                        <span
                                            class="material-symbols-outlined {{ ($sale->due_amount ?? 0) > 0 ? 'text-red-500' : 'text-slate-400' }} text-base">account_balance_wallet</span>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Due Amount
                                        </p>
                                        <p
                                            class="text-sm font-bold {{ ($sale->due_amount ?? 0) > 0 ? 'text-red-600' : 'text-slate-500' }}">
                                            Rs. {{ number_format($sale->due_amount ?? 0, 2) }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Items Preview --}}
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <div class="flex items-center gap-2 mb-2.5">
                                    <span class="material-symbols-outlined text-slate-400 text-sm">shopping_bag</span>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Items
                                        ({{ count($sale->items) }})</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($sale->items->take(5) as $item)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200/80 rounded-lg text-xs font-medium text-slate-600">
                                            {{ $item->product_name ?? ($item->product->name ?? 'Product') }}
                                            <span
                                                class="bg-slate-200 text-slate-600 text-[9px] font-black px-1.5 py-0.5 rounded">×{{ $item->quantity }}</span>
                                        </span>
                                    @endforeach
                                    @if(count($sale->items) > 5)
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-50 border border-blue-200/60 rounded-lg text-[10px] font-black text-[#161b97]">
                                            +{{ count($sale->items) - 5 }} more
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Delivery Info --}}
                            @if($sale->deliverySale)
                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    <div class="flex flex-wrap gap-3">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-50 border border-violet-200/60 rounded-lg text-[10px] font-bold text-violet-700">
                                            <span class="material-symbols-outlined text-sm">local_shipping</span>
                                            {{ $sale->deliverySale->delivery_method ?? 'N/A' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-200/60 rounded-lg text-[10px] font-bold text-blue-700">
                                            <span class="material-symbols-outlined text-sm">credit_card</span>
                                            {{ $sale->deliverySale->payment_method ?? 'N/A' }}
                                        </span>
                                        @if($sale->deliverySale->delivery_barcode)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200/60 rounded-lg text-[10px] font-bold text-slate-600 font-mono">
                                                <span class="material-symbols-outlined text-sm">qr_code</span>
                                                {{ $sale->deliverySale->delivery_barcode }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($sale->deliverySale->customer_details)
                                        <div class="mt-2 px-3 py-2 bg-amber-50 border border-amber-200/60 rounded-lg">
                                            <p class="text-[9px] font-black text-amber-600 uppercase tracking-widest mb-1">Delivery Address / Info</p>
                                            <p class="text-xs text-amber-800 font-medium whitespace-pre-line">{{ $sale->deliverySale->customer_details }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    {{-- Empty State --}}
                    <div
                        class="bg-white rounded-2xl border border-slate-200/60 shadow-sm py-20 text-center animate-fade-in">
                        <div
                            class="w-20 h-20 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-5">
                            <span class="material-symbols-outlined text-4xl text-slate-200">inbox</span>
                        </div>
                        <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-2">No Orders Found</h3>
                        <p class="text-xs text-slate-400 font-medium max-w-sm mx-auto">There are no orders matching your
                            current filter. Try adjusting your search or filter criteria.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════════ PACKING MODAL ═══════════════ --}}
    @if($showModal && $modalSale)
        <div class="fixed inset-0 z-[5000] flex items-center justify-center p-4" x-data
            x-init="document.body.classList.add('overflow-hidden')"
            x-destroy="document.body.classList.remove('overflow-hidden')">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

            {{-- Modal Content --}}
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden relative z-10 transform transition-all animate-fade-in"
                style="max-height: 90vh;">

                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-[#161b97] to-[#4361ee] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-2xl">package_2</span>
                        </div>
                        <div>
                            <h2 class="text-base font-black text-white uppercase tracking-wider">Packing Details</h2>
                            <p class="text-xs text-blue-200 font-bold mt-0.5">Order #{{ $modalSale->order_no }}</p>
                        </div>
                    </div>
                    <button
                        class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors"
                        wire:click="closeModal">
                        <span class="material-symbols-outlined text-white text-lg">close</span>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="overflow-y-auto custom-scrollbar" style="max-height: calc(90vh - 180px);">
                    <div class="p-6">
                        {{-- Order Info Grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-blue-500 text-base">person</span>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Customer</p>
                                </div>
                                <p class="text-sm font-bold text-slate-700">{{ $this->getCustomerDisplayName($modalSale) }}</p>
                                @php $modalPhone = $this->getCustomerPhone($modalSale); @endphp
                                @if($modalPhone)
                                    <p class="text-[10px] text-slate-400 font-medium mt-1">{{ $modalPhone }}</p>
                                @endif
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-emerald-500 text-base">calendar_today</span>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Date</p>
                                </div>
                                <p class="text-sm font-bold text-slate-700">
                                    {{ \Carbon\Carbon::parse($modalSale->created_at)->format('d M Y') }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-[#161b97] text-base">payments</span>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total</p>
                                </div>
                                <p class="text-sm font-black text-slate-800">Rs.
                                    {{ number_format($modalSale->total_amount, 2) }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-amber-500 text-base">credit_card</span>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Payment</p>
                                </div>
                                <p class="text-sm font-bold text-slate-700">
                                    {{ ucfirst($modalSale->payment_status ?? 'N/A') }}</p>
                            </div>
                        </div>

                        {{-- Delivery Details --}}
                        @if($modalSale->deliverySale)
                            <div class="mb-6 bg-violet-50 rounded-xl border border-violet-100 p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="material-symbols-outlined text-violet-600 text-base">local_shipping</span>
                                    <h3 class="text-xs font-black text-violet-700 uppercase tracking-widest">Delivery Information</h3>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    <div>
                                        <p class="text-[9px] font-black text-violet-400 uppercase tracking-widest">Method</p>
                                        <p class="text-sm font-bold text-violet-800">{{ $modalSale->deliverySale->delivery_method ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-violet-400 uppercase tracking-widest">Payment</p>
                                        <p class="text-sm font-bold text-violet-800">{{ $modalSale->deliverySale->payment_method ?? 'N/A' }}</p>
                                    </div>
                                    @if($modalSale->deliverySale->delivery_barcode)
                                        <div>
                                            <p class="text-[9px] font-black text-violet-400 uppercase tracking-widest">Barcode</p>
                                            <p class="text-sm font-bold text-violet-800 font-mono">{{ $modalSale->deliverySale->delivery_barcode }}</p>
                                        </div>
                                    @endif
                                </div>
                                @if($modalSale->deliverySale->customer_details)
                                    <div class="mt-3 pt-3 border-t border-violet-200/60">
                                        <p class="text-[9px] font-black text-violet-400 uppercase tracking-widest mb-1">Customer Details / Address</p>
                                        <p class="text-sm text-violet-800 font-medium whitespace-pre-line">{{ $modalSale->deliverySale->customer_details }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif


                        <div class="mb-2">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-[#161b97] text-lg">checklist</span>
                                <h3 class="text-xs font-black text-slate-700 uppercase tracking-widest">Products to Pack
                                </h3>
                                <span
                                    class="bg-[#161b97] text-white text-[9px] font-black px-2 py-0.5 rounded-full">{{ count($modalSale->items) }}</span>
                            </div>

                            <div class="bg-slate-50 rounded-xl border border-slate-100 overflow-hidden">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-slate-100/80">
                                            <th
                                                class="px-5 py-3 text-left text-[9px] font-black text-slate-500 uppercase tracking-widest">
                                                #</th>
                                            <th
                                                class="px-5 py-3 text-left text-[9px] font-black text-slate-500 uppercase tracking-widest">
                                                Product Name</th>
                                            <th
                                                class="px-5 py-3 text-center text-[9px] font-black text-slate-500 uppercase tracking-widest">
                                                Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($modalSale->items as $idx => $item)
                                            <tr class="hover:bg-white transition-colors">
                                                <td class="px-5 py-3.5">
                                                    <span
                                                        class="w-6 h-6 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-[10px] font-black text-slate-500">{{ $idx + 1 }}</span>
                                                </td>
                                                <td class="px-5 py-3.5">
                                                    <p class="text-sm font-bold text-slate-700">
                                                        {{ $item->product->name ?? $item->product_name ?? 'Product' }}</p>
                                                    @if($item->variant_value)
                                                        <p class="text-[10px] text-slate-400 font-medium mt-0.5">Variant:
                                                            {{ $item->variant_value }}</p>
                                                    @endif
                                                </td>
                                                <td class="px-5 py-3.5 text-center">
                                                    <span
                                                        class="inline-flex items-center justify-center w-10 h-10 bg-white border-2 border-[#161b97]/15 rounded-xl text-base font-black text-[#161b97]">{{ $item->quantity }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-200 flex items-center justify-between gap-4">
                    <button
                        class="px-6 py-3 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-500 uppercase tracking-wider hover:bg-slate-50 hover:border-slate-300 transition-all"
                        wire:click="closeModal">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">close</span>
                            Close
                        </span>
                    </button>
                    @if(($modalSale->delivery_status ?? '') !== 'packed')
                        <button
                            class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-emerald-500/20 hover:shadow-xl hover:shadow-emerald-500/30 transition-all flex items-center gap-2 group"
                            wire:click="markPacked" wire:loading.attr="disabled" wire:target="markPacked">
                            <span wire:loading.remove wire:target="markPacked"
                                class="material-symbols-outlined text-lg group-hover:scale-110 transition-transform">check_circle</span>
                            <span wire:loading wire:target="markPacked">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                    </circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                            <span wire:loading.remove wire:target="markPacked">Mark as Packed</span>
                            <span wire:loading wire:target="markPacked">Processing...</span>
                        </button>
                    @else
                        <div class="flex items-center gap-2 px-6 py-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                            <span class="material-symbols-outlined text-emerald-500 text-lg">verified</span>
                            <span class="text-xs font-black text-emerald-700 uppercase tracking-wider">Already Packed</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>