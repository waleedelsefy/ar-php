<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicNameParser\Exceptions;

use ArPHP\Core\Exceptions\ArPHPException;

/**
 * Arabic Name Parser Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\ArabicNameParser
 */
class ArabicNameParserException extends ArPHPException
{
    public static function emptyName(): self
    {
        return new self('Name cannot be empty');
    }

    public static function invalidName(string $name): self
    {
        return new self("Invalid Arabic name format: '{$name}'");
    }

    public static function invalidFormatStyle(string $style): self
    {
        return new self("Invalid format style: '{$style}'");
    }

    public static function parsingFailed(string $name, string $reason): self
    {
        return new self("Failed to parse name '{$name}': {$reason}");
    }

    public static function invalidPattern(string $pattern): self
    {
        return new self("Invalid name pattern: '{$pattern}'");
    }

    public static function componentNotFound(string $component, string $name): self
    {
        return new self("Component '{$component}' not found in name '{$name}'");
    }
}
