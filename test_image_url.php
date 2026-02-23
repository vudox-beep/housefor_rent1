<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the imageUrl function
echo "Testing imageUrl function:\n";

// Test with a sample image path
$testPath = 'test-image.jpg';
$result = imageUrl($testPath);

echo "Input: {$testPath}\n";
echo "Output: {$result}\n\n";

// Check environment variables
echo "Environment variables:\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_URL: " . env('APP_URL') . "\n";
echo "AWS_BUCKET: " . (env('AWS_BUCKET') ?: 'NOT_SET') . "\n";
echo "AWS_ENDPOINT: " . (env('AWS_ENDPOINT') ?: 'NOT_SET') . "\n";
echo "AWS_URL: " . (env('AWS_URL') ?: 'NOT_SET') . "\n\n";

// Test if we're detecting Laravel Cloud correctly
$isProduction = env('APP_ENV') === 'production';
$isLaravelCloud = str_contains(env('APP_URL', ''), '.laravel.cloud');

echo "Detection results:\n";
echo "Is Production: " . ($isProduction ? 'YES' : 'NO') . "\n";
echo "Is Laravel Cloud: " . ($isLaravelCloud ? 'YES' : 'NO') . "\n";

?>