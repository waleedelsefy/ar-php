<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Stopwords\Contracts;

/**
 * Stopwords Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Stopwords
 */
interface StopwordsInterface
{
    /**
     * Check if a word is a stopword
     */
    public function isStopword(string $word): bool;

    /**
     * Remove stopwords from text
     */
    public function removeStopwords(string $text): string;

    /**
     * Remove stopwords from array of words
     *
     * @param array<string> $words
     * @return array<string>
     */
    public function filterStopwords(array $words): array;

    /**
     * Get all stopwords
     *
     * @return array<string>
     */
    public function getStopwords(): array;

    /**
     * Add custom stopwords
     *
     * @param array<string> $words
     */
    public function addStopwords(array $words): void;

    /**
     * Remove words from stoplist
     *
     * @param array<string> $words
     */
    public function removeFromList(array $words): void;

    /**
     * Reset to default stopwords
     */
    public function reset(): void;

    /**
     * Get stopwords by category
     *
     * @return array<string>
     */
    public function getByCategory(string $category): array;

    /**
     * Count stopwords in text
     */
    public function countStopwords(string $text): int;
}
