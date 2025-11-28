<?php

declare(strict_types=1);

namespace ArPHP\Core\Tests\Unit;

use ArPHP\Core\ModuleRegistry;
use ArPHP\Core\Contracts\ModuleInterface;
use ArPHP\Core\Exceptions\ModuleNotFoundException;
use ArPHP\Core\Exceptions\CircularDependencyException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for ModuleRegistry
 *
 * @package ArPHP\Core\Tests\Unit
 */
class ModuleRegistryTest extends TestCase
{
    private ModuleRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new ModuleRegistry();
    }

    /**
     * Test module registration
     */
    public function testRegisterModule(): void
    {
        $module = $this->createMockModule('test-module');

        $module->expects($this->once())
               ->method('register');

        $this->registry->register($module);

        $this->assertTrue($this->registry->has('test-module'));
    }

    /**
     * Test duplicate module registration throws exception
     */
    public function testRegisterDuplicateModuleThrowsException(): void
    {
        $module1 = $this->createMockModule('test-module');
        $module2 = $this->createMockModule('test-module');

        $module1->expects($this->once())->method('register');

        $this->registry->register($module1);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Module 'test-module' already registered");

        $this->registry->register($module2);
    }

    /**
     * Test getting registered module
     */
    public function testGetModule(): void
    {
        $module = $this->createMockModule('test-module');
        $module->method('register');

        $this->registry->register($module);
        $retrieved = $this->registry->get('test-module');

        $this->assertSame($module, $retrieved);
    }

    /**
     * Test getting non-existent module throws exception
     */
    public function testGetNonExistentModuleThrowsException(): void
    {
        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage("Module 'non-existent' not found");

        $this->registry->get('non-existent');
    }

    /**
     * Test checking if module exists
     */
    public function testHasModule(): void
    {
        $module = $this->createMockModule('test-module');
        $module->method('register');

        $this->assertFalse($this->registry->has('test-module'));

        $this->registry->register($module);

        $this->assertTrue($this->registry->has('test-module'));
    }

    /**
     * Test getting all registered modules
     */
    public function testGetAllModules(): void
    {
        $module1 = $this->createMockModule('module-1');
        $module2 = $this->createMockModule('module-2');

        $module1->method('register');
        $module2->method('register');

        $this->registry->register($module1);
        $this->registry->register($module2);

        $all = $this->registry->all();

        $this->assertCount(2, $all);
        $this->assertArrayHasKey('module-1', $all);
        $this->assertArrayHasKey('module-2', $all);
    }

    /**
     * Test booting a module without dependencies
     */
    public function testBootModuleWithoutDependencies(): void
    {
        $module = $this->createMockModule('test-module');
        $module->method('register');
        $module->method('getDependencies')->willReturn([]);

        $module->expects($this->once())
               ->method('boot');

        $this->registry->register($module);
        $this->registry->boot('test-module');
    }

    /**
     * Test booting same module twice only boots once
     */
    public function testBootModuleTwiceOnlyBootsOnce(): void
    {
        $module = $this->createMockModule('test-module');
        $module->method('register');
        $module->method('getDependencies')->willReturn([]);

        $module->expects($this->once())
               ->method('boot');

        $this->registry->register($module);
        $this->registry->boot('test-module');
        $this->registry->boot('test-module'); // Second call should be no-op
    }

    /**
     * Test booting module with dependencies
     */
    public function testBootModuleWithDependencies(): void
    {
        // Create dependency module
        $dependencyModule = $this->createMockModule('dependency');
        $dependencyModule->method('register');
        $dependencyModule->method('getDependencies')->willReturn([]);

        // Create main module that depends on dependency
        $mainModule = $this->createMockModule('main-module');
        $mainModule->method('register');
        $mainModule->method('getDependencies')->willReturn(['dependency']);

        // Both modules should be booted, dependency first
        $bootOrder = [];

        $dependencyModule->expects($this->once())
                        ->method('boot')
                        ->willReturnCallback(function() use (&$bootOrder) {
                            $bootOrder[] = 'dependency';
                        });

        $mainModule->expects($this->once())
                   ->method('boot')
                   ->willReturnCallback(function() use (&$bootOrder) {
                       $bootOrder[] = 'main-module';
                   });

        $this->registry->register($dependencyModule);
        $this->registry->register($mainModule);

        $this->registry->boot('main-module');

        // Verify dependency was booted before main module
        $this->assertEquals(['dependency', 'main-module'], $bootOrder);
    }

    /**
     * Test booting module with multiple dependencies
     */
    public function testBootModuleWithMultipleDependencies(): void
    {
        $dep1 = $this->createMockModule('dep1');
        $dep1->method('register');
        $dep1->method('getDependencies')->willReturn([]);
        $dep1->method('boot');

        $dep2 = $this->createMockModule('dep2');
        $dep2->method('register');
        $dep2->method('getDependencies')->willReturn([]);
        $dep2->method('boot');

        $mainModule = $this->createMockModule('main');
        $mainModule->method('register');
        $mainModule->method('getDependencies')->willReturn(['dep1', 'dep2']);
        $mainModule->expects($this->once())->method('boot');

        $this->registry->register($dep1);
        $this->registry->register($dep2);
        $this->registry->register($mainModule);

        $this->registry->boot('main');

        // No exception means success
        $this->assertTrue(true);
    }

    /**
     * Test booting module with transitive dependencies
     */
    public function testBootModuleWithTransitiveDependencies(): void
    {
        // A depends on B, B depends on C
        $moduleC = $this->createMockModule('module-c');
        $moduleC->method('register');
        $moduleC->method('getDependencies')->willReturn([]);

        $moduleB = $this->createMockModule('module-b');
        $moduleB->method('register');
        $moduleB->method('getDependencies')->willReturn(['module-c']);

        $moduleA = $this->createMockModule('module-a');
        $moduleA->method('register');
        $moduleA->method('getDependencies')->willReturn(['module-b']);

        $bootOrder = [];

        $moduleC->expects($this->once())
               ->method('boot')
               ->willReturnCallback(function() use (&$bootOrder) {
                   $bootOrder[] = 'module-c';
               });

        $moduleB->expects($this->once())
               ->method('boot')
               ->willReturnCallback(function() use (&$bootOrder) {
                   $bootOrder[] = 'module-b';
               });

        $moduleA->expects($this->once())
               ->method('boot')
               ->willReturnCallback(function() use (&$bootOrder) {
                   $bootOrder[] = 'module-a';
               });

        $this->registry->register($moduleC);
        $this->registry->register($moduleB);
        $this->registry->register($moduleA);

        $this->registry->boot('module-a');

        // Verify boot order: C -> B -> A
        $this->assertEquals(['module-c', 'module-b', 'module-a'], $bootOrder);
    }

    /**
     * Test booting non-existent module throws exception
     */
    public function testBootNonExistentModuleThrowsException(): void
    {
        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage("Module 'non-existent' not found");

        $this->registry->boot('non-existent');
    }

    /**
     * Test booting module with missing dependency throws exception
     */
    public function testBootModuleWithMissingDependencyThrowsException(): void
    {
        $module = $this->createMockModule('test-module');
        $module->method('register');
        $module->method('getDependencies')->willReturn(['missing-dependency']);

        $this->registry->register($module);

        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage("Module 'missing-dependency' not found");

        $this->registry->boot('test-module');
    }

    /**
     * Test circular dependency detection (direct circle)
     */
    public function testCircularDependencyDetectionDirect(): void
    {
        // Module A depends on B, B depends on A
        $moduleA = $this->createMockModule('module-a');
        $moduleA->method('register');
        $moduleA->method('getDependencies')->willReturn(['module-b']);

        $moduleB = $this->createMockModule('module-b');
        $moduleB->method('register');
        $moduleB->method('getDependencies')->willReturn(['module-a']);

        $this->registry->register($moduleA);
        $this->registry->register($moduleB);

        $this->expectException(CircularDependencyException::class);
        $this->expectExceptionMessageMatches('/Circular dependency.*module-a.*module-b.*module-a/');

        $this->registry->boot('module-a');
    }

    /**
     * Test circular dependency detection (indirect circle)
     */
    public function testCircularDependencyDetectionIndirect(): void
    {
        // A depends on B, B depends on C, C depends on A
        $moduleA = $this->createMockModule('module-a');
        $moduleA->method('register');
        $moduleA->method('getDependencies')->willReturn(['module-b']);

        $moduleB = $this->createMockModule('module-b');
        $moduleB->method('register');
        $moduleB->method('getDependencies')->willReturn(['module-c']);

        $moduleC = $this->createMockModule('module-c');
        $moduleC->method('register');
        $moduleC->method('getDependencies')->willReturn(['module-a']);

        $this->registry->register($moduleA);
        $this->registry->register($moduleB);
        $this->registry->register($moduleC);

        $this->expectException(CircularDependencyException::class);

        $this->registry->boot('module-a');
    }

    /**
     * Test self-dependency throws circular dependency exception
     */
    public function testSelfDependencyThrowsException(): void
    {
        $module = $this->createMockModule('self-dependent');
        $module->method('register');
        $module->method('getDependencies')->willReturn(['self-dependent']);

        $this->registry->register($module);

        $this->expectException(CircularDependencyException::class);

        $this->registry->boot('self-dependent');
    }

    /**
     * Helper method to create a mock module
     */
    private function createMockModule(string $name): ModuleInterface
    {
        $module = $this->createMock(ModuleInterface::class);

        $module->method('getName')
               ->willReturn($name);

        $module->method('getVersion')
               ->willReturn('1.0.0');

        $module->method('isEnabled')
               ->willReturn(true);

        return $module;
    }
}

