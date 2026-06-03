<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

// Find order and check shipping breakdown data
echo "=== CHECKING SHIPPING BREAKDOWN DATA ===\n";
$order = \App\Models\Order::where('invoice_number', 'BH-20260603124736-353')->first();

if (!$order) {
    echo "Order not found!\n";
    exit(1);
}

echo "Order ID: " . $order->id . "\n";
echo "Invoice: " . $order->invoice_number . "\n";
echo "Shipping Cost: " . $order->shipping_cost . "\n";
echo "Shipping Zone: " . ($order->shipping_zone ?? 'NULL') . "\n";
echo "Shipping Breakdown (raw): " . var_export($order->shipping_breakdown, true) . "\n";

if ($order->shipping_breakdown) {
    echo "\nBreakdown exists:\n";
    
    // Try to decode if string
    $breakdown = $order->shipping_breakdown;
    if (is_string($breakdown)) {
        echo "  Type: String (JSON)\n";
        $decoded = json_decode($breakdown, true);
        echo "  Decoded: " . var_export($decoded, true) . "\n";
    } else if (is_array($breakdown)) {
        echo "  Type: Array\n";
        echo "  Count: " . count($breakdown) . "\n";
        echo "  Content:\n";
        foreach ($breakdown as $key => $value) {
            echo "    [$key] => " . var_export($value, true) . "\n";
        }
    } else {
        echo "  Type: " . gettype($breakdown) . "\n";
    }
} else {
    echo "\n✗ Shipping breakdown is NULL or empty\n";
    echo "This is why courier breakdown doesn't show in email.\n";
}

echo "\n=== DATABASE RAW QUERY ===\n";
$raw = \Illuminate\Support\Facades\DB::selectOne("SELECT id, invoice_number, shipping_cost, shipping_zone, shipping_breakdown FROM orders WHERE invoice_number = 'BH-20260603124736-353'");
if ($raw) {
    echo "Raw DB shipping_breakdown: " . $raw->shipping_breakdown . "\n";
} else {
    echo "Order not found in database\n";
}
