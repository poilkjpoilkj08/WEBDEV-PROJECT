<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Minimal request
$request = \Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

$logFile = __DIR__ . '/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "Log file not found: $logFile\n";
    exit(1);
}

echo "=== LATEST LARAVEL LOG ===\n";
echo str_repeat("=", 80) . "\n";

$lines = file($logFile);
$recentLines = array_slice($lines, -200);  // Last 200 lines

foreach ($recentLines as $line) {
    echo $line;
}

echo str_repeat("=", 80) . "\n";
