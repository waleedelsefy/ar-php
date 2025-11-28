<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\DialectNormalizer\Exceptions;

use ArPHP\Core\Exceptions\ArPhpException;

/**
 * DialectNormalizer Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\DialectNormalizer
 */
final class DialectNormalizerException extends ArPhpException
{
    public static function unsupportedDialect(string $dialect): self
    {
        return new self("Unsupported dialect: {$dialect}");
    }

    public static function detectionFailed(): self
    {
        return new self('Failed to detect dialect');
    }

    public static function conversionFailed(string $from, string $to): self
    {
        return new self("Failed to convert from {$from} to {$to}");
    }
}
