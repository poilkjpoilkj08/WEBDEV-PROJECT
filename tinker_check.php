<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

// Check logs
echo "=== ORDER & EMAIL DETAILS ===\n";
$order = \App\Models\Order::where('invoice_number', 'BH-20260603124736-353')->first();
$user = $order->user;

echo "Order Invoice: " . $order->invoice_number . "\n";
echo "Order ID: " . $order->id . "\n";
echo "User ID: " . $order->user_id . "\n";
echo "User Name: " . $user->name . "\n";
echo "User Email in DB: " . $user->email . "\n";

echo "\n=== EMAIL CONFIGURATION ===\n";
echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n";

echo "\n=== WHAT TO CHECK ===\n";
echo "Email was SENT FROM: " . config('mail.from.address') . "\n";
echo "Email was SENT TO: " . $user->email . "\n";
echo "\nIn Gmail, check:\n";
echo "  1. Look for emails FROM: " . config('mail.from.address') . "\n";
echo "  2. Check ALL tabs (Primary, Social, Promotions, Updates, Forums)\n";
echo "  3. Check Spam/Junk folder\n";
echo "  4. Search for invoice: BH-20260603124736-353\n";
echo "  5. Search for sender: noreply@deicide.my.id\n";

echo "\n=== CHECKING LARAVEL LOG ===\n";
$logFile = __DIR__ . '/storage/logs/laravel.log';
$lines = file($logFile);
$recentLines = array_slice($lines, -50);

$foundEmail = false;
foreach ($recentLines as $line) {
    if (stripos($line, 'receipt') !== false || stripos($line, 'mail') !== false) {
        echo $line;
        $foundEmail = true;
    }
}

if (!$foundEmail) {
    echo "No email logs found in last 50 lines. Full recent logs:\n";
    foreach ($recentLines as $line) {
        echo $line;
    }
}
