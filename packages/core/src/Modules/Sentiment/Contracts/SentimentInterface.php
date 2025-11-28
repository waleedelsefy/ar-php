<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Sentiment\Contracts;

/**
 * Sentiment Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Sentiment
 */
interface SentimentInterface
{
    /**
     * Analyze sentiment of text
     *
     * @return array{score: float, label: string, confidence: float}
     */
    public function analyze(string $text): array;

    /**
     * Get sentiment score (-1.0 to 1.0)
     */
    public function getScore(string $text): float;

    /**
     * Get sentiment label (positive, negative, neutral)
     */
    public function getLabel(string $text): string;

    /**
     * Check if text is positive
     */
    public function isPositive(string $text): bool;

    /**
     * Check if text is negative
     */
    public function isNegative(string $text): bool;

    /**
     * Check if text is neutral
     */
    public function isNeutral(string $text): bool;

    /**
     * Get detailed breakdown
     *
     * @return array{positive_words: array<string>, negative_words: array<string>, score: float}
     */
    public function getBreakdown(string $text): array;

    /**
     * Add custom positive words
     *
     * @param array<string> $words
     */
    public function addPositiveWords(array $words): void;

    /**
     * Add custom negative words
     *
     * @param array<string> $words
     */
    public function addNegativeWords(array $words): void;
}
