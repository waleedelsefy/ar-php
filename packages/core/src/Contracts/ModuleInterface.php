<?php

declare(strict_types=1);

namespace ArPHP\Core\Contracts;

/**
 * Interface that all ArPHP modules must implement
 * 
 * This ensures modularity and interoperability between packages. 
 * 
 * @package ArPHP\Core\Contracts
 * @author Waleed Elsefyy
 */
interface ModuleInterface
{
    /**
     * Get the unique module name
     * 
     * @return string The module name
     */
    public function getName(): string;
    
    /**
     * Get the module version
     * 
     * @return string The module version
     */
    public function getVersion(): string;
    
    /**
     * Register the module services
     * 
     * @return void
     */
    public function register(): void;
    
    /**
     * Boot the module
     * 
     * @return void
     */
    public function boot(): void;
    
    /**
     * Get module dependencies
     * 
     * @return array<string> List of required module names
     */
    public function getDependencies(): array;
    
    /**
     * Check if module is enabled
     * 
     * @return bool True if enabled
     */
    public function isEnabled(): bool;
}
