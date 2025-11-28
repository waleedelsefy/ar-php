<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Slugify\Exceptions;

use ArPHP\Core\Exceptions\ArPhpException;

/**
 * Slugify Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Slugify
 */
final class SlugifyException extends ArPhpException
{
    public static function emptyText(): self
    {
        return new self('Text cannot be empty for slug generation');
    }

    public static function invalidSeparator(string $separator): self
    {
        return new self("Invalid separator: {$separator}");
    }

    public static function slugGenerationFailed(): self
    {
        return new self('Failed to generate slug');
    }
}
