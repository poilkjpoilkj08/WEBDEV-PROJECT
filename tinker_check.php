<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

// Find order and send email
echo "=== SENDING TEST EMAIL ===\n";
$order = \App\Models\Order::where('invoice_number', 'BH-20260603124736-353')->first();

if (!$order) {
    echo "Order not found!\n";
    exit(1);
}

$user = $order->user;
echo "Sending email to: " . $user->email . "\n";
echo "From: " . config('mail.from.address') . "\n";

try {
    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrderReceiptMail($order));
    echo "✓ Email sent successfully!\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Wait a moment for log to be written
sleep(1);

echo "\n=== CHECKING LATEST LOGS ===\n";
echo str_repeat("=", 80) . "\n";

$logFile = __DIR__ . '/storage/logs/laravel.log';
$lines = file($logFile);
$recentLines = array_slice($lines, -100);  // Last 100 lines

foreach ($recentLines as $line) {
    echo $line;
}

echo str_repeat("=", 80) . "\n";
