# API Reference

Complete API documentation for ArPHP Enhanced Core.

## Table of Contents

- [ArPHP\Core\Arabic](#arphpcorearabic)
- [ArPHP\Core\ModuleRegistry](#arphpcoremoduleregistry)
- [ArPHP\Core\ServiceContainer](#arphpcoreservicecontainer)
- [ArPHP\Core\AbstractModule](#arphpcoreabstractmodule)
- [Contracts](#contracts)
  - [ModuleInterface](#moduleinterface)
  - [ServiceInterface](#serviceinterface)
- [Exceptions](#exceptions)
  - [ModuleNotFoundException](#modulenotfoundexception)
  - [ServiceNotFoundException](#servicenotfoundexception)
  - [CircularDependencyException](#circulardependencyexception)

---

## ArPHP\Core\Arabic

The main facade class providing unified access to ArPHP Enhanced functionality.

### Namespace

```php
namespace ArPHP\Core;
```

### Methods

#### `init(array $modules = []): void`

Initialize the ArPHP framework with optional modules.

**Parameters:**
- `$modules` (array): Optional array of `ModuleInterface` instances to register

**Returns:** `void`

**Example:**
```php
use ArPHP\Core\Arabic;

// Initialize with no modules
Arabic::init();

// Initialize with modules
Arabic::init([
    new MyModule(),
    new AnotherModule(),
]);
```

**Behavior:**
1. Creates new `ModuleRegistry` instance
2. Creates new `ServiceContainer` instance
3. Registers all provided modules
4. Boots all registered modules in dependency order

---

#### `registry(): ModuleRegistry`

Get the module registry instance.

**Parameters:** None

**Returns:** `ModuleRegistry` - The module registry instance

**Example:**
```php
$registry = Arabic::registry();
$allModules = $registry->all();
```

**Behavior:**
- Initializes framework if not already initialized
- Returns singleton registry instance

---

#### `container(): ServiceContainer`

Get the service container instance.

**Parameters:** None

**Returns:** `ServiceContainer` - The service container instance

**Example:**
```php
$container = Arabic::container();
$service = $container->get('translator');
```

**Behavior:**
- Initializes framework if not already initialized
- Returns singleton container instance

---

#### `__callStatic(string $name, array $arguments): mixed`

Magic method for convenient service access.

**Parameters:**
- `$name` (string): Service identifier
- `$arguments` (array): Method arguments (unused)

**Returns:** `mixed` - The requested service instance

**Example:**
```php
// These are equivalent:
$translator = Arabic::translator();
$translator = Arabic::container()->get('translator');
```

**Throws:**
- `ServiceNotFoundException` if service not found

---

## ArPHP\Core\ModuleRegistry

Central registry managing module lifecycle and dependencies.

### Namespace

```php
namespace ArPHP\Core;
```

### Properties

```php
private array $modules = [];     // Registered modules
private array $booted = [];      // Boot status tracking
private array $loading = [];     // Circular dependency detection
```

### Methods

#### `register(ModuleInterface $module): void`

Register a new module in the registry.

**Parameters:**
- `$module` (ModuleInterface): The module instance to register

**Returns:** `void`

**Example:**
```php
$registry = Arabic::registry();
$registry->register(new MyModule());
```

**Behavior:**
1. Gets module name via `$module->getName()`
2. Checks for duplicate registration
3. Stores module in registry
4. Calls `$module->register()`

**Throws:**
- `RuntimeException` if module already registered

---

#### `boot(string $moduleName): void`

Boot a module with automatic dependency resolution.

**Parameters:**
- `$moduleName` (string): Name of the module to boot

**Returns:** `void`

**Example:**
```php
$registry = Arabic::registry();
$registry->boot('translator');
```

**Behavior:**
1. Returns immediately if already booted
2. Validates module exists
3. Checks for circular dependencies
4. Recursively boots dependencies first
5. Calls `$module->boot()`
6. Marks module as booted

**Throws:**
- `ModuleNotFoundException` if module not registered
- `CircularDependencyException` if circular dependency detected

---

#### `get(string $moduleName): ModuleInterface`

Retrieve a registered module by name.

**Parameters:**
- `$moduleName` (string): Name of the module to retrieve

**Returns:** `ModuleInterface` - The module instance

**Example:**
```php
$registry = Arabic::registry();
$module = $registry->get('translator');
echo $module->getVersion(); // "1.0.0"
```

**Throws:**
- `ModuleNotFoundException` if module not found

---

#### `has(string $moduleName): bool`

Check if a module is registered.

**Parameters:**
- `$moduleName` (string): Name of the module to check

**Returns:** `bool` - True if module exists, false otherwise

**Example:**
```php
$registry = Arabic::registry();

if ($registry->has('translator')) {
    $module = $registry->get('translator');
}
```

---

#### `all(): array`

Get all registered modules.

**Parameters:** None

**Returns:** `array<string, ModuleInterface>` - Associative array of modules

**Example:**
```php
$registry = Arabic::registry();
$modules = $registry->all();

foreach ($modules as $name => $module) {
    echo "{$name}: {$module->getVersion()}\n";
}
```

---

## ArPHP\Core\ServiceContainer

PSR-11 compliant dependency injection container.

### Namespace

```php
namespace ArPHP\Core;
```

### Implements

```php
Psr\Container\ContainerInterface
```

### Properties

```php
private array $services = [];    // Instantiated services
private array $factories = [];   // Service factories
```

### Methods

#### `register(string $id, callable|object $concrete): void`

Register a service in the container.

**Parameters:**
- `$id` (string): Unique service identifier
- `$concrete` (callable|object): Service instance or factory closure

**Returns:** `void`

**Example:**
```php
$container = Arabic::container();

// Register as factory (lazy loading)
$container->register('translator', function($container) {
    return new TranslatorService();
});

// Register as instance (eager loading)
$container->register('config', new ConfigService());
```

**Behavior:**
- If `$concrete` is callable: stores as factory for lazy instantiation
- If `$concrete` is object: stores as ready-to-use service instance

---

#### `get(string $id): mixed`

Retrieve a service from the container (PSR-11).

**Parameters:**
- `$id` (string): Service identifier

**Returns:** `mixed` - The service instance

**Example:**
```php
$container = Arabic::container();
$translator = $container->get('translator');
```

**Behavior:**
1. Returns cached instance if available
2. Calls factory closure if defined (passes container as parameter)
3. Caches result for future calls
4. Returns the service

**Throws:**
- `ServiceNotFoundException` (PSR-11 NotFoundExceptionInterface) if service not found

---

#### `has(string $id): bool`

Check if a service exists in the container (PSR-11).

**Parameters:**
- `$id` (string): Service identifier

**Returns:** `bool` - True if service is registered, false otherwise

**Example:**
```php
$container = Arabic::container();

if ($container->has('translator')) {
    $translator = $container->get('translator');
} else {
    echo "Translator service not available";
}
```

---

## ArPHP\Core\AbstractModule

Abstract base class for creating custom modules using the Template Method pattern.

### Namespace

```php
namespace ArPHP\Core;
```

### Implements

```php
ArPHP\Core\Contracts\ModuleInterface
```

### Properties

```php
protected bool $enabled = true;
protected array $dependencies = [];
protected string $version = '1.0.0';
```

### Methods

#### `abstract getName(): string`

Get the unique module name. Must be implemented by child classes.

**Parameters:** None

**Returns:** `string` - Unique module identifier

**Example:**
```php
class MyModule extends AbstractModule
{
    public function getName(): string
    {
        return 'my-module';
    }
}
```

---

#### `getVersion(): string`

Get the module version.

**Parameters:** None

**Returns:** `string` - Module version (default: "1.0.0")

**Example:**
```php
class MyModule extends AbstractModule
{
    protected string $version = '2.1.0';
    
    // getVersion() will return '2.1.0'
}
```

---

#### `getDependencies(): array`

Get module dependencies.

**Parameters:** None

**Returns:** `array<string>` - Array of required module names

**Example:**
```php
class MyModule extends AbstractModule
{
    protected array $dependencies = ['core', 'translator'];
    
    // getDependencies() will return ['core', 'translator']
}
```

---

#### `isEnabled(): bool`

Check if module is enabled.

**Parameters:** None

**Returns:** `bool` - True if enabled, false otherwise

**Example:**
```php
$module = new MyModule();
if ($module->isEnabled()) {
    echo "Module is active";
}
```

---

#### `enable(): void`

Enable the module.

**Parameters:** None

**Returns:** `void`

**Example:**
```php
$module = new MyModule();
$module->disable();
$module->enable();
echo $module->isEnabled(); // true
```

---

#### `disable(): void`

Disable the module.

**Parameters:** None

**Returns:** `void`

**Example:**
```php
$module = new MyModule();
$module->disable();
echo $module->isEnabled(); // false
```

---

#### `register(): void`

Register module services. Override in child class.

**Parameters:** None

**Returns:** `void`

**Default Behavior:** No-op (empty implementation)

**Example:**
```php
class MyModule extends AbstractModule
{
    public function register(): void
    {
        Arabic::container()->register('myservice', function() {
            return new MyService();
        });
    }
}
```

---

#### `boot(): void`

Boot the module. Override in child class if needed.

**Parameters:** None

**Returns:** `void`

**Default Behavior:** No-op (empty implementation)

**Example:**
```php
class MyModule extends AbstractModule
{
    public function boot(): void
    {
        $service = Arabic::container()->get('myservice');
        $service->initialize();
    }
}
```

---

## Contracts

### ModuleInterface

Interface that all modules must implement.

#### Namespace

```php
namespace ArPHP\Core\Contracts;
```

#### Methods

##### `getName(): string`

Get the unique module name.

**Returns:** `string` - Module identifier

---

##### `getVersion(): string`

Get the module version.

**Returns:** `string` - Version string (e.g., "1.0.0")

---

##### `register(): void`

Register module services in the container.

**Returns:** `void`

---

##### `boot(): void`

Boot the module after dependencies are resolved.

**Returns:** `void`

---

##### `getDependencies(): array`

Get list of required module names.

**Returns:** `array<string>` - Module dependencies

---

##### `isEnabled(): bool`

Check if module is enabled.

**Returns:** `bool` - Enabled status

---

### ServiceInterface

Interface for service implementations.

#### Namespace

```php
namespace ArPHP\Core\Contracts;
```

#### Methods

##### `getServiceName(): string`

Get the service name.

**Returns:** `string` - Service identifier

---

##### `getConfig(): array`

Get the service configuration.

**Returns:** `array<string, mixed>` - Configuration array

---

##### `isAvailable(): bool`

Check if service is available.

**Returns:** `bool` - True if service can be used

---

## Exceptions

### ModuleNotFoundException

Exception thrown when a module is not found.

#### Namespace

```php
namespace ArPHP\Core\Exceptions;
```

#### Extends

```php
RuntimeException
```

#### Constructor

```php
public function __construct(
    string $moduleName,
    int $code = 0,
    ?\Throwable $previous = null
)
```

**Parameters:**
- `$moduleName` (string): Name of the missing module
- `$code` (int): Exception code (default: 0)
- `$previous` (?Throwable): Previous exception (default: null)

**Message Format:** `"Module '{$moduleName}' not found. Make sure it's registered."`

**Example:**
```php
use ArPHP\Core\Exceptions\ModuleNotFoundException;

try {
    $module = Arabic::registry()->get('nonexistent');
} catch (ModuleNotFoundException $e) {
    echo $e->getMessage();
    // Output: Module 'nonexistent' not found. Make sure it's registered.
}
```

---

### ServiceNotFoundException

Exception thrown when a service is not found. Implements PSR-11.

#### Namespace

```php
namespace ArPHP\Core\Exceptions;
```

#### Extends

```php
RuntimeException
```

#### Implements

```php
Psr\Container\NotFoundExceptionInterface
```

#### Constructor

```php
public function __construct(
    string $serviceId,
    int $code = 0,
    ?\Throwable $previous = null
)
```

**Parameters:**
- `$serviceId` (string): Service identifier
- `$code` (int): Exception code (default: 0)
- `$previous` (?Throwable): Previous exception (default: null)

**Message Format:** `"Service '{$serviceId}' not found in container."`

**Example:**
```php
use ArPHP\Core\Exceptions\ServiceNotFoundException;

try {
    $service = Arabic::container()->get('nonexistent');
} catch (ServiceNotFoundException $e) {
    echo $e->getMessage();
    // Output: Service 'nonexistent' not found in container.
}
```

---

### CircularDependencyException

Exception thrown when circular dependencies are detected.

#### Namespace

```php
namespace ArPHP\Core\Exceptions;
```

#### Extends

```php
RuntimeException
```

#### Constructor

```php
public function __construct(
    string $message = "Circular dependency detected",
    int $code = 0,
    ?\Throwable $previous = null
)
```

**Parameters:**
- `$message` (string): Error message
- `$code` (int): Exception code (default: 0)
- `$previous` (?Throwable): Previous exception (default: null)

---

#### Static Factory Method

##### `fromChain(array $chain): self`

Create exception from dependency chain.

**Parameters:**
- `$chain` (array<string>): Array of module names showing circular dependency

**Returns:** `CircularDependencyException` - New exception instance

**Example:**
```php
use ArPHP\Core\Exceptions\CircularDependencyException;

try {
    // Module A depends on B, B depends on A
    Arabic::init([
        new ModuleA(), // depends on 'module-b'
        new ModuleB(), // depends on 'module-a'
    ]);
} catch (CircularDependencyException $e) {
    echo $e->getMessage();
    // Output: Circular dependency: module-a -> module-b -> module-a
}
```

---

## Usage Examples

### Complete Module Example

```php
<?php

namespace MyApp\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;
use ArPHP\Core\Contracts\ServiceInterface;

/**
 * Translation service implementation
 */
class TranslationService implements ServiceInterface
{
    private array $translations = [];
    
    public function translate(string $key): string
    {
        return $this->translations[$key] ?? $key;
    }
    
    public function addTranslation(string $key, string $value): void
    {
        $this->translations[$key] = $value;
    }
    
    public function getServiceName(): string
    {
        return 'translation';
    }
    
    public function getConfig(): array
    {
        return ['count' => count($this->translations)];
    }
    
    public function isAvailable(): bool
    {
        return true;
    }
}

/**
 * Translation module
 */
class TranslationModule extends AbstractModule
{
    protected string $version = '1.0.0';
    
    public function getName(): string
    {
        return 'translation';
    }
    
    public function register(): void
    {
        Arabic::container()->register('translation', function() {
            return new TranslationService();
        });
    }
    
    public function boot(): void
    {
        $service = Arabic::container()->get('translation');
        $service->addTranslation('hello', 'مرحباً');
    }
}

// Usage
Arabic::init([new TranslationModule()]);
$translator = Arabic::container()->get('translation');
echo $translator->translate('hello'); // مرحباً
```

---

## PSR Compliance

### PSR-4: Autoloading

All classes follow PSR-4 autoloading standards:

```
ArPHP\Core\Arabic              → packages/core/src/Arabic.php
ArPHP\Core\ModuleRegistry      → packages/core/src/ModuleRegistry.php
ArPHP\Core\Contracts\*         → packages/core/src/Contracts/*.php
```

### PSR-11: Container Interface

`ServiceContainer` implements `Psr\Container\ContainerInterface`:

- ✅ `get(string $id): mixed`
- ✅ `has(string $id): bool`
- ✅ Throws `NotFoundExceptionInterface` on missing service

### PSR-12: Coding Style

All code follows PSR-12 coding standards:

- ✅ Strict types declaration
- ✅ Type hints for parameters and return values
- ✅ Proper indentation and formatting
- ✅ DocBlock comments

---

**Last Updated**: November 28, 2025  
**Version**: 1.0.0

