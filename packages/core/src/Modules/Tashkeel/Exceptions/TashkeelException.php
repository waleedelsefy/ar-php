<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Tashkeel\Exceptions;

use ArPHP\Core\Exceptions\ArPhpException;

/**
 * Tashkeel Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Tashkeel
 */
final class TashkeelException extends ArPhpException
{
    public static function invalidDiacritic(string $diacritic): self
    {
        return new self("Invalid diacritic: {$diacritic}");
    }

    public static function processingError(string $reason): self
    {
        return new self("Tashkeel processing error: {$reason}");
    }
}
