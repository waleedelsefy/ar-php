<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Summarizer\Exceptions;

use ArPHP\Core\Exceptions\ArPhpException;

/**
 * Summarizer Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Summarizer
 */
final class SummarizerException extends ArPhpException
{
    public static function textTooShort(int $minLength): self
    {
        return new self("Text is too short for summarization. Minimum: {$minLength} characters.");
    }

    public static function invalidRatio(float $ratio): self
    {
        return new self("Invalid ratio: {$ratio}. Must be between 0.0 and 1.0.");
    }

    public static function noSentences(): self
    {
        return new self("No sentences found in text.");
    }

    public static function processingError(string $reason): self
    {
        return new self("Summarization error: {$reason}");
    }
}
