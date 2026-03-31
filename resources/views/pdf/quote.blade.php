<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TradePulse Quote</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            color: #111111;
            background: #ffffff;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.45;
        }

        .page {
            width: 100%;
            max-width: 760px;
            margin: 0 auto;
            padding: 28px 30px 18px;
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 18px;
            margin-bottom: 24px;
        }

        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: top;
        }

        .header-left {
            width: 65%;
        }

        .header-right {
            width: 35%;
            text-align: right;
        }

        .logo {
            max-height: 48px;
            max-width: 150px;
            margin-bottom: 10px;
        }

        .business-name {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.2px;
            margin-bottom: 6px;
        }

        .quote-label {
            font-size: 11px;
            color: #555555;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .meta-line {
            margin-top: 4px;
            color: #333333;
        }

        .section {
            margin-bottom: 22px;
        }

        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            color: #555555;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .customer-card {
            border: 1px solid #e8e8e8;
            border-radius: 10px;
            padding: 12px 14px;
            background: #fafafa;
        }

        .customer-line {
            margin: 0 0 6px;
            font-size: 13px;
        }

        .customer-line:last-child {
            margin-bottom: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e8e8e8;
            border-radius: 10px;
            overflow: hidden;
        }

        thead th {
            background: #f6f6f6;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #444444;
            padding: 10px 12px;
            border-bottom: 1px solid #e5e5e5;
        }

        tbody td {
            padding: 11px 12px;
            border-bottom: 1px solid #efefef;
            font-size: 13px;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .align-right {
            text-align: right;
        }

        .total-wrap {
            margin-top: 16px;
            border: 1px solid #111111;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .total-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            color: #333333;
            margin-bottom: 4px;
            font-weight: 700;
        }

        .total-price {
            font-size: 32px;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: -0.4px;
            margin: 0;
        }

        .notes-box {
            border: 1px solid #e8e8e8;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .notes-line {
            margin: 0 0 6px;
            color: #333333;
        }

        .notes-line:last-child {
            margin-bottom: 0;
        }

        .footer {
            margin-top: 26px;
            padding-top: 12px;
            border-top: 1px solid #ececec;
            font-size: 10px;
            color: #666666;
            text-align: center;
        }

        @media only screen and (max-width: 560px) {
            .page {
                padding: 18px 16px;
            }

            .header,
            .header-left,
            .header-right {
                display: block;
                width: 100%;
                text-align: left;
            }

            .header-right {
                margin-top: 14px;
            }

            .business-name {
                font-size: 21px;
            }

            .total-price {
                font-size: 28px;
            }

            thead th,
            tbody td {
                padding: 8px 9px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
<div class="page">
    <header class="header">
        <div class="header-left">
            @if (!empty($logoDataUri))
                <img src="{{ $logoDataUri }}" alt="Business logo" class="logo">
            @endif
            <div class="business-name">{{ $businessName }}</div>
            <div class="quote-label">Quote</div>
            <div class="meta-line">Date: {{ $quoteDate->format('d M Y') }}</div>
        </div>
        <div class="header-right">
            @if (!empty($contactPhone))
                <div class="meta-line">{{ $contactPhone }}</div>
            @endif
            <div class="meta-line">{{ $contactEmail }}</div>
        </div>
    </header>

    <section class="section">
        <div class="section-title">Customer</div>
        <div class="customer-card">
            <p class="customer-line"><strong>Name:</strong> {{ $customerName !== '' ? $customerName : 'Homeowner' }}</p>
            <p class="customer-line"><strong>Job:</strong> {{ $jobDescription }}</p>
        </div>
    </section>

    <section class="section">
        <div class="section-title">Quote Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="align-right">Quantity</th>
                    <th class="align-right">Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Fence Posts</td>
                    <td class="align-right">{{ number_format($breakdown['posts_qty']) }}</td>
                    <td class="align-right">&pound;{{ number_format($breakdown['posts_price'], 2) }}</td>
                </tr>
                <tr>
                    <td>Timber Boards / Panels</td>
                    <td class="align-right">{{ number_format($breakdown['boards_qty']) }}</td>
                    <td class="align-right">&pound;{{ number_format($breakdown['boards_price'], 2) }}</td>
                </tr>
                <tr>
                    <td>Labour</td>
                    <td class="align-right">{{ rtrim(rtrim(number_format($breakdown['length'], 2, '.', ''), '0'), '.') }}m</td>
                    <td class="align-right">&pound;{{ number_format($breakdown['labour_cost'], 2) }}</td>
                </tr>
                <tr>
                    <td>VAT ({{ number_format($breakdown['vat_rate'], 2) }}%)</td>
                    <td class="align-right">-</td>
                    <td class="align-right">&pound;{{ number_format($breakdown['vat_amount'], 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-wrap">
            <div class="total-label">Total Price</div>
            <p class="total-price">&pound;{{ number_format($breakdown['total_price'], 2) }}</p>
        </div>
    </section>

    <section class="section">
        <div class="section-title">Notes</div>
        <div class="notes-box">
            <p class="notes-line">Includes materials and labour</p>
            <p class="notes-line">Valid for 14 days (until {{ $validUntil->format('d M Y') }})</p>
            @if (!empty($paymentTerms))
                <p class="notes-line">Payment terms: {{ $paymentTerms }}</p>
            @endif
        </div>
    </section>

    @if (! $isPro)
        <footer class="footer">
            Powered by TradePulse
        </footer>
    @endif
</div>
</body>
</html>
