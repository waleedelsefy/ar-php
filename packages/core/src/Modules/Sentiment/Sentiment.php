<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Sentiment;

/**
 * Sentiment Facade - PHP 8.4
 *
 * Static facade for Arabic sentiment analysis.
 *
 * Usage:
 *   use ArPHP\Core\Modules\Sentiment\Sentiment;
 *
 *   $result = Sentiment::analyze('هذا منتج رائع ومميز');
 *   $score = Sentiment::score('أنا سعيد جداً');
 *   $isPositive = Sentiment::isPositive($text);
 *
 * @package ArPHP\Core\Modules\Sentiment
 */
final class Sentiment
{
    private static ?SentimentModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): SentimentModule
    {
        if (self::$instance === null) {
            self::$instance = new SentimentModule();
            self::$instance->register();
        }

        return self::$instance;
    }

    /**
     * Reset the singleton instance
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Analyze sentiment
     *
     * Example:
     *   Sentiment::analyze('هذا منتج رائع ومميز')
     *   // ['score' => 0.85, 'label' => 'positive', 'confidence' => 0.7]
     *
     * @return array{score: float, label: string, confidence: float}
     */
    public static function analyze(string $text): array
    {
        return self::getInstance()->analyze($text);
    }

    /**
     * Get sentiment score (-1.0 to 1.0)
     *
     * Example:
     *   Sentiment::score('أنا سعيد جداً') // 0.9
     *   Sentiment::score('هذا سيء للغاية') // -0.9
     */
    public static function score(string $text): float
    {
        return self::getInstance()->getScore($text);
    }

    /**
     * Alias for score
     */
    public static function getScore(string $text): float
    {
        return self::score($text);
    }

    /**
     * Get sentiment label
     *
     * Example:
     *   Sentiment::label('هذا جميل') // 'positive'
     */
    public static function label(string $text): string
    {
        return self::getInstance()->getLabel($text);
    }

    /**
     * Alias for label
     */
    public static function getLabel(string $text): string
    {
        return self::label($text);
    }

    /**
     * Check if sentiment is positive
     *
     * Example:
     *   Sentiment::isPositive('أحب هذا المكان') // true
     */
    public static function isPositive(string $text): bool
    {
        return self::getInstance()->isPositive($text);
    }

    /**
     * Check if sentiment is negative
     *
     * Example:
     *   Sentiment::isNegative('هذا فظيع') // true
     */
    public static function isNegative(string $text): bool
    {
        return self::getInstance()->isNegative($text);
    }

    /**
     * Check if sentiment is neutral
     *
     * Example:
     *   Sentiment::isNeutral('الجو معتدل اليوم') // true
     */
    public static function isNeutral(string $text): bool
    {
        return self::getInstance()->isNeutral($text);
    }

    /**
     * Get sentiment breakdown
     *
     * @return array{positive_words: array<string>, negative_words: array<string>, score: float}
     */
    public static function breakdown(string $text): array
    {
        return self::getInstance()->getBreakdown($text);
    }

    /**
     * Alias for breakdown
     *
     * @return array{positive_words: array<string>, negative_words: array<string>, score: float}
     */
    public static function getBreakdown(string $text): array
    {
        return self::breakdown($text);
    }

    /**
     * Add custom positive words
     *
     * @param array<string>|array<string, float> $words
     */
    public static function addPositive(array $words): void
    {
        self::getInstance()->addPositiveWords($words);
    }

    /**
     * Add custom negative words
     *
     * @param array<string>|array<string, float> $words
     */
    public static function addNegative(array $words): void
    {
        self::getInstance()->addNegativeWords($words);
    }

    /**
     * Analyze by sentences
     *
     * @return array<array{sentence: string, score: float, label: string}>
     */
    public static function analyzeBySentence(string $text): array
    {
        /** @var \ArPHP\Core\Modules\Sentiment\Services\SentimentService $service */
        $service = self::getInstance()->getService();

        return $service->analyzeBySentence($text);
    }

    /**
     * Compare sentiment of two texts
     *
     * @return array{text1: array{score: float, label: string}, text2: array{score: float, label: string}, comparison: string}
     */
    public static function compare(string $text1, string $text2): array
    {
        /** @var \ArPHP\Core\Modules\Sentiment\Services\SentimentService $service */
        $service = self::getInstance()->getService();

        return $service->compare($text1, $text2);
    }

    /**
     * Get statistics
     *
     * @return array{score: float, label: string, positive_count: int, negative_count: int, neutral_ratio: float}
     */
    public static function stats(string $text): array
    {
        /** @var \ArPHP\Core\Modules\Sentiment\Services\SentimentService $service */
        $service = self::getInstance()->getService();

        return $service->getStatistics($text);
    }

    /**
     * Quick positive check
     */
    public static function positive(string $text): bool
    {
        return self::isPositive($text);
    }

    /**
     * Quick negative check
     */
    public static function negative(string $text): bool
    {
        return self::isNegative($text);
    }

    /**
     * Quick neutral check
     */
    public static function neutral(string $text): bool
    {
        return self::isNeutral($text);
    }
}
