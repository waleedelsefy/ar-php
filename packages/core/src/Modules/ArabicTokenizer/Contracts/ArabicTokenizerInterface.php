<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicTokenizer\Contracts;

/**
 * Arabic Tokenizer Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\ArabicTokenizer
 */
interface ArabicTokenizerInterface
{
    /**
     * Tokenize text into words
     *
     * @return array<string>
     */
    public function tokenize(string $text): array;

    /**
     * Tokenize into sentences
     *
     * @return array<string>
     */
    public function sentences(string $text): array;

    /**
     * Tokenize into paragraphs
     *
     * @return array<string>
     */
    public function paragraphs(string $text): array;

    /**
     * Get word count
     */
    public function wordCount(string $text): int;

    /**
     * Get character count (excluding whitespace)
     */
    public function charCount(string $text, bool $includeSpaces = false): int;

    /**
     * Get sentence count
     */
    public function sentenceCount(string $text): int;

    /**
     * Tokenize with positions
     *
     * @return array<array{token: string, start: int, end: int, type: string}>
     */
    public function tokenizeWithPositions(string $text): array;

    /**
     * Extract n-grams
     *
     * @return array<string>
     */
    public function ngrams(string $text, int $n = 2): array;

    /**
     * Get word frequency distribution
     *
     * @return array<string, int>
     */
    public function wordFrequency(string $text): array;

    /**
     * Check if string is a single word
     */
    public function isWord(string $text): bool;

    /**
     * Split by pattern
     *
     * @return array<string>
     */
    public function splitBy(string $text, string $pattern): array;
}
