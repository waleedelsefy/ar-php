<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\WordFrequency\Contracts;

/**
 * WordFrequency Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\WordFrequency
 */
interface WordFrequencyInterface
{
    /**
     * Count word frequencies
     *
     * @return array<string, int>
     */
    public function count(string $text): array;

    /**
     * Get top N frequent words
     *
     * @return array<string, int>
     */
    public function topWords(string $text, int $limit = 10): array;

    /**
     * Get word count
     */
    public function wordCount(string $text): int;

    /**
     * Get unique word count
     */
    public function uniqueWordCount(string $text): int;

    /**
     * Get character count
     */
    public function characterCount(string $text, bool $includeSpaces = false): int;

    /**
     * Get sentence count
     */
    public function sentenceCount(string $text): int;

    /**
     * Calculate text statistics
     *
     * @return array{words: int, unique_words: int, characters: int, sentences: int, avg_word_length: float}
     */
    public function statistics(string $text): array;

    /**
     * Get word frequency percentage
     *
     * @return array<string, float>
     */
    public function frequencyPercent(string $text): array;
}
