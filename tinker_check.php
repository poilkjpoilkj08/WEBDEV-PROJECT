<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

// Find order and test email rendering
echo "=== TESTING EMAIL RENDERING ===\n";
$order = \App\Models\Order::where('invoice_number', 'BH-20260603124736-353')->first();

if (!$order) {
    echo "Order not found!\n";
    exit(1);
}

$user = $order->user;
echo "Testing email for Order: " . $order->invoice_number . "\n";
echo "User: " . $user->email . "\n\n";

// Try to render the view
try {
    $mailable = new \App\Mail\OrderReceiptMail($order);
    $html = $mailable->render();
    
    if (strpos($html, 'error') !== false || strpos($html, 'Error') !== false) {
        echo "✗ View contains error text:\n";
        echo substr($html, 0, 500) . "\n";
    } else {
        echo "✓ View rendered successfully\n";
        echo "Size: " . strlen($html) . " bytes\n";
        
        // Check for key elements
        if (strpos($html, 'Rp') === false) {
            echo "⚠️ WARNING: No 'Rp' currency found in email!\n";
        } else {
            echo "✓ Currency formatting found\n";
        }
        
        if (strpos($html, 'google.com/maps') === false) {
            echo "⚠️ WARNING: No Google Maps link found!\n";
        } else {
            echo "✓ Google Maps link found\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ Error rendering email:\n";
    echo "  " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
}

// Now try sending
echo "\n=== ATTEMPTING TO SEND ===\n";
try {
    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrderReceiptMail($order));
    echo "✓ Email sent\n";
} catch (\Exception $e) {
    echo "✗ Send error: " . $e->getMessage() . "\n";
}
