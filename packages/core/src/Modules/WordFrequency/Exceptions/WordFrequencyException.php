<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\WordFrequency\Exceptions;

use ArPHP\Core\Exceptions\ArPhpException;

/**
 * WordFrequency Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\WordFrequency
 */
final class WordFrequencyException extends ArPhpException
{
    public static function emptyText(): self
    {
        return new self('Text cannot be empty for frequency analysis');
    }

    public static function invalidLimit(int $limit): self
    {
        return new self("Invalid limit: {$limit}. Must be positive.");
    }
}
