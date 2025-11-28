<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicSoundex\Exceptions;

use RuntimeException;

/**
 * Exception for Arabic Soundex operations
 *
 * @package ArPHP\Core\Modules\ArabicSoundex\Exceptions
 */
final class ArabicSoundexException extends RuntimeException
{
    public static function emptyInput(): self
    {
        return new self('Input word cannot be empty');
    }

    public static function invalidCharacter(string $char): self
    {
        return new self(
            \sprintf('Invalid character encountered: %s', $char)
        );
    }

    public static function invalidThreshold(int $threshold): self
    {
        return new self(
            \sprintf('Invalid threshold: %d. Must be between 0 and 100.', $threshold)
        );
    }

    public static function invalidCodeLength(int $length): self
    {
        return new self(
            \sprintf('Invalid code length: %d. Must be positive.', $length)
        );
    }
}
