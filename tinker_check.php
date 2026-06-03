<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

// Check logs
echo "=== CHECKING LARAVEL LOGS ===\n";
$logFile = __DIR__ . '/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "Log file not found: $logFile\n";
    exit(1);
}

$lines = file($logFile);
$recentLines = array_slice($lines, -100);

echo "Last 100 lines of laravel.log:\n";
echo str_repeat("=", 80) . "\n";
foreach ($recentLines as $line) {
    echo $line;
}
echo str_repeat("=", 80) . "\n";

// Check mail config
echo "\n=== MAIL CONFIGURATION ===\n";
echo "MAIL_MAILER: " . config('mail.mailer') . "\n";
echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n";
echo "SMTP HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "SMTP PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "SMTP ENCRYPTION: " . config('mail.mailers.smtp.encryption') . "\n";
echo "\n⚠️ Check your Gmail:\n";
echo "  1. Spam/Junk folder\n";
echo "  2. All Mail folder\n";
echo "  3. Promotions tab\n";
echo "  4. Other tabs at top of inbox\n";
