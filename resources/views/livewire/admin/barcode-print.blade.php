<div>
    @push('styles')
    <style>
        /* Modern card styling */
        .barcode-print-page .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .barcode-print-page .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .barcode-print-page .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 12px 12px 0 0 !important;
            padding: 1.25rem 1.5rem;
        }

        /* Stats cards */
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 1.5rem;
            color: white;
        }

        .stats-card .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .stats-card .stats-label {
            font-size: 0.85rem;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* QR code display in table */
        .qr-display {
            background: #fff;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 6px;
            text-align: center;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Checkbox styling */
        .form-check-input:checked {
            background-color: #4361ee;
            border-color: #4361ee;
        }

        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            cursor: pointer;
        }

        /* Table styling */
        .barcode-print-page .table th {
            border-top: none;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .barcode-print-page .table td {
            vertical-align: middle;
            padding: 0.75rem;
        }

        /* Selected row highlight */
        .barcode-print-page .table tbody tr.selected-row {
            background-color: rgba(67, 97, 238, 0.08);
        }

        /* Action buttons */
        .btn-print-all {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-print-all:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .stats-card .stats-number {
                font-size: 1.8rem;
            }
        }
    </style>
    @endpush

    <div class="container-fluid p-3 barcode-print-page">
        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-2">
                    <i class="bi bi-upc-scan text-primary me-2"></i> Barcode Print Center
                </h3>
                <p class="text-muted mb-0">Print QR code labels for newly generated products</p>
            </div>
        </div>

        {{-- Stats Row --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stats-number">{{ $totalUnprinted }}</div>
                            <div class="stats-label mt-1">Barcodes Pending Print</div>
                        </div>
                        <div>
                            <i class="bi bi-printer fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stats-number">{{ count($selectedProducts) }}</div>
                            <div class="stats-label mt-1">Selected for Print</div>
                        </div>
                        <div>
                            <i class="bi bi-check2-square fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-stretch">
                <div class="card w-100 border-0 shadow-sm d-flex justify-content-center" style="border-radius: 12px;">
                    <div class="card-body d-flex flex-column justify-content-center gap-2 p-3">
                        <button class="btn btn-print-all w-100" onclick="printSelectedLabels()"
                            {{ count($selectedProducts) === 0 ? 'disabled' : '' }}>
                            <i class="bi bi-printer me-2"></i> Print Selected ({{ count($selectedProducts) }})
                        </button>
                        <button class="btn btn-outline-success w-100" wire:click="markAsPrinted"
                            {{ count($selectedProducts) === 0 ? 'disabled' : '' }}>
                            <i class="bi bi-check-circle me-2"></i> Mark as Printed
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Table --}}
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-list-ul text-primary me-2"></i> Products Waiting for Barcode Print
                    </h5>
                    <p class="text-muted small mb-0">Select products and click Print to generate QR code labels</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <div class="input-group" style="width: 280px;">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" wire:model.live="search"
                            placeholder="Search products...">
                    </div>
                    <select wire:model.live="perPage" class="form-select form-select-sm" style="width: 80px;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model.live="selectAll"
                                            id="selectAllCheckbox">
                                    </div>
                                </th>
                                <th>No</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Product Code</th>
                                <th>Barcode</th>
                                <th>QR Preview</th>
                                <th>Retail Price</th>
                                <th>Created</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $index => $product)
                            @php
                                $imagePath = $product->image;
                                $defaultImage = asset('images/product.jpg');
                                if ($imagePath && strpos($imagePath, 'storage/images/') === 0) {
                                    $imgFilename = substr($imagePath, strlen('storage/images/'));
                                    $imageUrl = url('/product-image-serve/' . $imgFilename);
                                } elseif ($imagePath) {
                                    $imageUrl = asset($imagePath);
                                } else {
                                    $imageUrl = null;
                                }
                            @endphp
                            <tr wire:key="barcode-{{ $product->id }}"
                                class="{{ in_array((string) $product->id, $selectedProducts) ? 'selected-row' : '' }}">
                                <td class="ps-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model.live="selectedProducts"
                                            value="{{ $product->id }}"
                                            id="product-{{ $product->id }}">
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark">{{ $products->firstItem() + $index }}</span>
                                </td>
                                <td>
                                    @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                        class="img-thumbnail" style="width: 45px; height: 45px; object-fit: cover;"
                                        onerror="this.src='{{ $defaultImage }}'">
                                    @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                        style="width: 45px; height: 45px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-success">Rs.{{ number_format($product->retail_price ?? 0, 2) }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark">{{ $product->name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $product->code }}</span>
                                </td>
                                <td>
                                    <code class="fs-6">{{ $product->barcode }}</code>
                                </td>
                                <td>
                                    <div class="qr-display">
                                        <div class="product-qr" id="qr-{{ $product->id }}"
                                            data-barcode="{{ $product->barcode }}"></div>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $product->created_at->format('d M Y') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="printSingleLabel('{{ $product->barcode }}', '{{ addslashes($product->name) }}', '{{ $product->code }}', '{{ number_format($product->retail_price ?? 0, 2) }}')"
                                            title="Print this label">
                                            <i class="bi bi-printer"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success"
                                            wire:click="markSingleAsPrinted({{ $product->id }})"
                                            wire:confirm="Mark this barcode as printed?"
                                            title="Mark as printed">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                        <h5 class="mt-3 fw-bold text-dark">All Barcodes Printed!</h5>
                                        <p class="text-muted">No products with unprinted barcodes found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($products->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-center">
                        {{ $products->links('livewire.custom-pagination') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        // Render QR codes in the table
        function renderQRCodes() {
            document.querySelectorAll('.product-qr[data-barcode]').forEach(function(el) {
                if (el.querySelector('canvas') || el.querySelector('img')) return; // Already rendered
                el.innerHTML = '';
                new QRCode(el, {
                    text: el.dataset.barcode,
                    width: 55,
                    height: 55,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            });
        }

        /**
         * Print a single dumbbell jewelry label
         * Total: 76mm (3"). Print area LEFT: 51mm (2"). Tag tail RIGHT: 25mm (1")
         * Height: 13mm (0.5")
         * Layout: Details LEFT, QR code RIGHT within the 51mm print area
         */
        function printSingleLabel(barcode, productName, productCode, retailPrice) {
            const printWindow = window.open('', '_blank', 'width=400,height=250');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Print Label</title>
                    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"><\/script>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body {
                            font-family: Arial, Helvetica, sans-serif;
                            margin: 0;
                            padding: 0;
                            background: white;
                        }
                        .label-wrapper {
                            width: 76mm;
                            height: 13mm;
                            display: flex;
                            align-items: center;
                        }
                        .print-area {
                            width: 51mm;
                            height: 13mm;
                            background: white;
                            display: flex;
                            align-items: center;
                            padding: 0.5mm 1mm;
                        }
                        .info-section {
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            gap: 0px;
                            overflow: hidden;
                        }
                        .info-code {
                            font-size: 7pt;
                            font-weight: bold;
                            color: #000;
                            white-space: nowrap;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            line-height: 1.15;
                        }
                        .info-name {
                            font-size: 5.5pt;
                            color: #222;
                            white-space: nowrap;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            line-height: 1.15;
                        }
                        .info-price {
                            font-size: 7pt;
                            font-weight: bold;
                            color: #000;
                            line-height: 1.15;
                        }
                        .info-barcode {
                            font-size: 5pt;
                            color: #333;
                            letter-spacing: 0.3px;
                            line-height: 1.15;
                        }
                        .qr-section {
                            flex-shrink: 0;
                            width: 11mm;
                            height: 11mm;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-left: 3mm;
                        }
                        .qr-section canvas, .qr-section img {
                            width: 11mm !important;
                            height: 11mm !important;
                        }
                        .tag-tail {
                            display: none;
                        }
                        @media print {
                            body { margin: 0; padding: 0; }
                            .tag-tail { display: none; }
                            @page {
                                size: 76mm 13mm;
                                margin: 0;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="label-wrapper">
                        <div class="print-area">
                            <div class="info-section">
                                <div class="info-code">${productCode}</div>
                                <div class="info-name">${productName}</div>
                                <div class="info-price">Rs.${retailPrice}</div>
                                <div class="info-barcode">${barcode}</div>
                            </div>
                            <div class="qr-section" id="qrcode"></div>
                        </div>
                        <div class="tag-tail"></div>
                    </div>
                    <script>
                        new QRCode(document.getElementById('qrcode'), {
                            text: '${barcode}',
                            width: 42,
                            height: 42,
                            colorDark: '#000000',
                            colorLight: '#ffffff',
                            correctLevel: QRCode.CorrectLevel.M
                        });
                        setTimeout(() => { window.print(); }, 600);
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        /**
         * Print selected dumbbell jewelry labels in bulk
         * Same layout: 51mm print area (details + QR), 25mm tail
         */
        function printSelectedLabels() {
            const selectedCheckboxes = document.querySelectorAll('input[type="checkbox"][wire\\:model\\.live="selectedProducts"]:checked');
            if (selectedCheckboxes.length === 0) {
                Swal.fire({icon: 'warning', title: 'No Selection', text: 'Please select at least one product.', timer: 2000, showConfirmButton: false});
                return;
            }

            const labelData = [];
            selectedCheckboxes.forEach(function(cb) {
                const row = cb.closest('tr');
                if (row) {
                    const cells = row.querySelectorAll('td');
                    // cells: [checkbox, no, image, name, code, barcode, qr, price, created, actions]
                    const productName = cells[3] ? cells[3].textContent.trim() : '';
                    const productCode = cells[4] ? cells[4].textContent.trim() : '';
                    const barcode = cells[5] ? cells[5].textContent.trim() : '';
                    const retailPrice = cells[7] ? cells[7].textContent.trim() : '0.00';
                    labelData.push({
                        name: productName,
                        code: productCode,
                        barcode: barcode,
                        price: retailPrice
                    });
                }
            });

            if (labelData.length === 0) return;

            const printWindow = window.open('', '_blank', 'width=600,height=500');

            let labelsHtml = labelData.map((item, idx) => `
                <div class="label-wrapper">
                    <div class="print-area">
                        <div class="info-section">
                            <div class="info-code">${item.code}</div>
                            <div class="info-name">${item.name}</div>
                            <div class="info-price">${item.price}</div>
                            <div class="info-barcode">${item.barcode}</div>
                        </div>
                        <div class="qr-section" id="qr-${idx}"></div>
                    </div>
                </div>
            `).join('');

            let qrScripts = labelData.map((item, idx) => `
                new QRCode(document.getElementById('qr-${idx}'), {
                    text: '${item.barcode}',
                    width: 42,
                    height: 42,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                });
            `).join('\n');

            printWindow.document.write(`
                <html>
                <head>
                    <title>Print Labels (${labelData.length})</title>
                    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"><\/script>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body {
                            font-family: Arial, Helvetica, sans-serif;
                            padding: 0;
                            margin: 0;
                            background: white;
                        }
                        .labels-grid {
                            display: flex;
                            flex-direction: column;
                            gap: 0;
                        }
                        .label-wrapper {
                            width: 76mm;
                            height: 13mm;
                            display: flex;
                            align-items: center;
                            page-break-inside: avoid;
                        }
                        .print-area {
                            width: 51mm;
                            height: 13mm;
                            background: white;
                            display: flex;
                            align-items: center;
                            padding: 0.5mm 1mm;
                        }
                        .info-section {
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            gap: 0px;
                            overflow: hidden;
                        }
                        .info-code {
                            font-size: 7pt;
                            font-weight: bold;
                            color: #000;
                            white-space: nowrap;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            line-height: 1.15;
                        }
                        .info-name {
                            font-size: 5.5pt;
                            color: #222;
                            white-space: nowrap;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            line-height: 1.15;
                        }
                        .info-price {
                            font-size: 7pt;
                            font-weight: bold;
                            color: #000;
                            line-height: 1.15;
                        }
                        .info-barcode {
                            font-size: 5pt;
                            color: #333;
                            letter-spacing: 0.3px;
                            line-height: 1.15;
                        }
                        .qr-section {
                            flex-shrink: 0;
                            width: 11mm;
                            height: 11mm;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-left: 0.5mm;
                        }
                        .qr-section canvas, .qr-section img {
                            width: 11mm !important;
                            height: 11mm !important;
                        }
                        @media print {
                            body { margin: 0; padding: 0; }
                            .print-area { border: none; }
                            @page {
                                size: 76mm 13mm;
                                margin: 0;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="labels-grid">
                        ${labelsHtml}
                    </div>
                    <script>
                        ${qrScripts}
                        setTimeout(() => { window.print(); }, 800);
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        // Run on initial load
        document.addEventListener('DOMContentLoaded', renderQRCodes);

        // Livewire v3 hook
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('morph.updated', () => {
                setTimeout(renderQRCodes, 150);
            });
        }

        // Fallback: MutationObserver
        const observer = new MutationObserver(function(mutations) {
            let shouldRender = false;
            mutations.forEach(function(m) {
                if (m.addedNodes.length > 0) shouldRender = true;
            });
            if (shouldRender) setTimeout(renderQRCodes, 200);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.querySelector('.barcode-print-page tbody');
            if (tableBody) {
                observer.observe(tableBody, { childList: true, subtree: true });
            }
        });
    </script>
    @endpush
</div>
