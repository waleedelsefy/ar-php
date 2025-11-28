<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicSoundex\Contracts;

/**
 * Interface for Arabic Soundex operations
 *
 * @package ArPHP\Core\Modules\ArabicSoundex\Contracts
 */
interface ArabicSoundexInterface
{
    /**
     * Generate Arabic Soundex code for a word
     */
    public function soundex(string $word): string;

    /**
     * Generate phonetic code using Arabic Metaphone algorithm
     */
    public function metaphone(string $word): string;

    /**
     * Check if two words sound similar
     */
    public function soundsLike(string $word1, string $word2): bool;

    /**
     * Get similarity score between two words (0-100)
     */
    public function similarity(string $word1, string $word2): int;

    /**
     * Find similar words from a list
     *
     * @param string $word Target word
     * @param array<string> $wordList List of words to search
     * @param int $threshold Minimum similarity score (0-100)
     * @return array<string, int> Words with their similarity scores
     */
    public function findSimilar(string $word, array $wordList, int $threshold = 70): array;

    /**
     * Generate all phonetic variants of a word
     *
     * @return array<string>
     */
    public function getPhoneticVariants(string $word): array;
}
