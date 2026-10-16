<table class="table premium-table align-middle mb-0">
    <thead>
        <tr>
            <th class="ps-4">Date</th>
            <th>Txn ID</th>
            <th>Type</th>
            <th>Category</th>
            <th>Details & Remarks</th>
            <th>Payment Method</th>
            <th>Logged By</th>
            <th class="text-end pe-4">Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ledger as $item)
        <tr>
            <td class="ps-4 text-muted small font-monospace">
                {{ $item->date->format('d M, Y') }}
            </td>
            <td class="font-monospace fw-bold text-light small">
                {{ $item->txn_id }}
            </td>
            <td>
                @if($item->type === 'Credit')
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                        <i class="bi bi-arrow-down-left me-1"></i> Cash In
                    </span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                        <i class="bi bi-arrow-up-right me-1"></i> Cash Out
                    </span>
                @endif
            </td>
            <td>
                <span class="text-white fw-semibold small">{{ $item->category }}</span>
            </td>
            <td class="text-muted small" style="max-width: 320px;">
                {{ $item->description }}
            </td>
            <td>
                <span class="badge bg-dark border border-secondary border-opacity-25 text-warning font-monospace py-1 px-2" style="font-size: 0.75rem;">
                    {{ $item->method }}
                </span>
            </td>
            <td class="text-secondary small">
                {{ $item->created_by }}
            </td>
            <td class="text-end pe-4 font-monospace fw-bold fs-6 {{ $item->type === 'Credit' ? 'text-success' : 'text-danger' }}">
                {{ $item->type === 'Credit' ? '+' : '-' }} AED {{ number_format($item->amount, 2) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-2 d-block mb-1 text-secondary"></i>
                No transaction records found matching the given filter.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
