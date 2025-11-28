<?php

declare(strict_types=1);

namespace ArPHP\Core\Exceptions;

use RuntimeException;

/**
 * Exception thrown when a module is not found
 * 
 * @package ArPHP\Core\Exceptions
 * @author Waleed Elsefyy
 */
class ModuleNotFoundException extends RuntimeException
{
    /**
     * Create a new ModuleNotFoundException
     * 
     * @param string $moduleName Module name
     * @param int $code Exception code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $moduleName,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $message = sprintf(
            "Module '%s' not found.  Make sure it's registered. ",
            $moduleName
        );
        
        parent::__construct($message, $code, $previous);
    }
}
