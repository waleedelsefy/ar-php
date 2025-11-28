<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Stopwords\Exceptions;

use ArPHP\Core\Exceptions\ArPhpException;

/**
 * Stopwords Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Stopwords
 */
final class StopwordsException extends ArPhpException
{
    public static function invalidCategory(string $category): self
    {
        return new self("Invalid stopwords category: {$category}");
    }

    public static function emptyWordList(): self
    {
        return new self('Word list cannot be empty');
    }

    public static function loadFailed(string $reason): self
    {
        return new self("Failed to load stopwords: {$reason}");
    }
}
