# ArPHP Enhanced - Architecture Documentation

## Overview

ArPHP Enhanced is built on a **modern modular architecture** that prioritizes flexibility, maintainability, and performance. The core framework provides the foundation for building and managing independent, reusable modules that can be easily composed to create powerful Arabic text processing applications.

## Design Principles

1. **Modularity**: Each feature is encapsulated in independent modules
2. **Dependency Injection**: Loose coupling through PSR-11 container
3. **Lazy Loading**: Services and modules are loaded only when needed
4. **Type Safety**: Full PHP 8.1+ type hints and strict types
5. **Standards Compliance**: Follows PSR-11, PSR-12, and PSR-4

## Core Components

### 1. Module System

The module system is the heart of ArPHP Enhanced, enabling a plugin-like architecture.

#### ModuleInterface

Every module must implement `ArPHP\Core\Contracts\ModuleInterface`:

```php
interface ModuleInterface
{
    public function getName(): string;
    public function getVersion(): string;
    public function register(): void;
    public function boot(): void;
    public function getDependencies(): array;
    public function isEnabled(): bool;
}
```

#### AbstractModule

The `AbstractModule` class provides a template method implementation:

- **Template Method Pattern**: Defines the skeleton of module initialization
- **Hook Methods**: `register()` and `boot()` can be overridden
- **Default Implementations**: Provides sensible defaults for common functionality

### 2. Module Registry (Registry Pattern)

The `ModuleRegistry` manages the lifecycle of all modules:

```
┌─────────────────────────────────────────┐
│         ModuleRegistry                  │
├─────────────────────────────────────────┤
│ - modules: ModuleInterface[]            │
│ - booted: bool[]                        │
│ - loading: string[]                     │
├─────────────────────────────────────────┤
│ + register(module): void                │
│ + boot(name): void                      │
│ + get(name): ModuleInterface            │
│ + has(name): bool                       │
│ + all(): ModuleInterface[]              │
└─────────────────────────────────────────┘
```

**Key Features:**

- **Central Registration**: Single source of truth for all modules
- **Dependency Resolution**: Automatically boots dependencies first
- **Circular Dependency Detection**: Prevents infinite loops
- **Singleton Pattern**: Each module is registered once

### 3. Service Container (PSR-11 DI Container)

The `ServiceContainer` implements PSR-11 `ContainerInterface`:

```
┌─────────────────────────────────────────┐
│       ServiceContainer                  │
│   (implements ContainerInterface)       │
├─────────────────────────────────────────┤
│ - services: mixed[]                     │
│ - factories: callable[]                 │
├─────────────────────────────────────────┤
│ + register(id, concrete): void          │
│ + get(id): mixed                        │
│ + has(id): bool                         │
└─────────────────────────────────────────┘
```

**Key Features:**

- **Lazy Instantiation**: Services created only when first requested
- **Singleton by Default**: Each service is instantiated once
- **Factory Support**: Closures can be registered as factories
- **PSR-11 Compliant**: Standard container interface

### 4. Arabic Facade (Facade Pattern)

The `Arabic` class provides a unified, static interface:

```php
Arabic::init();                    // Initialize framework
$registry = Arabic::registry();    // Get module registry
$container = Arabic::container();  // Get service container
$service = Arabic::serviceName();  // Magic method access
```

**Benefits:**

- **Simplified API**: Clean, intuitive interface
- **Global Access**: Available throughout the application
- **Lazy Initialization**: Components created on first use

## Module Lifecycle

```
┌─────────────┐
│   Module    │
│  Creation   │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  register() │ ◄─── Register services in container
└──────┬──────┘
       │
       ▼
┌─────────────┐
│    boot()   │ ◄─── Initialize module, configure services
└──────┬──────┘      Dependencies are booted first
       │
       ▼
┌─────────────┐
│   Active    │ ◄─── Module ready for use
└─────────────┘
```

### Phase 1: Registration

```php
public function register(): void
{
    // Register services in the container
    Arabic::container()->register('myService', function() {
        return new MyService();
    });
}
```

- Services are registered but NOT instantiated
- No dependencies on other modules
- Fast, lightweight operation

### Phase 2: Booting

```php
public function boot(): void
{
    // Configure services, set up event listeners, etc.
    $service = Arabic::container()->get('myService');
    $service->configure(['option' => 'value']);
}
```

- Dependencies are guaranteed to be booted
- Services can be retrieved and configured
- Module is now active and ready

## Dependency Resolution

The module registry implements **topological sorting** for dependency resolution:

```
Module A (depends on: B, C)
Module B (depends on: C)
Module C (no dependencies)

Boot Order: C → B → A
```

### Circular Dependency Detection

```
Module A → Module B
Module B → Module C
Module C → Module A  ✗ CIRCULAR!

Throws: CircularDependencyException
Message: "Circular dependency: A -> B -> C -> A"
```

## Design Patterns Used

### 1. Registry Pattern (ModuleRegistry)

- **Purpose**: Centralized module management
- **Benefits**: Single lookup point, prevents duplicates
- **Implementation**: Array-based storage with string keys

### 2. Dependency Injection (ServiceContainer)

- **Purpose**: Loose coupling, testability
- **Benefits**: Easier testing, flexible configurations
- **Implementation**: PSR-11 compliant container

### 3. Facade Pattern (Arabic)

- **Purpose**: Simplified API
- **Benefits**: Clean interface, hides complexity
- **Implementation**: Static methods with lazy initialization

### 4. Factory Pattern (ServiceContainer factories)

- **Purpose**: Deferred object creation
- **Benefits**: Lazy loading, memory efficiency
- **Implementation**: Callable/Closure registration

### 5. Template Method Pattern (AbstractModule)

- **Purpose**: Define algorithm skeleton
- **Benefits**: Code reuse, consistent behavior
- **Implementation**: Abstract class with hook methods

### 6. Singleton Pattern (Services)

- **Purpose**: Single instance per service
- **Benefits**: Memory efficiency, shared state
- **Implementation**: Container caches instances

## Performance Considerations

### 1. Lazy Loading

**Modules**: Only booted when first accessed or when dependencies require them

**Services**: Only instantiated when first retrieved from container

**Impact**: Significantly reduces initialization time and memory usage

### 2. Singleton Services

Services are instantiated once and reused:

```php
$service1 = Arabic::container()->get('translator');
$service2 = Arabic::container()->get('translator');

// $service1 === $service2 (same instance)
```

**Benefits**:
- Reduced memory footprint
- Faster subsequent access
- Shared state when needed

### 3. Dependency Caching

The module registry tracks booted modules to avoid re-initialization:

```php
private array $booted = [];

public function boot(string $moduleName): void
{
    if (isset($this->booted[$moduleName])) {
        return; // Already booted, skip
    }
    // ... boot logic
}
```

### 4. Type Optimization

**PHP 8.1+ Features**:
- Union types for flexible APIs
- Readonly properties (future enhancement)
- Enums for constants (future enhancement)

**Strict Types**: `declare(strict_types=1)` prevents type coercion overhead

## Error Handling

### Exception Hierarchy

```
RuntimeException
├── ModuleNotFoundException
├── CircularDependencyException
└── ServiceNotFoundException (implements NotFoundExceptionInterface)
```

### Exception Usage

**ModuleNotFoundException**: Thrown when accessing non-existent module

**CircularDependencyException**: Thrown during boot when circular dependencies detected

**ServiceNotFoundException**: Thrown when accessing non-existent service (PSR-11)

## Extension Points

### Creating Custom Modules

```php
class MyModule extends AbstractModule
{
    public function getName(): string
    {
        return 'my-module';
    }
    
    protected array $dependencies = ['core-module'];
    
    public function register(): void
    {
        // Register services
    }
    
    public function boot(): void
    {
        // Initialize module
    }
}
```

### Creating Custom Services

```php
class MyService implements ServiceInterface
{
    public function getServiceName(): string
    {
        return 'my-service';
    }
    
    public function getConfig(): array
    {
        return ['option' => 'value'];
    }
    
    public function isAvailable(): bool
    {
        return true;
    }
}
```

## Testing Strategy

### Unit Testing

- **Module Registry**: Test registration, booting, dependency resolution
- **Service Container**: Test PSR-11 compliance, singleton behavior
- **Individual Modules**: Mock dependencies, test in isolation

### Integration Testing

- **Full Lifecycle**: Test complete initialization chain
- **Multiple Modules**: Test module interactions
- **Real Dependencies**: Test actual dependency resolution

## Future Enhancements

1. **Event System**: Module-to-module communication
2. **Configuration Management**: Centralized config system
3. **Plugin Discovery**: Auto-discovery of modules
4. **Performance Monitoring**: Built-in profiling tools
5. **Caching Layer**: Advanced caching strategies

## Conclusion

ArPHP Enhanced's architecture provides a solid foundation for building maintainable, scalable Arabic text processing applications. The modular design allows for easy extension and customization while maintaining clean separation of concerns.

---

**Last Updated**: November 28, 2025  
**Version**: 1.0.0

