<?php

declare(strict_types=1);

namespace ArPHP\Core;

use ArPHP\Core\Contracts\ModuleInterface;
use ArPHP\Core\Exceptions\ModuleNotFoundException;
use ArPHP\Core\Exceptions\CircularDependencyException;

/**
 * Central registry for all ArPHP modules
 * 
 * Manages module lifecycle and dependency resolution
 * 
 * @package ArPHP\Core
 * @author Waleed Elsefyy
 */
class ModuleRegistry
{
    /** @var array<string, ModuleInterface> */
    private array $modules = [];
    
    /** @var array<string, bool> */
    private array $booted = [];
    
    /** @var array<string> */
    private array $loading = [];
    
    /**
     * Register a module
     * 
     * @param ModuleInterface $module Module to register
     * @return void
     */
    public function register(ModuleInterface $module): void
    {
        $name = $module->getName();
        
        if (isset($this->modules[$name])) {
            throw new \RuntimeException("Module '$name' already registered");
        }
        
        $this->modules[$name] = $module;
        $module->register();
    }
    
    /**
     * Boot a module with dependency resolution
     * 
     * @param string $moduleName Module name
     * @return void
     * @throws ModuleNotFoundException
     * @throws CircularDependencyException
     */
    public function boot(string $moduleName): void
    {
        if (isset($this->booted[$moduleName])) {
            return;
        }
        
        if (! isset($this->modules[$moduleName])) {
            throw new ModuleNotFoundException($moduleName);
        }
        
        if (in_array($moduleName, $this->loading, true)) {
            $this->loading[] = $moduleName;
            throw CircularDependencyException::fromChain($this->loading);
        }
        
        $this->loading[] = $moduleName;
        $module = $this->modules[$moduleName];
        
        foreach ($module->getDependencies() as $dependency) {
            $this->boot($dependency);
        }
        
        $module->boot();
        $this->booted[$moduleName] = true;
        
        array_pop($this->loading);
    }
    
    /**
     * Get a module
     * 
     * @param string $moduleName Module name
     * @return ModuleInterface
     * @throws ModuleNotFoundException
     */
    public function get(string $moduleName): ModuleInterface
    {
        if (! isset($this->modules[$moduleName])) {
            throw new ModuleNotFoundException($moduleName);
        }
        
        return $this->modules[$moduleName];
    }
    
    /**
     * Check if module exists
     * 
     * @param string $moduleName Module name
     * @return bool
     */
    public function has(string $moduleName): bool
    {
        return isset($this->modules[$moduleName]);
    }
    
    /**
     * Get all modules
     * 
     * @return array<string, ModuleInterface>
     */
    public function all(): array
    {
        return $this->modules;
    }
}
