<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\SpellChecker\Contracts;

/**
 * SpellChecker Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\SpellChecker
 */
interface SpellCheckerInterface
{
    /**
     * Check if word is spelled correctly
     */
    public function check(string $word): bool;

    /**
     * Get spelling suggestions for a word
     *
     * @return array<string>
     */
    public function suggest(string $word, int $limit = 5): array;

    /**
     * Check text and return errors
     *
     * @return array<array{word: string, position: int, suggestions: array<string>}>
     */
    public function checkText(string $text): array;

    /**
     * Add word to dictionary
     */
    public function addWord(string $word): void;

    /**
     * Add multiple words to dictionary
     *
     * @param array<string> $words
     */
    public function addWords(array $words): void;

    /**
     * Remove word from dictionary
     */
    public function removeWord(string $word): void;

    /**
     * Check if word exists in dictionary
     */
    public function exists(string $word): bool;

    /**
     * Get dictionary size
     */
    public function getDictionarySize(): int;

    /**
     * Calculate edit distance between two words
     */
    public function editDistance(string $word1, string $word2): int;
}
