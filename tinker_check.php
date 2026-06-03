<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

// Find order and test email rendering with detailed error reporting
echo "=== EMAIL DEBUGGING ===\n";
$order = \App\Models\Order::where('invoice_number', 'BH-20260603124736-353')->first();

if (!$order) {
    echo "Order not found!\n";
    exit(1);
}

$user = $order->user;
echo "Order: " . $order->invoice_number . "\n";
echo "User: " . $user->email . "\n";
echo "User has google_id: " . ($user->google_id ? "YES" : "NO") . "\n\n";

// Step 1: Check if google_id exists (email only sends if it does)
if (!$user->google_id) {
    echo "✗ ERROR: User does NOT have google_id!\n";
    echo "Email will NOT be sent - by design, only Google-logged users get emails.\n";
    echo "User needs to log in with Google first.\n";
    exit(0);
}

// Step 2: Try to render the view
echo "Step 1: Rendering email template...\n";
try {
    $view = \Illuminate\Support\Facades\View::make('emails.order-receipt', [
        'order' => $order,
        'user' => $user,
        'orderDetails' => $order->order_details,
    ]);
    $html = $view->render();
    
    if (empty($html)) {
        echo "✗ View rendered but is EMPTY!\n";
        exit(1);
    }
    
    echo "✓ View rendered successfully (" . strlen($html) . " bytes)\n";
} catch (\Exception $e) {
    echo "✗ ERROR rendering view:\n";
    echo "  " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
    exit(1);
}

// Step 3: Try sending
echo "\nStep 2: Sending email...\n";
try {
    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrderReceiptMail($order));
    echo "✓ Email sent successfully!\n";
} catch (\Exception $e) {
    echo "✗ ERROR sending email:\n";
    echo "  " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
}

echo "\nDone!\n";
