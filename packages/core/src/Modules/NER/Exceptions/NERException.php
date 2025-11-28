<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\NER\Exceptions;

use ArPHP\Core\Exceptions\ArPhpException;

/**
 * NER Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\NER
 */
final class NERException extends ArPhpException
{
    public static function invalidEntityType(string $type): self
    {
        return new self("Invalid entity type: {$type}");
    }

    public static function extractionFailed(string $reason): self
    {
        return new self("Entity extraction failed: {$reason}");
    }

    public static function emptyText(): self
    {
        return new self('Text cannot be empty for entity extraction');
    }
}
