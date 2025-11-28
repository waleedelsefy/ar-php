<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\BuckwalterTransliteration\Exceptions;

use ArPHP\Core\Exceptions\ArPHPException;

/**
 * Buckwalter Transliteration Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\BuckwalterTransliteration
 */
class BuckwalterTransliterationException extends ArPHPException
{
    public static function emptyText(): self
    {
        return new self('Text cannot be empty');
    }

    public static function unsupportedScheme(string $scheme): self
    {
        return new self("Unsupported transliteration scheme: '{$scheme}'");
    }

    public static function transliterationFailed(string $reason): self
    {
        return new self("Transliteration failed: {$reason}");
    }

    public static function invalidCharacter(string $char): self
    {
        return new self("Invalid character for transliteration: '{$char}'");
    }
}
