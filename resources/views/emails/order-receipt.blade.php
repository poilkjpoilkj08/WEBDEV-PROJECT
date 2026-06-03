<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { color: #2c3e50; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        h2 { color: #34495e; font-size: 16px; margin-top: 25px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background-color: #f5f5f5; font-weight: bold; }
        .summary-table { margin-top: 20px; }
        .summary-table tr td { padding: 10px; }
        .summary-table tr.total { font-weight: bold; font-size: 16px; background-color: #f0f0f0; }
        .summary-table tr.total td { padding: 12px; }
        .button { display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .shipping-info { background-color: #f9f9f9; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0; }
        .shipping-info p { margin: 5px 0; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px; font-size: 12px; color: #666; text-align: center; }
        .price-breakdown { font-size: 14px; margin-top: 15px; }
        .price-row { display: flex; justify-content: space-between; margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Order Receipt</h1>
        
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
                    <td>Rp {{ number_format($detail->book->price / 100, 0, ',', '.') }}</td>
                    <td><strong>Rp {{ number_format($detail->subtotal / 100, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h2>Price Breakdown</h2>
        <table class="summary-table">
            <tr>
                <td><strong>Subtotal (Items)</strong></td>
                <td style="text-align: right;">Rp {{ number_format($order->total_price / 100, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Shipping Cost</strong></td>
                <td style="text-align: right;">Rp {{ number_format($order->shipping_cost / 100, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td>💰 TOTAL AMOUNT</td>
                <td style="text-align: right;">Rp {{ number_format(($order->total_price + $order->shipping_cost) / 100, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="shipping-info">
            <h2 style="margin-top: 0;">📍 Delivery Address</h2>
            <p>
                {{ $order->shipping_address }}
            </p>
            <p>
                <strong>Shipping Status:</strong> {{ ucfirst($order->shipping_status) }}
            </p>
        </div>

        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/orders/{{ $order->id }}" class="button">View Full Order Details</a>
        </div>

        <div class="footer">
            <p>Thank you for shopping with us! 🎉</p>
            <p style="margin-top: 15px; color: #999;">If you have any questions about your order, please contact our support team.</p>
            <p style="color: #999; font-size: 11px; margin-top: 10px;">© {{ date('Y') }} BookStore. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
