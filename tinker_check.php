<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

// Check mail configuration first
echo "=== MAIL CONFIGURATION ===\n";
echo "MAIL_MAILER: " . config('mail.mailer') . "\n";
echo "From: " . config('mail.from.address') . "\n";
echo "SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
echo "SMTP Port: " . config('mail.mailers.smtp.port') . "\n";

// Find order
$order = \App\Models\Order::where('invoice_number', 'BH-20260603124736-353')->first();
if (!$order) {
    echo "Order not found!\n";
    exit(1);
}

$user = $order->user;
echo "\n=== SENDING EMAIL ===\n";
echo "To: " . $user->email . "\n";

$logFile = __DIR__ . '/storage/logs/laravel.log';
$sizeBefore = filesize($logFile);
$timeBefore = filemtime($logFile);

try {
    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrderReceiptMail($order));
    echo "✓ Send command completed without exception\n";
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

sleep(2);

$sizeAfter = filesize($logFile);
$timeAfter = filemtime($logFile);

echo "\n=== LOG FILE STATUS ===\n";
echo "File: $logFile\n";
echo "Size before: $sizeBefore bytes\n";
echo "Size after: $sizeAfter bytes\n";
echo "Changed: " . ($sizeAfter > $sizeBefore ? "YES (+" . ($sizeAfter - $sizeBefore) . " bytes)" : "NO") . "\n";
echo "Time modified: " . date('Y-m-d H:i:s', $timeAfter) . "\n";

echo "\n=== LAST 50 LINES OF LOG ===\n";
echo str_repeat("=", 80) . "\n";
$lines = file($logFile);
$recentLines = array_slice($lines, -50);
foreach ($recentLines as $line) {
    echo $line;
}
echo str_repeat("=", 80) . "\n";

echo "\nIf log file size didn't change:\n";
echo "  1. Mail driver might be 'smtp' (no local logs)\n";
echo "  2. Check if log file permissions allow writing\n";
echo "  3. Check APP_ENV setting\n";
