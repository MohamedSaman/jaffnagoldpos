<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Label - {{ $sale->deliverySale->delivery_barcode ?? 'N/A' }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: 4in 6in;
            margin: 0;
        }

        body {
            width: 4in;
            height: 6in;
            font-family: sans-serif;
            color: #000;
            overflow: hidden;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .label-wrapper {
            position: absolute;
            width: 5.6in;
            height: 3in;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-90deg);
        }

        .label-border {
            border: 2px solid #000;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            padding: 6px 10px;
            border-bottom: 2px solid #000;
        }

        .company-name {
            font-size: 18px;
            font-weight: 900;
            line-height: 1.1;
        }

        .company-name span {
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        .meta {
            text-align: right;
            font-weight: 800;
        }

        .meta .method {
            font-size: 16px;
        }

        .meta .date {
            font-size: 12px;
            margin-top: 2px;
        }

        /* CUSTOMER + BARCODE ROW */
        .row {
            display: flex;
            width: 100%;
        }

        .customer-section {
            flex: 2;
            padding: 8px 10px;
            border-right: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .to-label {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .address {
            font-size: 14px;
            line-height: 1.3;
            white-space: pre-wrap;
        }

        .barcode-section {
            flex: 1;
            padding: 8px;
            text-align: center;
            border-bottom: 2px solid #000;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .barcode-section svg {
            width: 100%;
            height: 45px;
        }

        .barcode-text {
            font-size: 10px;
            font-weight: 700;
            margin-top: 3px;
        }

        /* PRICE + ICONS ROW */
        .price-row,
        .icons-row {
            flex: 1;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            padding: 6px;
            border-bottom: 2px solid #000;
        }

        .price-row {
            background: #d1d1d1;
            font-size: 24px;
            font-weight: 900;
        }

        .icons-row {
            justify-content: space-evenly;
            padding: 6px;
        }

        .icon-item {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .icon-box {
            width: 30px;
            height: 30px;
            border: 2px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* COD black label style */
        .cod-box {
            width: 45px;
            height: 30px;
            background: #000;
            color: #fff;
            font-weight: 900;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* FOOTER */
        .footer {
            padding: 6px;
            font-size: 10px;
            font-weight: 600;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="label-wrapper">
        <div class="label-border">

            <!-- HEADER -->
            <div class="header">
                <div class="company-name">
                    JAFFNA GOLD <br><span>COVERING</span>
                </div>
                <div class="meta">
                    <div class="method">
                        {{ strtoupper($sale->deliverySale->delivery_method ?? 'POST') }}
                    </div>
                    <div class="date">
                        {{ $sale->created_at->format('d.m.Y') }}
                    </div>
                </div>
            </div>

            <!-- CUSTOMER + BARCODE -->
            <div class="row">
                <div class="customer-section">
                    <div class="to-label">
                        TO : {{ $sale->deliverySale->customer_details ?? $sale->customer->address ?? '' }}

                    </div>
                    <div class="address">

                    </div>
                </div>

                <div class="barcode-section">
                    <svg id="barcode"></svg>
                    <div class="barcode-text">
                        {{ $sale->deliverySale->delivery_barcode }}
                    </div>
                </div>
            </div>

            <!-- PRICE + ICONS -->
            <div class="row">
                <div class="price-row">
                    Rs.{{ number_format($sale->total_amount, 2) }}
                </div>

                <div class="icons-row">

                    <!-- Fragile -->
                    <div class="icon-item">
                        <div class="icon-box">
                            <svg viewBox="0 0 100 100" width="40" height="40">
                                <rect x="5" y="5" width="90" height="90"
                                    fill="none" stroke="black" stroke-width="6" />

                                <!-- Glass -->
                                <path d="M30 25 H70 C70 50 55 60 50 60 
             C45 60 30 50 30 25 Z" fill="black" />

                                <line x1="50" y1="60" x2="50" y2="80"
                                    stroke="black" stroke-width="6" />
                                <line x1="40" y1="80" x2="60" y2="80"
                                    stroke="black" stroke-width="6" />
                            </svg>
                        </div>
                    </div>

                    <!-- Recycle -->
                    <div class="icon-item">
                        <div class="icon-box">
                            <svg viewBox="0 0 100 100" width="40" height="40">
                                <rect x="5" y="5" width="90" height="90"
                                    fill="none" stroke="black" stroke-width="6" />

                                <polygon points="50,20 60,40 40,40" fill="black" />
                                <polygon points="20,60 40,55 35,75" fill="black" />
                                <polygon points="80,60 65,75 60,55" fill="black" />
                            </svg>
                        </div>
                    </div>

                    <!-- This Side Up -->
                    <div class="icon-item">
                        <div class="icon-box">
                            <svg viewBox="0 0 100 100" width="40" height="40">
                                <!-- Outer Border -->
                                <rect x="5" y="5" width="90" height="90"
                                    fill="none" stroke="black" stroke-width="6" />

                                <!-- Left Arrow -->
                                <line x1="35" y1="75" x2="35" y2="35"
                                    stroke="black" stroke-width="8" />
                                <polygon points="35,20 20,40 50,40" fill="black" />

                                <!-- Right Arrow -->
                                <line x1="65" y1="75" x2="65" y2="35"
                                    stroke="black" stroke-width="8" />
                                <polygon points="65,20 50,40 80,40" fill="black" />
                            </svg>
                        </div>
                    </div>

                    <!-- COD -->
                    @if($sale->deliverySale && $sale->deliverySale->payment_method === 'Cash on Delivery')
                    <div class="icon-item">
                        <div class="cod-box">
                            COD
                        </div>
                    </div>
                    @else
                    <div class="icon-item">
                        <div class="cod-box">
                            PAID
                        </div>
                    </div>
                    @endif

                </div>
            </div>

            <!-- FOOTER -->
            <div class="footer">
                <div>076 1919 650 | 077 5287 556</div>
                <div>NO: 237 KKS ROAD, JAFFNA | NO: 37 NEW MARKET JAFFNA</div>
            </div>

        </div>
    </div>

    <script>
        JsBarcode("#barcode", "{{ $sale->deliverySale->delivery_barcode ?? 'N/A' }}", {
            format: "CODE128",
            width: 2,
            height: 50,
            displayValue: false,
            margin: 0
        });

        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>