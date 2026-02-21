<div>
    <!-- Report Header & Filters -->
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <h2 class="card-title" style="font-size: 20px;">Business Reports</h2>
                <p style="font-size: 13px; color: var(--text-muted);">Overview of your showroom performance</p>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="margin-bottom: 4px;">From Date</label>
                    <input type="date" wire:model.live="dateFrom" class="form-control" style="width: 160px;">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="margin-bottom: 4px;">To Date</label>
                    <input type="date" wire:model.live="dateTo" class="form-control" style="width: 160px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="stat-grid">
        <div class="stat-card gold">
            <div class="stat-label">Total Sales</div>
            <div class="stat-value">Rs.{{ number_format($totalSales, 2) }}</div>
            <div class="stat-sub">{{ $sales->count() }} invoices generated</div>
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
        </div>
        <div class="stat-card blue">
            <div class="stat-label">Total Purchases</div>
            <div class="stat-value">Rs.{{ number_format($totalPurchases, 2) }}</div>
            <div class="stat-sub">Stock procurement cost</div>
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></div>
        </div>
        <div class="stat-card red">
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value">Rs.{{ number_format($totalExpenses, 2) }}</div>
            <div class="stat-sub">Shop & utility costs</div>
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="7" y1="15" x2="7.01" y2="15"/><line x1="12" y1="15" x2="12.01" y2="15"/></svg></div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">Gross Profit</div>
            <div class="stat-value" style="color: {{ $grossProfit >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                Rs.{{ number_format($grossProfit, 2) }}
            </div>
            <div class="stat-sub">Estimated earnings</div>
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left Column: Daily Breakdown -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daily Sales Breakdown</h3>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoices</th>
                            <th style="text-align: right;">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailySales as $day)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($day->date)->format('D, d M Y') }}</td>
                                <td><span class="badge badge-gray">{{ $day->count }}</span></td>
                                <td style="text-align: right; font-weight: 600;">Rs.{{ number_format($day->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 20px;">No sales data for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Alerts & High priority -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Low Stock Alert -->
            <div class="card" style="border-color: var(--danger);">
                <div class="card-header">
                    <h3 class="card-title" style="color: var(--danger);"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:8px;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Low Stock Alerts</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($lowStockProducts as $lp)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: var(--bg3); border-radius: 8px;">
                            <div>
                                <div style="font-size: 13px; font-weight: 600;">{{ $lp->name }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $lp->purity->name }}</div>
                            </div>
                            <span class="badge badge-red">{{ $lp->stock_quantity }} left</span>
                        </div>
                    @empty
                        <p style="font-size: 13px; color: var(--text-muted); text-align: center;">All stock levels are healthy.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Expenses</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($expenses->take(5) as $ex)
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <div style="font-size: 13px; font-weight: 500;">{{ $ex->category }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ \Carbon\Carbon::parse($ex->date)->format('d M') }}</div>
                            </div>
                            <div style="font-weight: 600; color: var(--danger);">-Rs.{{ number_format($ex->amount, 2) }}</div>
                        </div>
                    @empty
                        <p style="font-size: 13px; color: var(--text-muted); text-align: center;">No expenses recorded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>