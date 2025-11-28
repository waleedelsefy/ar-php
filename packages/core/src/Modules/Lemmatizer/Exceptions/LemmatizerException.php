<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Lemmatizer\Exceptions;

use ArPHP\Core\Exceptions\ArPhpException;

/**
 * Lemmatizer Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Lemmatizer
 */
final class LemmatizerException extends ArPhpException
{
    public static function invalidWord(string $word): self
    {
        return new self("Invalid word for lemmatization: {$word}");
    }

    public static function rootNotFound(string $word): self
    {
        return new self("Root not found for word: {$word}");
    }

    public static function patternNotMatched(string $word): self
    {
        return new self("No pattern matched for word: {$word}");
    }
}
