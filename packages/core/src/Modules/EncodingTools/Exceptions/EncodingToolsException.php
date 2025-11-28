<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\EncodingTools\Exceptions;

use ArPHP\Core\Exceptions\ArPHPException;

/**
 * Encoding Tools Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\EncodingTools
 */
class EncodingToolsException extends ArPHPException
{
    public static function unsupportedEncoding(string $encoding): self
    {
        return new self("Unsupported encoding: '{$encoding}'");
    }

    public static function conversionFailed(string $from, string $to): self
    {
        return new self("Failed to convert from '{$from}' to '{$to}'");
    }

    public static function detectionFailed(): self
    {
        return new self('Failed to detect text encoding');
    }

    public static function invalidUtf8(): self
    {
        return new self('Text is not valid UTF-8');
    }

    public static function invalidCodepoint(int $codepoint): self
    {
        return new self("Invalid Unicode codepoint: {$codepoint}");
    }

    public static function emptyInput(): self
    {
        return new self('Input text cannot be empty');
    }

    public static function iconvNotAvailable(): self
    {
        return new self('iconv extension is not available');
    }
}
