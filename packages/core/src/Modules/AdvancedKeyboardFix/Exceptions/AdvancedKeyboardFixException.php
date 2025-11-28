<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\AdvancedKeyboardFix\Exceptions;

use ArPHP\Core\Exceptions\ArPHPException;

/**
 * Advanced Keyboard Fix Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\AdvancedKeyboardFix
 */
class AdvancedKeyboardFixException extends ArPHPException
{
    public static function emptyText(): self
    {
        return new self('Text cannot be empty');
    }

    public static function unsupportedLayout(string $layout): self
    {
        return new self("Unsupported keyboard layout: '{$layout}'");
    }

    public static function conversionFailed(string $reason): self
    {
        return new self("Keyboard conversion failed: {$reason}");
    }

    public static function detectionFailed(): self
    {
        return new self('Could not detect keyboard layout');
    }
}
