<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ArPHP\Core\Arabic;
use ArPHP\Core\AbstractModule;

/**
 * Custom Module Example
 * 
 * This example shows how to create and use a custom module
 */

// Define a simple service
class GreetingService
{
    public function greet(string $name): string
    {
        return "مرحباً {$name}!";
    }
}

// Create custom module
class GreetingModule extends AbstractModule
{
    public function getName(): string
    {
        return 'greeting';
    }
    
    public function register(): void
    {
        Arabic::container()->register(
            'greeting',
            fn() => new GreetingService()
        );
        
        echo "✅ Greeting module registered\n";
    }
    
    public function boot(): void
    {
        echo "🚀 Greeting module booted\n";
    }
}

// Initialize with custom module
Arabic::init([
    new GreetingModule(),
]);

// Use the service
$greeting = Arabic::container()->get('greeting');
echo $greeting->greet('والييد') . "\n";

echo "\n✨ Custom module working perfectly!\n";
