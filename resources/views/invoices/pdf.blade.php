<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .company-details {
            font-size: 14px;
            color: #666;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info, .customer-info {
            width: 48%;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .info-row {
            margin-bottom: 8px;
            font-size: 14px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #d1d5db;
            padding: 12px;
            text-align: left;
        }
        .items-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        .total-row {
            margin-bottom: 8px;
            font-size: 16px;
        }
        .total-amount {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #2563eb;
            padding-top: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-unpaid {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-partially_paid {
            background-color: #fef3c7;
            color: #92400e;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Cold Storage Management</div>
        <div class="company-details">
            Professional Cold Storage Solutions<br>
            Email: info@coldstorage.com | Phone: +92-XXX-XXXXXXX
        </div>
    </div>

    <div class="invoice-details">
        <div class="invoice-info">
            <div class="section-title">Invoice Information</div>
            <div class="info-row">
                <span class="label">Invoice #:</span>
                {{ $invoice->invoice_number }}
            </div>
            <div class="info-row">
                <span class="label">Invoice Date:</span>
                {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}
            </div>
            <div class="info-row">
                <span class="label">Due Date:</span>
                {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}
            </div>
            <div class="info-row">
                <span class="label">Status:</span>
                <span class="status-badge status-{{ $invoice->status }}">
                    {{ str_replace('_', ' ', $invoice->status) }}
                </span>
            </div>
        </div>

        <div class="customer-info">
            <div class="section-title">Customer Information</div>
            <div class="info-row">
                <span class="label">Name:</span>
                {{ $customer->full_name }}
            </div>
            <div class="info-row">
                <span class="label">Phone:</span>
                {{ $customer->phone }}
            </div>
            <div class="info-row">
                <span class="label">CNIC:</span>
                {{ $customer->cnic }}
            </div>
            <div class="info-row">
                <span class="label">Address:</span>
                {{ $customer->address }}
            </div>
        </div>
    </div>

    <div class="section-title">Lot Information</div>
    <div class="info-row">
        <span class="label">Lot ID:</span>
        {{ $batch->id }}
    </div>
    <div class="info-row">
        <span class="label">Unit Price:</span>
        PKR {{ number_format($batch->unit_price, 2) }}
    </div>
    <div class="info-row">
        <span class="label">Number of Baskets:</span>
        {{ $batch->total_baskets }}
    </div>
    <div class="info-row">
        <span class="label">Storage Period:</span>
        {{ \Carbon\Carbon::parse($batch->created_at)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') }}
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>PKR {{ number_format($item->unit_price, 2) }}</td>
                <td>PKR {{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span class="label">Subtotal:</span>
            PKR {{ number_format($invoice->subtotal, 2) }}
        </div>
        <div class="total-row">
            <span class="label">Tax Amount:</span>
            PKR {{ number_format($invoice->tax_amount, 2) }}
        </div>
        <div class="total-row">
            <span class="label">Paid Amount:</span>
            PKR {{ number_format($invoice->paid_amount, 2) }}
        </div>
        <div class="total-amount">
            <span class="label">Balance Due:</span>
            PKR {{ number_format($invoice->balance_due, 2) }}
        </div>
    </div>

    @if($payments->count() > 0)
    <div class="section-title">Payment History</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>Payment Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                <td>PKR {{ number_format($payment->amount, 2) }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                <td>{{ $payment->reference_number ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Generated on {{ $generated_at }}</p>
        <p>This is a computer-generated invoice.</p>
    </div>
</body>
</html>
