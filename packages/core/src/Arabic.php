<?php

declare(strict_types=1);

namespace ArPHP\Core;

use ArPHP\Core\Contracts\ModuleInterface;

/**
 * Main facade for ArPHP Enhanced
 * 
 * Provides unified access to all modules
 * 
 * @package ArPHP\Core
 * @author Waleed Elsefyy
 */
class Arabic
{
    private static ?ModuleRegistry $registry = null;
    private static ?ServiceContainer $container = null;
    
    /**
     * Initialize ArPHP
     * 
     * @param array<ModuleInterface> $modules Optional modules to register
     * @return void
     */
    public static function init(array $modules = []): void
    {
        self::$registry = new ModuleRegistry();
        self::$container = new ServiceContainer();
        
        foreach ($modules as $module) {
            self::$registry->register($module);
        }
        
        foreach (array_keys(self::$registry->all()) as $name) {
            self::$registry->boot($name);
        }
    }
    
    /**
     * Get module registry
     * 
     * @return ModuleRegistry
     */
    public static function registry(): ModuleRegistry
    {
        if (self::$registry === null) {
            self::init();
        }
        
        /** @var ModuleRegistry $registry */
        $registry = self::$registry;
        return $registry;
    }
    
    /**
     * Get service container
     * 
     * @return ServiceContainer
     */
    public static function container(): ServiceContainer
    {
        if (self::$container === null) {
            self::init();
        }
        
        /** @var ServiceContainer $container */
        $container = self::$container;
        return $container;
    }
    
    /**
     * Magic method for service access
     * 
     * @param string $name Service name
     * @param array<mixed> $arguments Method arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return self::container()->get($name);
    }
}
