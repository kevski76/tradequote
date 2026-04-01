<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
            line-height: 1.5;
        }

        /* ── Header band ───────────────────────────── */
        .header-band {
            background: #00684e;
            padding: 22px 30px 20px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left-cell {
            vertical-align: middle;
            width: 60%;
        }

        .header-right-cell {
            vertical-align: top;
            text-align: right;
            width: 40%;
        }

        .logo {
            max-height: 44px;
            max-width: 140px;
            margin-bottom: 8px;
        }

        .business-name {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 6px;
        }

        .quote-badge {
            display: inline-block;
            background: #74f3c6;
            color: #00533e;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 2px 9px;
            border-radius: 20px;
        }

        .header-meta {
            font-size: 11.5px;
            color: rgba(255,255,255,0.85);
            margin-bottom: 3px;
        }

        .header-date {
            margin-top: 10px;
            font-size: 10.5px;
            color: rgba(255,255,255,0.65);
        }

        /* ── Mint accent strip ─────────────────────── */
        .accent-strip {
            height: 4px;
            background: #74f3c6;
        }

        /* ── Content area ──────────────────────────── */
        .content {
            padding: 24px 30px 18px;
        }

        .section {
            margin-bottom: 22px;
        }

        .section-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #00684e;
            font-weight: 700;
            margin-bottom: 10px;
            padding-left: 9px;
            border-left: 3px solid #74f3c6;
        }

        /* ── Customer card ─────────────────────────── */
        .customer-card {
            border: 1px solid #c5e8d9;
            border-radius: 8px;
            padding: 12px 14px;
            background: #f0faf6;
        }

        .customer-line {
            margin: 0 0 5px;
            font-size: 12.5px;
        }

        .customer-line:last-child {
            margin-bottom: 0;
        }

        /* ── Items table ───────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #c5e8d9;
            border-radius: 8px;
            overflow: hidden;
        }

        thead th {
            background: #00684e;
            color: #ffffff;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 10px 12px;
        }

        tbody tr:nth-child(even) td {
            background: #f7fdfb;
        }

        tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5f3ec;
            font-size: 12.5px;
            color: #1a1a1a;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .align-right {
            text-align: right;
        }

        /* ── Total box ─────────────────────────────── */
        .total-wrap {
            margin-top: 12px;
            background: #00684e;
            border-radius: 8px;
            padding: 14px 16px;
        }

        .total-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #74f3c6;
            margin-bottom: 4px;
            font-weight: 700;
        }

        .total-price {
            font-size: 32px;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #ffffff;
            margin: 0;
        }

        /* ── Notes box ─────────────────────────────── */
        .notes-box {
            border: 1px solid #c5e8d9;
            border-left: 4px solid #74f3c6;
            border-radius: 0 8px 8px 0;
            padding: 12px 14px;
            background: #f7fdfb;
        }

        .notes-line {
            margin: 0 0 5px;
            color: #333333;
            font-size: 12px;
        }

        .notes-line:last-child {
            margin-bottom: 0;
        }

        /* ── Footer ────────────────────────────────── */
        .footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #c5e8d9;
            font-size: 10px;
            color: #888888;
            text-align: center;
        }

        .footer-logo {
            vertical-align: middle;
            margin-right: 5px;
            width: 13px;
            height: 14px;
        }

        .footer-brand {
            color: #00684e;
            font-weight: 700;
        }
    </style>
</head>
<body>

<!-- Green header band -->
<div class="header-band">
    <table class="header-table">
        <tr>
            <td class="header-left-cell">
                @if (!empty($logoDataUri))
                    <img src="{{ $logoDataUri }}" alt="Business logo" class="logo"><br>
                @endif
                <div class="business-name">{{ $businessName }}</div>
            </td>
            <td class="header-right-cell">
                <div class="quote-badge">Quote</div>
                <div class="header-date">{{ $quoteDate->format('d M Y') }}</div>
                @if (!empty($contactPhone))
                    <div class="header-meta" style="margin-top:8px;">{{ $contactPhone }}</div>
                @endif
                <div class="header-meta">{{ $contactEmail }}</div>
            </td>
        </tr>
    </table>
</div>

<!-- Mint accent strip -->
<div class="accent-strip"></div>

<!-- Main content -->
<div class="content">

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
            <svg class="footer-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 42" fill="#00684e">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.2 5.633 8.6.855 0 5.633v26.51l16.2 9 16.2-9v-8.442l7.6-4.223V9.856l-8.6-4.777-8.6 4.777V18.3l-5.6 3.111V5.633ZM38 18.301l-5.6 3.11v-6.157l5.6-3.11V18.3Zm-1.06-7.856-5.54 3.078-5.54-3.079 5.54-3.078 5.54 3.079ZM24.8 18.3v-6.157l5.6 3.111v6.158L24.8 18.3Zm-1 1.732 5.54 3.078-13.14 7.302-5.54-3.078 13.14-7.3v-.002Zm-16.2 7.89 7.6 4.222V38.3L2 30.966V7.92l5.6 3.111v16.892ZM8.6 9.3 3.06 6.222 8.6 3.143l5.54 3.08L8.6 9.3Zm21.8 15.51-13.2 7.334V38.3l13.2-7.334v-6.156ZM9.6 11.034l5.6-3.11v14.6l-5.6 3.11v-14.6Z"/>
            </svg>
            <span>Powered by <span class="footer-brand">TradePulse</span></span>
        </footer>
    @endif

</div>
</body>
</html>
