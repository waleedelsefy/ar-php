<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicTokenizer\Exceptions;

use ArPHP\Core\Exceptions\ArPHPException;

/**
 * Arabic Tokenizer Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\ArabicTokenizer
 */
class ArabicTokenizerException extends ArPHPException
{
    public static function emptyText(): self
    {
        return new self('Text cannot be empty');
    }

    public static function invalidNgramSize(int $n): self
    {
        return new self("Invalid n-gram size: {$n}. Must be >= 1");
    }

    public static function invalidPattern(string $pattern): self
    {
        return new self("Invalid split pattern: '{$pattern}'");
    }

    public static function tokenizationFailed(string $reason): self
    {
        return new self("Tokenization failed: {$reason}");
    }
}
