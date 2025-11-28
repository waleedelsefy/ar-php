<?php

declare(strict_types=1);

namespace ArPHP\Core\Tests\Unit;

use ArPHP\Core\ServiceContainer;
use ArPHP\Core\Exceptions\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Unit tests for ServiceContainer
 *
 * @package ArPHP\Core\Tests\Unit
 */
class ServiceContainerTest extends TestCase
{
    private ServiceContainer $container;

    protected function setUp(): void
    {
        $this->container = new ServiceContainer();
    }

    /**
     * Test container implements PSR-11 interface
     */
    public function testImplementsPsr11Interface(): void
    {
        $this->assertInstanceOf(ContainerInterface::class, $this->container);
    }

    /**
     * Test registering service as object
     */
    public function testRegisterServiceAsObject(): void
    {
        $service = new \stdClass();
        $service->name = 'test-service';

        $this->container->register('test', $service);

        $this->assertTrue($this->container->has('test'));
    }

    /**
     * Test registering service as factory
     */
    public function testRegisterServiceAsFactory(): void
    {
        $factory = function() {
            $service = new \stdClass();
            $service->name = 'factory-service';
            return $service;
        };

        $this->container->register('test', $factory);

        $this->assertTrue($this->container->has('test'));
    }

    /**
     * Test getting service registered as object
     */
    public function testGetServiceRegisteredAsObject(): void
    {
        $service = new \stdClass();
        $service->name = 'test-service';

        $this->container->register('test', $service);
        $retrieved = $this->container->get('test');

        $this->assertSame($service, $retrieved);
        $this->assertEquals('test-service', $retrieved->name);
    }

    /**
     * Test getting service registered as factory
     */
    public function testGetServiceRegisteredAsFactory(): void
    {
        $factory = function() {
            $service = new \stdClass();
            $service->name = 'factory-service';
            return $service;
        };

        $this->container->register('test', $factory);
        $retrieved = $this->container->get('test');

        $this->assertInstanceOf(\stdClass::class, $retrieved);
        $this->assertEquals('factory-service', $retrieved->name);
    }

    /**
     * Test factory receives container as parameter
     */
    public function testFactoryReceivesContainer(): void
    {
        $factory = function($container) {
            $this->assertInstanceOf(ServiceContainer::class, $container);
            $this->assertInstanceOf(ContainerInterface::class, $container);
            return new \stdClass();
        };

        $this->container->register('test', $factory);
        $this->container->get('test');
    }

    /**
     * Test singleton behavior - factory called only once
     */
    public function testSingletonBehavior(): void
    {
        $callCount = 0;

        $factory = function() use (&$callCount) {
            $callCount++;
            $service = new \stdClass();
            $service->id = uniqid();
            return $service;
        };

        $this->container->register('test', $factory);

        $service1 = $this->container->get('test');
        $service2 = $this->container->get('test');
        $service3 = $this->container->get('test');

        // Factory should be called only once
        $this->assertEquals(1, $callCount);

        // All references should be the same instance
        $this->assertSame($service1, $service2);
        $this->assertSame($service2, $service3);
        $this->assertEquals($service1->id, $service2->id);
    }

    /**
     * Test getting non-existent service throws exception
     */
    public function testGetNonExistentServiceThrowsException(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage("Service 'non-existent' not found in container");

        $this->container->get('non-existent');
    }

    /**
     * Test exception implements PSR-11 NotFoundExceptionInterface
     */
    public function testExceptionImplementsPsr11Interface(): void
    {
        try {
            $this->container->get('non-existent');
            $this->fail('Expected ServiceNotFoundException to be thrown');
        } catch (ServiceNotFoundException $e) {
            $this->assertInstanceOf(NotFoundExceptionInterface::class, $e);
        }
    }

    /**
     * Test has() returns true for registered services
     */
    public function testHasReturnsTrueForRegisteredServices(): void
    {
        $this->assertFalse($this->container->has('test'));

        $this->container->register('test', new \stdClass());

        $this->assertTrue($this->container->has('test'));
    }

    /**
     * Test has() returns true for factory services before instantiation
     */
    public function testHasReturnsTrueForFactoryBeforeInstantiation(): void
    {
        $factory = function() {
            return new \stdClass();
        };

        $this->container->register('test', $factory);

        // has() should return true even though factory hasn't been called yet
        $this->assertTrue($this->container->has('test'));
    }

    /**
     * Test has() returns false for non-existent services
     */
    public function testHasReturnsFalseForNonExistentServices(): void
    {
        $this->assertFalse($this->container->has('non-existent'));
    }

    /**
     * Test registering multiple services
     */
    public function testRegisterMultipleServices(): void
    {
        $service1 = new \stdClass();
        $service1->name = 'service1';

        $service2 = new \stdClass();
        $service2->name = 'service2';

        $factory3 = function() {
            $service = new \stdClass();
            $service->name = 'service3';
            return $service;
        };

        $this->container->register('service1', $service1);
        $this->container->register('service2', $service2);
        $this->container->register('service3', $factory3);

        $this->assertTrue($this->container->has('service1'));
        $this->assertTrue($this->container->has('service2'));
        $this->assertTrue($this->container->has('service3'));

        $this->assertEquals('service1', $this->container->get('service1')->name);
        $this->assertEquals('service2', $this->container->get('service2')->name);
        $this->assertEquals('service3', $this->container->get('service3')->name);
    }

    /**
     * Test overwriting registered service
     */
    public function testOverwriteRegisteredService(): void
    {
        $service1 = new \stdClass();
        $service1->value = 'first';

        $service2 = new \stdClass();
        $service2->value = 'second';

        $this->container->register('test', $service1);
        $this->assertEquals('first', $this->container->get('test')->value);

        // Register again with different value
        $this->container->register('test', $service2);
        $this->assertEquals('second', $this->container->get('test')->value);
    }

    /**
     * Test factory can access other services from container
     */
    public function testFactoryCanAccessOtherServices(): void
    {
        // Register a dependency
        $dependency = new \stdClass();
        $dependency->name = 'dependency';
        $this->container->register('dependency', $dependency);

        // Register a service that uses the dependency
        $factory = function($container) {
            $dep = $container->get('dependency');
            $service = new \stdClass();
            $service->dependency = $dep;
            return $service;
        };

        $this->container->register('main-service', $factory);

        $service = $this->container->get('main-service');

        $this->assertInstanceOf(\stdClass::class, $service->dependency);
        $this->assertEquals('dependency', $service->dependency->name);
    }

    /**
     * Test complex service dependency chain
     */
    public function testComplexServiceDependencyChain(): void
    {
        // Register config service
        $config = new \stdClass();
        $config->apiKey = 'test-key';
        $this->container->register('config', $config);

        // Register logger that depends on config
        $this->container->register('logger', function($container) {
            $config = $container->get('config');
            $logger = new \stdClass();
            $logger->apiKey = $config->apiKey;
            $logger->name = 'logger';
            return $logger;
        });

        // Register translator that depends on logger and config
        $this->container->register('translator', function($container) {
            $logger = $container->get('logger');
            $config = $container->get('config');

            $translator = new \stdClass();
            $translator->logger = $logger;
            $translator->apiKey = $config->apiKey;
            $translator->name = 'translator';

            return $translator;
        });

        $translator = $this->container->get('translator');

        $this->assertEquals('translator', $translator->name);
        $this->assertEquals('test-key', $translator->apiKey);
        $this->assertEquals('logger', $translator->logger->name);
        $this->assertEquals('test-key', $translator->logger->apiKey);
    }

    /**
     * Test callable object as factory
     */
    public function testCallableObjectAsFactory(): void
    {
        $callable = new class {
            public function __invoke() {
                $service = new \stdClass();
                $service->name = 'callable-service';
                return $service;
            }
        };

        $this->container->register('test', $callable);
        $service = $this->container->get('test');

        $this->assertEquals('callable-service', $service->name);
    }

    /**
     * Test static method as factory
     */
    public function testStaticMethodAsFactory(): void
    {
        $factory = [self::class, 'createTestService'];

        $this->container->register('test', $factory);
        $service = $this->container->get('test');

        $this->assertEquals('static-factory-service', $service->name);
    }

    /**
     * Test instance method as factory
     */
    public function testInstanceMethodAsFactory(): void
    {
        $factoryObject = new class {
            public function createService() {
                $service = new \stdClass();
                $service->name = 'instance-factory-service';
                return $service;
            }
        };

        $factory = [$factoryObject, 'createService'];

        $this->container->register('test', $factory);
        $service = $this->container->get('test');

        $this->assertEquals('instance-factory-service', $service->name);
    }

    /**
     * Static factory method for testing
     */
    public static function createTestService(): \stdClass
    {
        $service = new \stdClass();
        $service->name = 'static-factory-service';
        return $service;
    }

    /**
     * Test registering service with numeric ID
     */
    public function testRegisterServiceWithNumericId(): void
    {
        $service = new \stdClass();
        $service->name = 'numeric-id-service';

        $this->container->register('123', $service);

        $this->assertTrue($this->container->has('123'));
        $this->assertEquals('numeric-id-service', $this->container->get('123')->name);
    }

    /**
     * Test empty string as service ID
     */
    public function testEmptyStringAsServiceId(): void
    {
        $service = new \stdClass();
        $service->name = 'empty-id-service';

        $this->container->register('', $service);

        $this->assertTrue($this->container->has(''));
        $this->assertEquals('empty-id-service', $this->container->get('')->name);
    }
}

