<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Summarizer;

/**
 * Summarizer Facade - PHP 8.4
 *
 * Static facade for Arabic text summarization.
 *
 * @package ArPHP\Core\Modules\Summarizer
 *
 * @method static string summarize(string $text, int $numSentences = 3)
 * @method static string summarizeByRatio(string $text, float $ratio = 0.3)
 * @method static array extractKeySentences(string $text, int $count = 5)
 * @method static array scoreSentences(string $text)
 * @method static array extractKeywords(string $text, int $count = 10)
 * @method static array splitSentences(string $text)
 * @method static array getStatistics(string $text)
 * @method static string generateHeadline(string $text, int $maxLength = 100)
 */
final class Summarizer
{
    private static ?SummarizerModule $instance = null;

    /**
     * Get singleton instance
     */
    public static function getInstance(): SummarizerModule
    {
        if (self::$instance === null) {
            self::$instance = new SummarizerModule();
        }
        return self::$instance;
    }

    /**
     * Reset instance
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Summarize text to specified number of sentences
     */
    public static function summarize(string $text, int $numSentences = 3): string
    {
        return self::getInstance()->summarize($text, $numSentences);
    }

    /**
     * Summarize text by ratio
     *
     * @param float $ratio Ratio of text to keep (0.0 to 1.0)
     */
    public static function byRatio(string $text, float $ratio = 0.3): string
    {
        return self::getInstance()->summarizeByRatio($text, $ratio);
    }

    /**
     * Extract key sentences with scores
     *
     * @return array<int, array{sentence: string, score: float, position: int}>
     */
    public static function keySentences(string $text, int $count = 5): array
    {
        return self::getInstance()->extractKeySentences($text, $count);
    }

    /**
     * Score all sentences in text
     *
     * @return array<int, array{sentence: string, score: float}>
     */
    public static function scoreSentences(string $text): array
    {
        return self::getInstance()->scoreSentences($text);
    }

    /**
     * Extract keywords from text
     *
     * @return array<string, float>
     */
    public static function keywords(string $text, int $count = 10): array
    {
        return self::getInstance()->extractKeywords($text, $count);
    }

    /**
     * Split text into sentences
     *
     * @return array<string>
     */
    public static function sentences(string $text): array
    {
        return self::getInstance()->splitSentences($text);
    }

    /**
     * Get text statistics
     *
     * @return array<string, mixed>
     */
    public static function stats(string $text): array
    {
        return self::getInstance()->getStatistics($text);
    }

    /**
     * Generate headline from text
     */
    public static function headline(string $text, int $maxLength = 100): string
    {
        return self::getInstance()->generateHeadline($text, $maxLength);
    }

    /**
     * Quick summarize with default settings
     */
    public static function quick(string $text): string
    {
        return self::getInstance()->summarize($text, Config::DEFAULT_SENTENCES);
    }

    /**
     * Get a brief summary (one sentence)
     */
    public static function brief(string $text): string
    {
        return self::getInstance()->summarize($text, 1);
    }

    /**
     * Static method handler
     *
     * @param array<mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return self::getInstance()->{$name}(...$arguments);
    }
}
