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
            @php
                $breakdowns = null;
                
                // Get shipping breakdown - can be either single breakdown or multiple by store ID
                if ($order->shipping_breakdown) {
                    if (is_string($order->shipping_breakdown)) {
                        $breakdowns = json_decode($order->shipping_breakdown, true);
                    } else {
                        $breakdowns = $order->shipping_breakdown;
                    }
                }
            @endphp
            
            @if($breakdowns && is_array($breakdowns))
                @php
                    // Check if this is a multi-store breakdown (keys are numeric store IDs) or single breakdown
                    $isMultiStore = false;
                    $storeBreakdowns = [];
                    
                    // If it has 'zone' key, it's a single breakdown
                    if (isset($breakdowns['zone'])) {
                        $storeBreakdowns['single'] = $breakdowns;
                    } else {
                        // Otherwise check if keys are store IDs with 'breakdown' sub-key
                        foreach ($breakdowns as $key => $data) {
                            if (is_array($data) && isset($data['breakdown'])) {
                                $isMultiStore = true;
                                $storeBreakdowns[$key] = $data;
                            }
                        }
                        // If no multi-store pattern found, treat as single
                        if (!$isMultiStore) {
                            $storeBreakdowns['single'] = $breakdowns;
                        }
                    }
                @endphp
                
                @foreach($storeBreakdowns as $storeKey => $storeData)
                    @php
                        $breakdown = isset($storeData['breakdown']) ? $storeData['breakdown'] : $storeData;
                    @endphp
                    <div class="courier-item">
                        @if(isset($storeData['display']))
                        <div class="courier-from" style="margin-bottom: 8px;">
                            <strong>{{ $storeData['display'] }}</strong>
                        </div>
                        @endif
                        
                        @if(isset($breakdown['zone']))
                        <div class="breakdown-row">
                            <span class="breakdown-label">Zone:</span>
                            <span class="breakdown-value">{{ $breakdown['zone'] }}</span>
                        </div>
                        @endif
                        
                        @if(isset($breakdown['weight_kg']))
                        <div class="breakdown-row">
                            <span class="breakdown-label">Weight:</span>
                            <span class="breakdown-value">{{ $breakdown['weight_kg'] }}kg @if(isset($breakdown['extra_kg']) && $breakdown['extra_kg'] > 0) (+{{ $breakdown['extra_kg'] }}kg extra)@endif</span>
                        </div>
                        @endif
                        
                        @if(isset($breakdown['zone_base']))
                        <div class="breakdown-row">
                            <span class="breakdown-label">Base Tariff:</span>
                            <span class="breakdown-value">Rp {{ number_format($breakdown['zone_base'], 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        @if(isset($breakdown['weight_fee']))
                        <div class="breakdown-row">
                            <span class="breakdown-label">Weight Fee:</span>
                            <span class="breakdown-value">Rp {{ number_format($breakdown['weight_fee'], 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        @if(isset($breakdown['service_surcharge']))
                        <div class="breakdown-row">
                            <span class="breakdown-label">Service ({{ $breakdown['service_level'] ?? 'standard' }}):</span>
                            <span class="breakdown-value">Rp {{ number_format($breakdown['service_surcharge'], 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        @if(isset($storeData['cost']))
                        <div style="margin: 12px 0; padding-top: 12px; border-top: 1px solid #ddd;">
                            <strong>Shipping Cost:</strong>
                            <span style="float: right; color: #28a745; font-weight: bold;">Rp {{ number_format($storeData['cost'], 0, ',', '.') }}</span>
                            <div style="clear: both;"></div>
                        </div>
                        @endif
                    </div>
                @endforeach
                
                @if(count($storeBreakdowns) > 1)
                <div class="courier-item" style="background-color: #e8f5e9; border-left-color: #28a745;">
                    <strong style="color: #28a745;">Total Shipping Cost (All Stores):</strong>
                    <span style="float: right; color: #28a745; font-weight: bold; font-size: 16px;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    <div style="clear: both;"></div>
                </div>
                @endif
            @else
                <p style="color: #666; font-size: 13px; padding: 15px; background: #fafafa; border-radius: 4px;">
                    <strong>Shipping Cost:</strong> Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}<br>
                    <small>Zone: {{ $order->shipping_zone ?? 'Standard' }}</small>
                </p>
            @endif
        </div>

        <h2>Delivery Address</h2>
        <div class="shipping-info">
            <p>
                <strong>Recipient Name:</strong> {{ $order->shipping_name ?? 'Not Specified' }}<br>
                <strong>Phone:</strong> {{ $order->shipping_phone ?? 'Not Provided' }}<br>
                <strong>Address:</strong> {{ $order->shipping_address }}<br>
                <strong>City:</strong> {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}<br>
                <strong>Country:</strong> {{ $order->shipping_country }}
            </p>
            <div class="coordinates" style="margin-top: 12px;">
                <strong>Delivery Location:</strong><br>
                <a href="https://www.google.com/maps/search/{{ urlencode($order->shipping_address . ', ' . $order->shipping_city . ', ' . $order->shipping_province . ', ' . $order->shipping_country) }}" target="_blank" style="color: #007bff; text-decoration: none;">📍 View Delivery Address on Google Maps</a>
            </div>
            @if($order->store && $order->store->latitude && $order->store->longitude)
            <div class="coordinates" style="margin-top: 8px;">
                <strong>Store Pickup Location:</strong><br>
                Latitude: {{ $order->store->latitude }}<br>
                Longitude: {{ $order->store->longitude }}<br>
                <a href="https://maps.google.com/?q={{ $order->store->latitude }},{{ $order->store->longitude }}" target="_blank" style="color: #007bff; text-decoration: none;">📍 View Store on Google Maps</a>
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
