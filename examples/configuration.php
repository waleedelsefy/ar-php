<?php

/**
 * Configuration Example
 * 
 * Shows how to properly configure ArPHP Enhanced with environment variables
 */

// Load environment variables (using vlucas/phpdotenv or similar)
// composer require vlucas/phpdotenv

// Method 1: Using .env file (Recommended)
// if (file_exists(__DIR__ . '/../.env')) {
//     $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
//     $dotenv->load();
// }

// Method 2: Manual Configuration (for this demo)
$_ENV['GEMINI_API_KEY'] = 'your-gemini-key-here';
$_ENV['OPENAI_API_KEY'] = 'your-openai-key-here';
$_ENV['CLAUDE_API_KEY'] = 'your-claude-key-here';

require_once __DIR__ . '/../vendor/autoload.php';

use ArPHP\Core\Arabic;
use ArPHP\Core\AbstractModule;

/**
 * Configuration Service
 */
class Config
{
    private static array $config = [];
    
    public static function get(string $key, mixed $default = null): mixed
    {
        // Try environment variable first
        $envValue = getenv($key);
        if ($envValue !== false) {
            return $envValue;
        }
        
        // Try $_ENV
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        // Try config array
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }
        
        return $default;
    }
    
    public static function set(string $key, mixed $value): void
    {
        self::$config[$key] = $value;
    }
    
    public static function has(string $key): bool
    {
        return getenv($key) !== false 
            || isset($_ENV[$key]) 
            || isset(self::$config[$key]);
    }
}

/**
 * Only load modules you need!
 */
echo "\n" . str_repeat('=', 70) . "\n";
echo "🎯 Modular Configuration Demo\n";
echo str_repeat('=', 70) . "\n\n";

// ==============================================
// Scenario 1: Minimal Setup (No AI)
// ==============================================

echo "1️⃣ Minimal Setup (Core Only):\n";
echo str_repeat('-', 70) . "\n";

Arabic::init();  // No modules = smallest footprint!

echo "✅ Core initialized\n";
echo "📦 Modules: " . count(Arabic::registry()->all()) . "\n";
echo "💾 Memory: " . round(memory_get_usage()/1024) . " KB\n";

// ==============================================
// Scenario 2: Single AI Provider
// ==============================================

echo "\n2️⃣ Single Provider (Gemini Only):\n";
echo str_repeat('-', 70) . "\n";

// Reset for demo
$reflection = new ReflectionClass(Arabic::class);
$registryProperty = $reflection->getProperty('registry');
$registryProperty->setAccessible(true);
$registryProperty->setValue(null, null);

// Load ONLY Gemini
class GeminiModule extends AbstractModule
{
    public function getName(): string { return 'gemini'; }
    
    public function register(): void
    {
        $apiKey = Config::get('GEMINI_API_KEY');
        
        if (!$apiKey || $apiKey === 'your-gemini-key-here') {
            echo "⚠️  No Gemini API key - using demo mode\n";
            $apiKey = 'demo-key';
        }
        
        Arabic::container()->register('gemini', function() use ($apiKey) {
            return (object)['provider' => 'gemini', 'apiKey' => $apiKey];
        });
    }
    
    public function boot(): void
    {
        echo "✅ Gemini ready\n";
    }
}

Arabic::init([new GeminiModule()]);

echo "📦 Modules: " . count(Arabic::registry()->all()) . "\n";
echo "💾 Memory: " . round(memory_get_usage()/1024) . " KB\n";

// ==============================================
// Scenario 3: Custom Module Only
// ==============================================

echo "\n3️⃣ Custom Module (No AI at all):\n";
echo str_repeat('-', 70) . "\n";

// Reset
$registryProperty->setValue(null, null);

class MyCustomModule extends AbstractModule
{
    public function getName(): string { return 'custom'; }
    
    public function register(): void
    {
        Arabic::container()->register('my-service', function() {
            return (object)['name' => 'My Custom Service'];
        });
    }
    
    public function boot(): void
    {
        echo "✅ Custom module ready\n";
    }
}

Arabic::init([new MyCustomModule()]);

$service = Arabic::container()->get('my-service');
echo "📦 Modules: " . count(Arabic::registry()->all()) . "\n";
echo "🔧 Service: {$service->name}\n";
echo "💾 Memory: " . round(memory_get_usage()/1024) . " KB\n";

// ==============================================
// Configuration Best Practices
// ==============================================

echo "\n" . str_repeat('=', 70) . "\n";
echo "📋 Configuration Best Practices\n";
echo str_repeat('=', 70) . "\n\n";

echo "✅ DO:\n";
echo "  • Use .env file for sensitive data (API keys)\n";
echo "  • Load only modules you need\n";
echo "  • Check if API key exists before loading module\n";
echo "  • Use Config::get() with defaults\n";
echo "  • Keep .env out of version control\n\n";

echo "❌ DON'T:\n";
echo "  • Hardcode API keys in code\n";
echo "  • Load all modules if you don't need them\n";
echo "  • Commit .env to git\n";
echo "  • Use production keys in examples\n\n";

echo "📁 File Structure:\n";
echo "  .env.example  → Template (commit this)\n";
echo "  .env          → Your keys (DON'T commit!)\n";
echo "  .gitignore    → Must include .env\n\n";

echo "🔑 Where to put API Keys:\n";
echo "  1. Create .env file from .env.example\n";
echo "  2. Fill in your API keys\n";
echo "  3. Use Config::get() to read them\n";
echo "  4. Never commit .env!\n\n";

// ==============================================
// Conditional Module Loading
// ==============================================

echo str_repeat('=', 70) . "\n";
echo "🔄 Conditional Module Loading\n";
echo str_repeat('=', 70) . "\n\n";

// Reset
$registryProperty->setValue(null, null);

$modules = [];

// Load Gemini only if key exists
if (Config::has('GEMINI_API_KEY')) {
    $modules[] = new GeminiModule();
    echo "✅ Gemini will be loaded\n";
} else {
    echo "⏭️  Gemini skipped (no API key)\n";
}

// Load OpenAI only if key exists
if (Config::has('OPENAI_API_KEY')) {
    // $modules[] = new OpenAIModule();
    echo "✅ OpenAI will be loaded\n";
} else {
    echo "⏭️  OpenAI skipped (no API key)\n";
}

// Always load custom module
$modules[] = new MyCustomModule();
echo "✅ Custom module will be loaded\n";

Arabic::init($modules);

echo "\n📊 Final Stats:\n";
echo "  Modules loaded: " . count(Arabic::registry()->all()) . "\n";
echo "  Memory used: " . round(memory_get_usage()/1024) . " KB\n";

echo "\n✨ Configuration complete!\n";
