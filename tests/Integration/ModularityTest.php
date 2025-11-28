<?php

declare(strict_types=1);

namespace ArPHP\Tests\Integration;

use ArPHP\Core\Arabic;
use ArPHP\Core\AbstractModule;
use ArPHP\Core\Contracts\ServiceInterface;
use ArPHP\Core\Exceptions\CircularDependencyException;
use ArPHP\Core\Exceptions\ModuleNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for modular architecture
 *
 * Tests the full module lifecycle and interaction between components
 *
 * @package ArPHP\Tests\Integration
 */
class ModularityTest extends TestCase
{
    protected function tearDown(): void
    {
        // Reset static state between tests
        $reflection = new \ReflectionClass(Arabic::class);
        $registryProperty = $reflection->getProperty('registry');
        $registryProperty->setAccessible(true);
        $registryProperty->setValue(null, null);

        $containerProperty = $reflection->getProperty('container');
        $containerProperty->setAccessible(true);
        $containerProperty->setValue(null, null);
    }

    /**
     * Test basic module lifecycle
     */
    public function testBasicModuleLifecycle(): void
    {
        $events = [];

        $module = new class($events) extends AbstractModule {
            private array $events;

            public function __construct(array &$events)
            {
                $this->events = &$events;
            }

            public function getName(): string
            {
                return 'test-module';
            }

            public function register(): void
            {
                $this->events[] = 'register';

                Arabic::container()->register('test-service', function() {
                    $service = new \stdClass();
                    $service->name = 'test';
                    return $service;
                });
            }

            public function boot(): void
            {
                $this->events[] = 'boot';
            }
        };

        Arabic::init([$module]);

        // Verify lifecycle events
        $this->assertEquals(['register', 'boot'], $events);

        // Verify service is available
        $this->assertTrue(Arabic::container()->has('test-service'));
        $service = Arabic::container()->get('test-service');
        $this->assertEquals('test', $service->name);
    }

    /**
     * Test module dependency resolution
     */
    public function testModuleDependencyResolution(): void
    {
        $bootOrder = [];

        // Base module with no dependencies
        $baseModule = new class($bootOrder) extends AbstractModule {
            private array $bootOrder;

            public function __construct(array &$bootOrder)
            {
                $this->bootOrder = &$bootOrder;
            }

            public function getName(): string
            {
                return 'base';
            }

            public function register(): void
            {
                Arabic::container()->register('base-service', function() {
                    return new \stdClass();
                });
            }

            public function boot(): void
            {
                $this->bootOrder[] = 'base';
            }
        };

        // Dependent module
        $dependentModule = new class($bootOrder) extends AbstractModule {
            private array $bootOrder;
            protected array $dependencies = ['base'];

            public function __construct(array &$bootOrder)
            {
                $this->bootOrder = &$bootOrder;
            }

            public function getName(): string
            {
                return 'dependent';
            }

            public function register(): void
            {
                Arabic::container()->register('dependent-service', function() {
                    return new \stdClass();
                });
            }

            public function boot(): void
            {
                $this->bootOrder[] = 'dependent';

                // Base service should be available
                $baseService = Arabic::container()->get('base-service');
                $this->assertInstanceOf(\stdClass::class, $baseService);
            }

            private function assertInstanceOf(string $class, $object): void
            {
                if (!$object instanceof $class) {
                    throw new \RuntimeException("Object is not instance of {$class}");
                }
            }
        };

        Arabic::init([$baseModule, $dependentModule]);

        // Verify boot order
        $this->assertEquals(['base', 'dependent'], $bootOrder);
    }

    /**
     * Test multiple modules working together
     */
    public function testMultipleModulesWorkingTogether(): void
    {
        // Config module
        $configModule = new class extends AbstractModule {
            public function getName(): string
            {
                return 'config';
            }

            public function register(): void
            {
                Arabic::container()->register('config', function() {
                    $config = new \stdClass();
                    $config->apiKey = 'test-key';
                    $config->timeout = 30;
                    return $config;
                });
            }
        };

        // Logger module (depends on config)
        $loggerModule = new class extends AbstractModule {
            protected array $dependencies = ['config'];

            public function getName(): string
            {
                return 'logger';
            }

            public function register(): void
            {
                Arabic::container()->register('logger', function($container) {
                    $config = $container->get('config');
                    $logger = new \stdClass();
                    $logger->apiKey = $config->apiKey;
                    $logger->logs = [];
                    return $logger;
                });
            }

            public function boot(): void
            {
                $logger = Arabic::container()->get('logger');
                $logger->logs[] = 'Logger initialized';
            }
        };

        // Translator module (depends on logger and config)
        $translatorModule = new class extends AbstractModule {
            protected array $dependencies = ['logger', 'config'];

            public function getName(): string
            {
                return 'translator';
            }

            public function register(): void
            {
                Arabic::container()->register('translator', function($container) {
                    $logger = $container->get('logger');
                    $config = $container->get('config');

                    $translator = new \stdClass();
                    $translator->logger = $logger;
                    $translator->timeout = $config->timeout;
                    $translator->translations = [];

                    return $translator;
                });
            }

            public function boot(): void
            {
                $translator = Arabic::container()->get('translator');
                $translator->logger->logs[] = 'Translator initialized';
                $translator->translations['hello'] = 'مرحباً';
            }
        };

        Arabic::init([$configModule, $loggerModule, $translatorModule]);

        // Verify all services are available and connected
        $config = Arabic::container()->get('config');
        $logger = Arabic::container()->get('logger');
        $translator = Arabic::container()->get('translator');

        $this->assertEquals('test-key', $config->apiKey);
        $this->assertEquals('test-key', $logger->apiKey);
        $this->assertEquals(30, $translator->timeout);
        $this->assertSame($logger, $translator->logger);
        $this->assertCount(2, $logger->logs);
        $this->assertEquals('مرحباً', $translator->translations['hello']);
    }

    /**
     * Test complex dependency chain
     */
    public function testComplexDependencyChain(): void
    {
        $bootOrder = [];

        // Create modules with complex dependencies:
        // A depends on B and C
        // B depends on D
        // C depends on D and E
        // D depends on E
        // E has no dependencies

        $moduleE = $this->createTestModule('E', [], $bootOrder);
        $moduleD = $this->createTestModule('D', ['E'], $bootOrder);
        $moduleC = $this->createTestModule('C', ['D', 'E'], $bootOrder);
        $moduleB = $this->createTestModule('B', ['D'], $bootOrder);
        $moduleA = $this->createTestModule('A', ['B', 'C'], $bootOrder);

        Arabic::init([$moduleA, $moduleB, $moduleC, $moduleD, $moduleE]);

        // Expected boot order: E -> D -> (B, C can be in any order) -> A
        $this->assertEquals('E', $bootOrder[0]);
        $this->assertEquals('D', $bootOrder[1]);
        $this->assertEquals('A', $bootOrder[4]);

        // B and C should be booted before A
        $aPosition = array_search('A', $bootOrder);
        $bPosition = array_search('B', $bootOrder);
        $cPosition = array_search('C', $bootOrder);

        $this->assertLessThan($aPosition, $bPosition);
        $this->assertLessThan($aPosition, $cPosition);
    }

    /**
     * Test circular dependency detection in integration
     */
    public function testCircularDependencyDetection(): void
    {
        $moduleA = new class extends AbstractModule {
            protected array $dependencies = ['module-b'];

            public function getName(): string
            {
                return 'module-a';
            }
        };

        $moduleB = new class extends AbstractModule {
            protected array $dependencies = ['module-c'];

            public function getName(): string
            {
                return 'module-b';
            }
        };

        $moduleC = new class extends AbstractModule {
            protected array $dependencies = ['module-a'];

            public function getName(): string
            {
                return 'module-c';
            }
        };

        $this->expectException(CircularDependencyException::class);

        Arabic::init([$moduleA, $moduleB, $moduleC]);
    }

    /**
     * Test module with ServiceInterface implementation
     */
    public function testModuleWithServiceInterface(): void
    {
        $service = new class implements ServiceInterface {
            private array $config = ['timeout' => 30];

            public function process(string $text): string
            {
                return strtoupper($text);
            }

            public function getServiceName(): string
            {
                return 'processor';
            }

            public function getConfig(): array
            {
                return $this->config;
            }

            public function isAvailable(): bool
            {
                return true;
            }
        };

        $module = new class($service) extends AbstractModule {
            private ServiceInterface $service;

            public function __construct(ServiceInterface $service)
            {
                $this->service = $service;
            }

            public function getName(): string
            {
                return 'processor-module';
            }

            public function register(): void
            {
                Arabic::container()->register('processor', $this->service);
            }
        };

        Arabic::init([$module]);

        $processor = Arabic::container()->get('processor');

        $this->assertInstanceOf(ServiceInterface::class, $processor);
        $this->assertEquals('processor', $processor->getServiceName());
        $this->assertTrue($processor->isAvailable());
        $this->assertEquals(['timeout' => 30], $processor->getConfig());
        $this->assertEquals('HELLO', $processor->process('hello'));
    }

    /**
     * Test lazy loading behavior
     */
    public function testLazyLoadingBehavior(): void
    {
        $instantiationCount = 0;

        $module = new class($instantiationCount) extends AbstractModule {
            private int $instantiationCount;

            public function __construct(int &$instantiationCount)
            {
                $this->instantiationCount = &$instantiationCount;
            }

            public function getName(): string
            {
                return 'lazy-module';
            }

            public function register(): void
            {
                Arabic::container()->register('lazy-service', function() {
                    $this->instantiationCount++;
                    return new \stdClass();
                });
            }
        };

        Arabic::init([$module]);

        // Service should not be instantiated yet
        $this->assertEquals(0, $instantiationCount);

        // First access triggers instantiation
        Arabic::container()->get('lazy-service');
        $this->assertEquals(1, $instantiationCount);

        // Subsequent accesses don't trigger instantiation (singleton)
        Arabic::container()->get('lazy-service');
        Arabic::container()->get('lazy-service');
        $this->assertEquals(1, $instantiationCount);
    }

    /**
     * Test module enable/disable functionality
     */
    public function testModuleEnableDisable(): void
    {
        $module = new class extends AbstractModule {
            public function getName(): string
            {
                return 'toggleable';
            }
        };

        $this->assertTrue($module->isEnabled());

        $module->disable();
        $this->assertFalse($module->isEnabled());

        $module->enable();
        $this->assertTrue($module->isEnabled());
    }

    /**
     * Test module version information
     */
    public function testModuleVersionInformation(): void
    {
        $module = new class extends AbstractModule {
            protected string $version = '2.5.1';

            public function getName(): string
            {
                return 'versioned-module';
            }
        };

        Arabic::init([$module]);

        $registeredModule = Arabic::registry()->get('versioned-module');
        $this->assertEquals('2.5.1', $registeredModule->getVersion());
    }

    /**
     * Test accessing registry and container
     */
    public function testAccessingRegistryAndContainer(): void
    {
        $module = new class extends AbstractModule {
            public function getName(): string
            {
                return 'test';
            }
        };

        Arabic::init([$module]);

        $registry = Arabic::registry();
        $container = Arabic::container();

        $this->assertInstanceOf(\ArPHP\Core\ModuleRegistry::class, $registry);
        $this->assertInstanceOf(\ArPHP\Core\ServiceContainer::class, $container);

        $this->assertTrue($registry->has('test'));
        $this->assertSame($module, $registry->get('test'));
    }

    /**
     * Test magic method service access
     */
    public function testMagicMethodServiceAccess(): void
    {
        $module = new class extends AbstractModule {
            public function getName(): string
            {
                return 'magic-test';
            }

            public function register(): void
            {
                Arabic::container()->register('myservice', function() {
                    $service = new \stdClass();
                    $service->value = 'magic';
                    return $service;
                });
            }
        };

        Arabic::init([$module]);

        // Access service via magic method
        $service = Arabic::myservice();

        $this->assertInstanceOf(\stdClass::class, $service);
        $this->assertEquals('magic', $service->value);
    }

    /**
     * Test initialization without modules
     */
    public function testInitializationWithoutModules(): void
    {
        Arabic::init();

        $registry = Arabic::registry();
        $container = Arabic::container();

        $this->assertInstanceOf(\ArPHP\Core\ModuleRegistry::class, $registry);
        $this->assertInstanceOf(\ArPHP\Core\ServiceContainer::class, $container);
        $this->assertEmpty($registry->all());
    }

    /**
     * Helper method to create test module
     */
    private function createTestModule(string $name, array $dependencies, array &$bootOrder): AbstractModule
    {
        return new class($name, $dependencies, $bootOrder) extends AbstractModule {
            private string $moduleName;
            private array $bootOrder;

            public function __construct(string $name, array $dependencies, array &$bootOrder)
            {
                $this->moduleName = $name;
                $this->dependencies = $dependencies;
                $this->bootOrder = &$bootOrder;
            }

            public function getName(): string
            {
                return $this->moduleName;
            }

            public function register(): void
            {
                Arabic::container()->register($this->moduleName, function() {
                    return new \stdClass();
                });
            }

            public function boot(): void
            {
                $this->bootOrder[] = $this->moduleName;
            }
        };
    }
}

