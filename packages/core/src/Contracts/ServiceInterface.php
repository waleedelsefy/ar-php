<?php

declare(strict_types=1);

namespace ArPHP\Core\Contracts;

/**
 * Base interface for all ArPHP services
 * 
 * @package ArPHP\Core\Contracts
 * @author Waleed Elsefyy
 */
interface ServiceInterface
{
    /**
     * Get the service name
     * 
     * @return string Service name
     */
    public function getServiceName(): string;
    
    /**
     * Get the service configuration
     * 
     * @return array<string, mixed> Configuration array
     */
    public function getConfig(): array;
    
    /**
     * Check if service is available
     * 
     * @return bool True if available
     */
    public function isAvailable(): bool;
}
