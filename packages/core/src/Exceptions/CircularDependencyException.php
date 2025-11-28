<?php

declare(strict_types=1);

namespace ArPHP\Core\Exceptions;

use RuntimeException;

/**
 * Exception thrown when circular dependencies are detected
 * 
 * @package ArPHP\Core\Exceptions
 * @author Waleed Elsefyy
 */
class CircularDependencyException extends RuntimeException
{
    /**
     * Create a new CircularDependencyException
     * 
     * @param string $message Error message
     * @param int $code Exception code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = "Circular dependency detected",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Create from dependency chain
     * 
     * @param array<string> $chain Dependency chain
     * @return self
     */
    public static function fromChain(array $chain): self
    {
        $message = sprintf(
            "Circular dependency: %s",
            implode(' -> ', $chain)
        );
        
        return new self($message);
    }
}
