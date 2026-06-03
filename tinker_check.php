<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

// Now run queries
echo "=== CHECKING LOCAL DATABASE ===\n";
$order = \App\Models\Order::where('invoice_number', 'BH-20260603124736-353')->first();

if (!$order) {
    echo "Order with invoice BH-20260603124736-353 not found!\n";
    $allOrders = \App\Models\Order::orderByDesc('id')->limit(5)->get();
    if ($allOrders->isEmpty()) {
        echo "No orders in local database.\n";
    } else {
        echo "Recent orders in local database:\n";
        foreach ($allOrders as $o) {
            echo "  Order #" . $o->id . " - Invoice: " . $o->invoice_number . " - User: " . $o->user_id . "\n";
        }
    }
    exit(0);
}

$user = $order->user;
echo "=== ORDER FOUND ===\n";
echo "Order ID: " . $order->id . "\n";
echo "Invoice: " . $order->invoice_number . "\n";
echo "User ID: " . $order->user_id . "\n";
echo "User Email: " . $user->email . "\n";
echo "User Name: " . $user->name . "\n";
echo "User Google ID: " . ($user->google_id ?: "NULL - Did NOT login via Google") . "\n";
echo "Order Status: " . $order->status . "\n";
echo "Payment Processed: " . ($order->payment_processed ? "YES" : "NO") . "\n";
echo "\n=== EMAIL SENDING DECISION ===\n";
if ($user->google_id) {
    echo "✓ User HAS google_id - Email SHOULD be sent\n";
} else {
    echo "✗ User does NOT have google_id - Email is NOT sent (by design)\n";
    echo "  User logged in with: email/password\n";
    echo "  Emails only sent to Google-logged users\n";
}
