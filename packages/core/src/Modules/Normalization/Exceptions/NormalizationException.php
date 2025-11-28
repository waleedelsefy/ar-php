<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Normalization\Exceptions;

use ArPHP\Core\Exceptions\ArPHPException;

/**
 * Normalization Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Normalization
 */
class NormalizationException extends ArPHPException
{
    public static function invalidOption(string $option): self
    {
        return new self("Invalid normalization option: '{$option}'");
    }

    public static function invalidNumberStyle(string $style): self
    {
        return new self("Invalid number style: '{$style}'. Use 'arabic' or 'western'");
    }

    public static function normalizationFailed(string $reason): self
    {
        return new self("Normalization failed: {$reason}");
    }
}
