<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
        h1 { color: #2c3e50; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        h2 { color: #34495e; font-size: 16px; margin-top: 25px; margin-bottom: 10px; }
        h3 { color: #555; font-size: 14px; margin: 15px 0 8px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background-color: #f5f5f5; font-weight: bold; }
        .summary-table { margin-top: 20px; }
        .summary-table tr td { padding: 10px; }
        .summary-table tr.total { font-weight: bold; font-size: 16px; background-color: #f0f0f0; }
        .summary-table tr.total td { padding: 12px; }
        .shipping-info { background-color: #f9f9f9; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0; }
        .shipping-info p { margin: 5px 0; }
        .coordinates { font-size: 12px; color: #666; background: #f0f0f0; padding: 8px; border-radius: 4px; margin: 10px 0; font-family: monospace; }
        .courier-section { background-color: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .courier-item { background-color: white; padding: 12px; margin-bottom: 10px; border-left: 3px solid #007bff; }
        .courier-from { font-weight: bold; color: #007bff; margin-bottom: 5px; }
        .book-title { color: #555; font-style: italic; margin: 5px 0; }
        .breakdown-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; }
        .breakdown-label { color: #666; }
        .breakdown-value { font-weight: 500; }
        .total-price { font-weight: bold; color: #28a745; border-top: 1px solid #ddd; padding-top: 8px; margin-top: 8px; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px; font-size: 12px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Receipt</h1>
        
        <p>Hello {{ $user->name }},</p>
        <p>Thank you for your purchase! Your order has been confirmed and will be processed shortly.</p>

        <h2>Order Details</h2>
        <p>
            <strong>Invoice Number:</strong> {{ $order->invoice_number }}<br>
            <strong>Order Date:</strong> {{ $order->created_at->format('M d, Y H:i A') }}<br>
            <strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">{{ ucfirst($order->status) }}</span>
        </p>

        <h2>Items Ordered</h2>
        <table>
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderDetails as $detail)
                <tr>
                    <td>{{ $detail->book->title }}</td>
                    <td style="text-align: center;">{{ $detail->quantity }}</td>
                    <td>Rp {{ number_format($detail->book->price, 0, ',', '.') }}</td>
                    <td><strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h2>Courier Breakdown & Logistics</h2>
        <div class="courier-section">
            @if($order->shipping_breakdown && is_array($order->shipping_breakdown))
                @foreach($order->shipping_breakdown as $courier)
                <div class="courier-item">
                    <div class="courier-from">From: {{ $courier['from'] ?? 'Store' }}</div>
                    @if(!empty($courier['items']))
                        @foreach($courier['items'] as $item)
                        <div class="book-title">{{ $item['title'] ?? '' }}</div>
                        @endforeach
                    @endif
                    <div style="margin: 8px 0;">
                        <strong>Zone {{ $courier['zone'] ?? 'A' }}</strong>
                        <span style="float: right; color: #28a745; font-weight: bold;">Rp {{ number_format($courier['total_cost'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div style="font-size: 12px; color: #666; margin-top: 5px;">Combined Weight: {{ $courier['weight'] ?? '0' }}kg</div>
                    <div class="breakdown-row">
                        <span class="breakdown-label">Base Tariff:</span>
                        <span class="breakdown-value">Rp {{ number_format($courier['base_tariff'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="breakdown-row">
                        <span class="breakdown-label">Weight Fee:</span>
                        <span class="breakdown-value">Rp {{ number_format($courier['weight_fee'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="breakdown-row">
                        <span class="breakdown-label">Service ({{ $courier['service'] ?? 'standard' }}):</span>
                        <span class="breakdown-value">Rp {{ number_format($courier['service_fee'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            @else
                <p style="color: #666; font-size: 13px;">Shipping cost: Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</p>
            @endif
        </div>

        <h2>Delivery Address</h2>
        <div class="shipping-info">
            <p>
                <strong>Recipient Name:</strong> {{ $order->shipping_name }}<br>
                <strong>Phone:</strong> {{ $order->shipping_phone }}<br>
                <strong>Address:</strong> {{ $order->shipping_address }}<br>
                <strong>City:</strong> {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}<br>
                <strong>Country:</strong> {{ $order->shipping_country }}
            </p>
            @if($order->store && $order->store->latitude && $order->store->longitude)
            <div class="coordinates" style="margin-top: 12px;">
                <strong>Pinpoint Coordinates:</strong><br>
                Latitude: {{ $order->store->latitude }}<br>
                Longitude: {{ $order->store->longitude }}<br>
                <a href="https://maps.google.com/?q={{ $order->store->latitude }},{{ $order->store->longitude }}" target="_blank" style="color: #007bff; text-decoration: none;">View on Google Maps</a>
            </div>
            @endif
            <p style="margin-top: 10px;">
                <strong>Shipping Status:</strong> {{ ucfirst($order->shipping_status) }}
                @if($order->tracking_number)
                <br><strong>Tracking Number:</strong> {{ $order->tracking_number }}
                @endif
            </p>
        </div>

        <h2>Price Summary</h2>
        <table class="summary-table">
            <tr>
                <td><strong>Subtotal (Items)</strong></td>
                <td style="text-align: right;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Shipping Cost</strong></td>
                <td style="text-align: right;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td>TOTAL AMOUNT</td>
                <td style="text-align: right;">Rp {{ number_format(($order->total_price + $order->shipping_cost), 0, ',', '.') }}</td>
            </tr>
        </table>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ config('app.url') }}/orders/{{ $order->id }}" style="display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">View Full Order Details</a>
        </div>

        <div class="footer">
            <p>Thank you for shopping with us!</p>
            <p style="margin-top: 15px; color: #999;">If you have any questions about your order, please contact our support team.</p>
            <p style="color: #999; font-size: 11px; margin-top: 10px;">© {{ date('Y') }} BookStore. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
