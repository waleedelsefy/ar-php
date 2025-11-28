<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ArPHP\Core\Arabic;

/**
 * Basic Usage Example
 * 
 * This example demonstrates the basic initialization of ArPHP Enhanced
 */

// Initialize ArPHP
Arabic::init();

echo "✅ ArPHP Enhanced initialized successfully!\n";

// Get registry
$registry = Arabic::registry();
echo "📦 Modules registered: " . count($registry->all()) . "\n";

// Get container
$container = Arabic::container();
echo "🔧 Service container ready\n";

echo "\n✨ ArPHP Enhanced is ready to use!\n";
