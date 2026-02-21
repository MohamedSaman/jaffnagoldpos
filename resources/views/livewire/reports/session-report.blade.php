<div>
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="card-title">Daily POS Sessions History</h2>
            <div style="display: flex; gap: 10px;">
                <input type="date" wire:model.live="dateFrom" class="form-control" style="width: 150px;">
                <input type="date" wire:model.live="dateTo" class="form-control" style="width: 150px;">
            </div>
        </div>

        <div class="table-wrap">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Opened At</th>
                        <th>Closed At</th>
                        <th>User</th>
                        <th style="text-align: right;">Opening Cash</th>
                        <th style="text-align: right;">Sales/Payments</th>
                        <th style="text-align: right;">Expected Cash</th>
                        <th style="text-align: right;">Actual Closing</th>
                        <th style="text-align: right;">Difference</th>
                        <th style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $s)
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $s->opened_at->format('d M, Y') }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $s->opened_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            @if($s->closed_at)
                                <div style="font-weight:600;">{{ $s->closed_at->format('d M, Y') }}</div>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $s->closed_at->format('h:i A') }}</div>
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">In Progress</span>
                            @endif
                        </td>
                        <td>{{ $s->user?->name }}</td>
                        <td style="text-align: right; font-weight: 600;">Rs.{{ number_format($s->opening_balance, 2) }}</td>
                        <td style="text-align: right; font-weight: 600; color: var(--success);">Rs.{{ number_format($s->total_payments, 2) }}</td>
                        <td style="text-align: right; font-weight: 600; color: var(--gold-dark);">Rs.{{ number_format($s->expected_closing, 2) }}</td>
                        <td style="text-align: right; font-weight: 700;">
                            {{ $s->status === 'closed' ? 'Rs.' . number_format($s->closing_balance, 2) : '-' }}
                        </td>
                        <td style="text-align: right;">
                            @if($s->status === 'closed')
                                @if($s->difference > 0)
                                    <span style="color: var(--success); font-weight: 700;">+Rs.{{ number_format($s->difference, 2) }}</span>
                                @elseif($s->difference < 0)
                                    <span style="color: var(--danger); font-weight: 700;">Rs.{{ number_format($s->difference, 2) }}</span>
                                @else
                                    <span style="color: var(--text-muted);">Balanced</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($s->status === 'open')
                                <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: var(--success); padding: 4px 12px; border-radius: 20px;">Open</span>
                            @else
                                <span class="badge" style="background: rgba(100, 116, 139, 0.1); color: var(--text-muted); padding: 4px 12px; border-radius: 20px;">Closed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">No sessions found for the selected period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">
            {{ $sessions->links() }}
        </div>
    </div>
</div>
