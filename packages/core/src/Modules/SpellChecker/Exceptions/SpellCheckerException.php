<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\SpellChecker\Exceptions;

use ArPHP\Core\Exceptions\ArPhpException;

/**
 * SpellChecker Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\SpellChecker
 */
final class SpellCheckerException extends ArPhpException
{
    public static function dictionaryLoadFailed(string $reason): self
    {
        return new self("Failed to load dictionary: {$reason}");
    }

    public static function invalidWord(string $word): self
    {
        return new self("Invalid word: {$word}");
    }

    public static function wordAlreadyExists(string $word): self
    {
        return new self("Word already exists in dictionary: {$word}");
    }

    public static function wordNotFound(string $word): self
    {
        return new self("Word not found in dictionary: {$word}");
    }
}
