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
    exit(0);
}

$user = $order->user;
echo "✓ Order found!\n";
echo "  User Email: " . $user->email . "\n";
echo "  Google ID: " . $user->google_id . "\n";

echo "\n=== TESTING EMAIL SENDING ===\n";
echo "Mail driver: " . config('mail.mailer') . "\n";
echo "SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
echo "SMTP Port: " . config('mail.mailers.smtp.port') . "\n";

try {
    echo "\nAttempting to send test email...\n";
    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrderReceiptMail($order));
    echo "✓ Email sent successfully!\n";
    echo "\nCheck logs for confirmation:\n";
    echo "  tail -50 storage/logs/laravel.log | grep -i receipt\n";
} catch (\Exception $e) {
    echo "✗ Error sending email:\n";
    echo "  " . $e->getMessage() . "\n";
    echo "\nFull error:\n";
    echo $e . "\n";
}
