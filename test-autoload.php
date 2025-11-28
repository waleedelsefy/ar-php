<?php
require_once __DIR__ . '/vendor/autoload.php';

echo "Testing class loading...\n\n";

// Test if classes exist
$classes = [
    'ArPHP\Core\Modules\TashkeelService',
    'ArPHP\Core\Modules\TashkeelModule',
    'ArPHP\Core\Modules\NumbersService',
    'ArPHP\Core\Modules\NumbersModule',
];

foreach ($classes as $class) {
    echo "{$class}: " . (class_exists($class) ? '✓ EXISTS' : '✗ NOT FOUND') . "\n";
}

echo "\nTrying to instantiate...\n";
try {
    $service = new \ArPHP\Core\Modules\TashkeelService();
    echo "✓ TashkeelService instantiated!\n";
} catch (\Throwable $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
