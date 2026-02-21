<div>
    <!-- STAT CARDS -->
    <div class="stat-grid">
        <div class="stat-card gold">
            <div class="stat-label">Today's Sales</div>
            <div class="stat-value">Rs.{{ number_format($todaySales, 0) }}</div>
            <div class="stat-sub">{{ $todaySalesCount }} transactions</div>
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
        </div>
        <div class="stat-card green">
            <div class="stat-label">Monthly Sales</div>
            <div class="stat-value">Rs.{{ number_format($monthlySales, 0) }}</div>
            <div class="stat-sub">{{ now()->format('F Y') }}</div>
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
        </div>
        <div class="stat-card blue">
            <div class="stat-label">Total Customers</div>
            <div class="stat-value">{{ $totalCustomers }}</div>
            <div class="stat-sub">Registered customers</div>
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
        </div>
        <div class="stat-card red">
            <div class="stat-label">Total Due</div>
            <div class="stat-value">Rs.{{ number_format($totalDue, 0) }}</div>
            <div class="stat-sub">Pending collections</div>
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">Low Stock Items</div>
            <div class="stat-value">{{ $lowStock }}</div>
            <div class="stat-sub">Need restocking</div>
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;"><path d="M12 22.08l-9-5.12a2 2 0 0 1-1-1.73V7.27a2 2 0 0 1 1-1.74l9-5.2a2 2 0 0 1 2 0l9 5.2a2 2 0 0 1 1 1.74v7.96a2 2 0 0 1-1 1.73l-9 5.12a2 2 0 0 1-2 0z"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></div>
        </div>
        <div class="stat-card gold">
            <div class="stat-label">Today's Expenses</div>
            <div class="stat-value">Rs.{{ number_format($totalExpenses, 0) }}</div>
            <div class="stat-sub">{{ now()->format('d M Y') }}</div>
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="7" y1="15" x2="7.01" y2="15"/><line x1="12" y1="15" x2="12.01" y2="15"/></svg></div>
        </div>
    </div>

    @if($currentRate)
    <div style="background:linear-gradient(135deg,rgba(201,168,76,0.12),rgba(201,168,76,0.04));border:1px solid rgba(201,168,76,0.25);border-radius:12px;padding:18px 24px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="font-size:12px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Current Gold Rate</div>
            <div style="font-size:24px;font-weight:700;color:var(--gold-dark);">
                <svg style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:8px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>{{ $currentRate->purity->name }} – Rs.{{ number_format($currentRate->rate_per_gram, 2) }} / gram
            </div>
        </div>
        <div style="font-size:12px;color:var(--text-muted);">{{ $currentRate->date->format('d M Y') }}</div>
    </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <!-- Recent Sales -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Recent Sales</div>
                <a href="{{ route('pos') }}" class="btn btn-gold btn-sm">+ New Sale</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                        <tr>
                            <td><span style="font-family:monospace;color:var(--gold-dark);">{{ $sale->invoice_no }}</span></td>
                            <td>{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                            <td>Rs.{{ number_format($sale->grand_total, 2) }}</td>
                            <td>
                                @if($sale->due_amount > 0)
                                    <span class="badge badge-red">Due Rs.{{ number_format($sale->due_amount, 0) }}</span>
                                @else
                                    <span class="badge badge-green">Paid</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:30px;">No sales yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:8px;color:var(--danger);"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>Low Stock Alert</div>
                <a href="{{ route('products') }}" class="btn btn-outline btn-sm">View All</a>
            </div>
            @forelse($lowStockProducts as $product)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border);">
                <div>
                    <div style="font-size:13.5px;font-weight:600;color:var(--text);">{{ $product->name }}</div>
                    <div style="font-size:12px;color:var(--text-muted);">{{ $product->category->name }} · {{ $product->purity->name }}</div>
                </div>
                <span class="badge {{ $product->stock_quantity == 0 ? 'badge-red' : 'badge-gold' }}">
                    {{ $product->stock_quantity }} left
                </span>
            </div>
            @empty
            <div class="empty-state" style="padding:30px 0;">
                <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:40px;height:40px;"><polyline points="20 6 9 17 4 12"/></svg></div>
                <p>All products well stocked</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
