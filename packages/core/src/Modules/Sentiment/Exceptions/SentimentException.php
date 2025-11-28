<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Sentiment\Exceptions;

use ArPHP\Core\Exceptions\ArPhpException;

/**
 * Sentiment Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Sentiment
 */
final class SentimentException extends ArPhpException
{
    public static function emptyText(): self
    {
        return new self('Text cannot be empty for sentiment analysis');
    }

    public static function invalidScore(float $score): self
    {
        return new self("Invalid sentiment score: {$score}. Score must be between -1.0 and 1.0");
    }

    public static function analysisError(string $reason): self
    {
        return new self("Sentiment analysis error: {$reason}");
    }
}
