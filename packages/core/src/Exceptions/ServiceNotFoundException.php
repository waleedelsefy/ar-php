<?php

declare(strict_types=1);

namespace ArPHP\Core\Exceptions;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * Exception thrown when a service is not found
 * 
 * Implements PSR-11 NotFoundExceptionInterface
 * 
 * @package ArPHP\Core\Exceptions
 * @author Waleed Elsefyy
 */
class ServiceNotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
    /**
     * Create a new ServiceNotFoundException
     * 
     * @param string $serviceId Service ID
     * @param int $code Exception code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $serviceId,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $message = sprintf(
            "Service '%s' not found in container.",
            $serviceId
        );
        
        parent::__construct($message, $code, $previous);
    }
}
