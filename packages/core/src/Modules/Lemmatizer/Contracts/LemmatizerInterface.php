<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Lemmatizer\Contracts;

/**
 * Lemmatizer Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Lemmatizer
 */
interface LemmatizerInterface
{
    /**
     * Get the lemma (root/base form) of a word
     */
    public function lemmatize(string $word): string;

    /**
     * Lemmatize all words in text
     */
    public function lemmatizeText(string $text): string;

    /**
     * Get word root (جذر)
     */
    public function getRoot(string $word): string;

    /**
     * Get word stem
     */
    public function stem(string $word): string;

    /**
     * Remove prefix from word
     */
    public function removePrefix(string $word): string;

    /**
     * Remove suffix from word
     */
    public function removeSuffix(string $word): string;

    /**
     * Remove prefix and suffix
     */
    public function removeAffixes(string $word): string;

    /**
     * Check if word has prefix
     */
    public function hasPrefix(string $word): bool;

    /**
     * Check if word has suffix
     */
    public function hasSuffix(string $word): bool;

    /**
     * Get word pattern (وزن)
     */
    public function getPattern(string $word): string;
}
