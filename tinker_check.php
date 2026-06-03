<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

// Full email test
echo "=== FULL EMAIL TEST ===\n";
$order = \App\Models\Order::where('invoice_number', 'BH-20260603124736-353')->first();

if (!$order) {
    echo "Order not found!\n";
    exit(1);
}

$user = $order->user;
echo "Order: " . $order->invoice_number . "\n";
echo "User: " . $user->email . "\n";
echo "Google ID: " . ($user->google_id ? "✓ Yes" : "✗ No") . "\n\n";

// Step 1: Render the view
echo "Step 1: Rendering email template...\n";
try {
    $html = \Illuminate\Support\Facades\View::make('emails.order-receipt', [
        'order' => $order,
        'user' => $user,
        'orderDetails' => $order->order_details,
    ])->render();
    
    echo "✓ Rendered (" . strlen($html) . " bytes)\n";
    
    // Check for key sections
    $checks = [
        'Courier Breakdown' => strpos($html, 'Courier Breakdown') !== false,
        'Google Maps' => strpos($html, 'google') !== false,
        'Delivery Address' => strpos($html, 'Delivery Address') !== false,
        'Rp currency' => strpos($html, 'Rp') !== false,
        'Zone' => strpos($html, 'Zone') !== false,
        'Base Tariff' => strpos($html, 'Base Tariff') !== false,
    ];
    
    echo "\nContent checks:\n";
    foreach ($checks as $section => $found) {
        echo "  " . ($found ? "✓" : "✗") . " " . $section . "\n";
    }
    
    if (!$checks['Base Tariff']) {
        echo "\n⚠️ Base Tariff not found in HTML - breakdown may not be rendering\n";
        echo "Searching for 'From:' in HTML...\n";
        if (strpos($html, 'From:') !== false) {
            echo "  ✓ Found 'From:' - courier section exists\n";
        } else {
            echo "  ✗ 'From:' not found - courier section not rendering\n";
        }
    }
    
} catch (\Exception $e) {
    echo "✗ Render error: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 2: Send email
echo "\nStep 2: Sending email...\n";
try {
    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrderReceiptMail($order));
    echo "✓ Email sent successfully to: " . $user->email . "\n";
} catch (\Exception $e) {
    echo "✗ Send error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== COMPLETE ===\n";
echo "Email has been sent. Check Gmail inbox/spam folder.\n";
echo "The breakdown should display if all checks above show ✓\n";
