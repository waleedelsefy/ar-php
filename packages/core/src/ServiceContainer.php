<?php

declare(strict_types=1);

namespace ArPHP\Core;

use Psr\Container\ContainerInterface;
use ArPHP\Core\Exceptions\ServiceNotFoundException;

/**
 * PSR-11 compliant dependency injection container
 * 
 * @package ArPHP\Core
 * @author Waleed Elsefyy
 */
class ServiceContainer implements ContainerInterface
{
    /** @var array<string, mixed> */
    private array $services = [];
    
    /** @var array<string, callable> */
    private array $factories = [];
    
    /**
     * Register a service
     * 
     * @param string $id Service identifier
     * @param callable|object $concrete Service instance or factory
     * @return void
     */
    public function register(string $id, callable|object $concrete): void
    {
        if (is_callable($concrete)) {
            $this->factories[$id] = $concrete;
        } else {
            $this->services[$id] = $concrete;
        }
    }
    
    /**
     * Get a service (PSR-11)
     * 
     * @param string $id Service identifier
     * @return mixed Service instance
     * @throws ServiceNotFoundException
     */
    public function get(string $id): mixed
    {
        if (isset($this->services[$id])) {
            return $this->services[$id];
        }
        
        if (isset($this->factories[$id])) {
            $service = $this->factories[$id]($this);
            $this->services[$id] = $service;
            return $service;
        }
        
        throw new ServiceNotFoundException($id);
    }
    
    /**
     * Check if service exists (PSR-11)
     * 
     * @param string $id Service identifier
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]) || isset($this->factories[$id]);
    }
}
