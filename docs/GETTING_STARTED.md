# Getting Started with ArPHP Enhanced

Welcome to ArPHP Enhanced! This guide will help you get up and running quickly with the modern modular Arabic text processing library.

## Table of Contents

1. [Installation](#installation)
2. [Quick Start](#quick-start)
3. [Basic Usage](#basic-usage)
4. [Creating Custom Modules](#creating-custom-modules)
5. [Working with Services](#working-with-services)
6. [Error Handling](#error-handling)
7. [Configuration](#configuration)
8. [Next Steps](#next-steps)

## Installation

### Requirements

- PHP 8.1 or higher
- Composer
- ext-mbstring extension

### Install via Composer

```bash
composer require arphp/core
```

### Verify Installation

```php
<?php

require_once 'vendor/autoload.php';

use ArPHP\Core\Arabic;

Arabic::init();
echo "✅ ArPHP Enhanced is installed and ready!";
```

## Quick Start

### 30-Second Example

```php
<?php

require_once 'vendor/autoload.php';

use ArPHP\Core\Arabic;

// Initialize ArPHP
Arabic::init();

// Your Arabic text processing starts here!
echo "🚀 ArPHP Enhanced is ready!";
```

## Basic Usage

### Initializing ArPHP

The simplest way to start using ArPHP Enhanced:

```php
<?php

use ArPHP\Core\Arabic;

// Initialize with default configuration
Arabic::init();

// Access the module registry
$registry = Arabic::registry();
echo "Modules loaded: " . count($registry->all());

// Access the service container
$container = Arabic::container();
echo "Container ready: " . ($container->has('any-service') ? 'Yes' : 'No');
```

### Initializing with Modules

You can register modules during initialization:

```php
<?php

use ArPHP\Core\Arabic;

// Initialize with custom modules
Arabic::init([
    new MyCustomModule(),
    new AnotherModule(),
]);

// All modules are registered and booted automatically
```

### Working with the Registry

The module registry manages all modules in your application:

```php
<?php

use ArPHP\Core\Arabic;
use ArPHP\Core\ModuleRegistry;

$registry = Arabic::registry();

// Check if a module is registered
if ($registry->has('transliteration')) {
    echo "Transliteration module is available";
}

// Get a specific module
$module = $registry->get('transliteration');
echo "Module version: " . $module->getVersion();

// Get all registered modules
$allModules = $registry->all();
foreach ($allModules as $name => $module) {
    echo "{$name}: {$module->getVersion()}\n";
}
```

### Working with the Container

The service container provides access to all registered services:

```php
<?php

use ArPHP\Core\Arabic;

$container = Arabic::container();

// Check if a service exists
if ($container->has('translator')) {
    // Get the service
    $translator = $container->get('translator');
    
    // Use the service
    $result = $translator->translate('hello');
}
```

## Creating Custom Modules

### Step 1: Create Your Module Class

```php
<?php

namespace MyApp\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;

class GreetingModule extends AbstractModule
{
    /**
     * Return a unique module name
     */
    public function getName(): string
    {
        return 'greeting';
    }
    
    /**
     * Register your services in the container
     * This method is called first, before boot()
     */
    public function register(): void
    {
        Arabic::container()->register('greeting', function() {
            return new GreetingService();
        });
    }
    
    /**
     * Boot your module
     * This method is called after register()
     * All dependencies are guaranteed to be booted here
     */
    public function boot(): void
    {
        // Optional: Initialize your module
        // Configure services, set up state, etc.
    }
}
```

### Step 2: Create Your Service Class

```php
<?php

namespace MyApp\Services;

use ArPHP\Core\Contracts\ServiceInterface;

class GreetingService implements ServiceInterface
{
    private array $config = [];
    
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }
    
    public function greet(string $name): string
    {
        return "مرحباً {$name}!";
    }
    
    public function getServiceName(): string
    {
        return 'greeting';
    }
    
    public function getConfig(): array
    {
        return $this->config;
    }
    
    public function isAvailable(): bool
    {
        return true;
    }
}
```

### Step 3: Register and Use Your Module

```php
<?php

use ArPHP\Core\Arabic;
use MyApp\Modules\GreetingModule;

// Initialize with your custom module
Arabic::init([
    new GreetingModule(),
]);

// Use your service
$greetingService = Arabic::container()->get('greeting');
echo $greetingService->greet('أحمد'); // Output: مرحباً أحمد!
```

## Working with Services

### Registering Services

There are two ways to register services:

#### 1. Register as Factory (Lazy Loading)

```php
<?php

use ArPHP\Core\Arabic;

Arabic::container()->register('translator', function($container) {
    // This closure is called only when the service is first requested
    return new TranslatorService([
        'api_key' => 'your-api-key',
        'timeout' => 30,
    ]);
});
```

#### 2. Register as Instance (Eager Loading)

```php
<?php

use ArPHP\Core\Arabic;

$service = new TranslatorService();
Arabic::container()->register('translator', $service);
```

### Retrieving Services

```php
<?php

use ArPHP\Core\Arabic;

// Get a service
$translator = Arabic::container()->get('translator');

// Services are singletons - same instance every time
$translator1 = Arabic::container()->get('translator');
$translator2 = Arabic::container()->get('translator');
// $translator1 === $translator2 ✅
```

### Magic Method Access (Optional)

```php
<?php

use ArPHP\Core\Arabic;

// Using magic method (calls container->get())
$translator = Arabic::translator();

// Equivalent to:
$translator = Arabic::container()->get('translator');
```

## Error Handling

### Handling Missing Modules

```php
<?php

use ArPHP\Core\Arabic;
use ArPHP\Core\Exceptions\ModuleNotFoundException;

try {
    $module = Arabic::registry()->get('non-existent');
} catch (ModuleNotFoundException $e) {
    echo "Error: " . $e->getMessage();
    // Output: Module 'non-existent' not found. Make sure it's registered.
}
```

### Handling Missing Services

```php
<?php

use ArPHP\Core\Arabic;
use ArPHP\Core\Exceptions\ServiceNotFoundException;

try {
    $service = Arabic::container()->get('non-existent');
} catch (ServiceNotFoundException $e) {
    echo "Error: " . $e->getMessage();
    // Output: Service 'non-existent' not found in container.
}
```

### Handling Circular Dependencies

```php
<?php

use ArPHP\Core\Arabic;
use ArPHP\Core\Exceptions\CircularDependencyException;

// Module A depends on B
// Module B depends on A
// This creates a circular dependency!

try {
    Arabic::init([
        new ModuleA(), // depends on module-b
        new ModuleB(), // depends on module-a
    ]);
} catch (CircularDependencyException $e) {
    echo "Error: " . $e->getMessage();
    // Output: Circular dependency: module-a -> module-b -> module-a
}
```

### Best Practices for Error Handling

```php
<?php

use ArPHP\Core\Arabic;
use ArPHP\Core\Exceptions\ServiceNotFoundException;

function getService(string $name, $default = null)
{
    try {
        return Arabic::container()->get($name);
    } catch (ServiceNotFoundException $e) {
        // Log the error
        error_log("Service not found: {$name}");
        
        // Return default or rethrow
        if ($default !== null) {
            return $default;
        }
        throw $e;
    }
}

// Usage
$translator = getService('translator', new DefaultTranslator());
```

## Configuration

### Module Configuration

```php
<?php

namespace MyApp\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;

class ConfigurableModule extends AbstractModule
{
    private array $config;
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'timeout' => 30,
            'retries' => 3,
            'debug' => false,
        ], $config);
    }
    
    public function getName(): string
    {
        return 'configurable';
    }
    
    public function register(): void
    {
        Arabic::container()->register('myservice', function() {
            return new MyService($this->config);
        });
    }
}

// Usage
Arabic::init([
    new ConfigurableModule([
        'timeout' => 60,
        'debug' => true,
    ]),
]);
```

### Module Dependencies

Declare dependencies to ensure proper boot order:

```php
<?php

namespace MyApp\Modules;

use ArPHP\Core\AbstractModule;

class DependentModule extends AbstractModule
{
    protected array $dependencies = [
        'core',
        'translator',
    ];
    
    public function getName(): string
    {
        return 'dependent';
    }
    
    public function boot(): void
    {
        // Dependencies are guaranteed to be booted here
        $translator = Arabic::container()->get('translator');
        $translator->configure(['locale' => 'ar']);
    }
}
```

## Complete Example: Translation Module

Here's a complete example showing all concepts together:

```php
<?php

namespace MyApp\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;
use ArPHP\Core\Contracts\ServiceInterface;

// Service Implementation
class TranslationService implements ServiceInterface
{
    private array $translations = [];
    
    public function __construct(array $config = [])
    {
        $this->translations = $config['translations'] ?? [];
    }
    
    public function translate(string $key, string $locale = 'ar'): string
    {
        return $this->translations[$locale][$key] ?? $key;
    }
    
    public function addTranslation(string $locale, string $key, string $value): void
    {
        $this->translations[$locale][$key] = $value;
    }
    
    public function getServiceName(): string
    {
        return 'translation';
    }
    
    public function getConfig(): array
    {
        return ['translations' => $this->translations];
    }
    
    public function isAvailable(): bool
    {
        return !empty($this->translations);
    }
}

// Module Implementation
class TranslationModule extends AbstractModule
{
    private array $config;
    
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->version = '1.0.0';
    }
    
    public function getName(): string
    {
        return 'translation';
    }
    
    public function register(): void
    {
        Arabic::container()->register('translation', function() {
            return new TranslationService($this->config);
        });
    }
    
    public function boot(): void
    {
        $service = Arabic::container()->get('translation');
        
        // Add default translations
        $service->addTranslation('ar', 'welcome', 'مرحباً');
        $service->addTranslation('en', 'welcome', 'Welcome');
    }
}

// Usage
require_once 'vendor/autoload.php';

use ArPHP\Core\Arabic;
use MyApp\Modules\TranslationModule;

// Initialize with translation module
Arabic::init([
    new TranslationModule([
        'translations' => [
            'ar' => ['hello' => 'مرحباً'],
            'en' => ['hello' => 'Hello'],
        ],
    ]),
]);

// Use the translation service
$translator = Arabic::container()->get('translation');
echo $translator->translate('welcome', 'ar'); // Output: مرحباً
echo $translator->translate('hello', 'en');   // Output: Hello
```

## Next Steps

### Learn More

- Read the [Architecture Documentation](ARCHITECTURE.md) to understand the design
- Check the [API Reference](API_REFERENCE.md) for detailed method documentation
- Explore the examples in the `examples/` directory

### Build Your First Module

1. Create a new module class extending `AbstractModule`
2. Implement the `getName()` method
3. Register services in `register()`
4. Initialize in `boot()`
5. Add to your `Arabic::init()` call

### Join the Community

- Report bugs and request features on GitHub
- Contribute to the project
- Share your custom modules

## Troubleshooting

### Common Issues

**Issue**: "Module not found" error
**Solution**: Make sure the module is registered with `Arabic::init([new YourModule()])`

**Issue**: "Service not found" error
**Solution**: Check that the service is registered in the module's `register()` method

**Issue**: "Circular dependency" error
**Solution**: Review module dependencies and break the circular chain

**Issue**: Service returns null or unexpected value
**Solution**: Verify the service factory closure returns the correct instance

### Getting Help

- Check the documentation in the `docs/` folder
- Review example files in the `examples/` folder
- Open an issue on GitHub

---

**Last Updated**: November 28, 2025  
**Version**: 1.0.0

Happy coding! 🚀

