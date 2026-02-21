<?php
require __DIR__ . '/vendor/autoload.php';

try {
    if (class_exists('Laravel\Socialite\Facades\Socialite')) {
        echo "Facade found.\n";
    } else {
        echo "Facade NOT found.\n";
    }

    if (class_exists('Laravel\Socialite\Socialite')) {
        echo "Socialite class found.\n";
    } else {
        echo "Socialite class NOT found.\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
