<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Stopwords\Services;

use ArPHP\Core\Modules\Stopwords\Config;
use ArPHP\Core\Modules\Stopwords\Contracts\StopwordsInterface;
use ArPHP\Core\Modules\Stopwords\Exceptions\StopwordsException;

/**
 * Stopwords Service - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Stopwords
 */
final class StopwordsService implements StopwordsInterface
{
    /** @var array<string, bool> */
    private array $stopwords = [];

    /** @var array<string, bool> */
    private array $originalStopwords = [];

    public function __construct()
    {
        $this->loadDefaultStopwords();
    }

    /**
     * Load default stopwords
     */
    private function loadDefaultStopwords(): void
    {
        $words = Config::getAllDefault();

        foreach ($words as $word) {
            $normalized = $this->normalizeWord($word);
            $this->stopwords[$normalized] = true;
            $this->originalStopwords[$normalized] = true;
        }
    }

    /**
     * Normalize a word for comparison
     */
    private function normalizeWord(string $word): string
    {
        // Trim and lowercase
        $word = \trim($word);

        // Remove diacritics
        $diacritics = ['ً', 'ٌ', 'ٍ', 'َ', 'ُ', 'ِ', 'ّ', 'ْ', 'ـ'];
        $word = \str_replace($diacritics, '', $word);

        // Normalize Alef
        $word = \str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $word);

        // Normalize Ta Marbuta and Alef Maqsura
        $word = \str_replace('ة', 'ه', $word);
        $word = \str_replace('ى', 'ي', $word);

        return $word;
    }

    /**
     * @inheritDoc
     */
    public function isStopword(string $word): bool
    {
        $normalized = $this->normalizeWord($word);

        return isset($this->stopwords[$normalized]);
    }

    /**
     * @inheritDoc
     */
    public function removeStopwords(string $text): string
    {
        $words = $this->tokenize($text);
        $filtered = $this->filterStopwords($words);

        return \implode(' ', $filtered);
    }

    /**
     * @inheritDoc
     */
    public function filterStopwords(array $words): array
    {
        return \array_values(\array_filter($words, fn(string $word): bool => !$this->isStopword($word)));
    }

    /**
     * @inheritDoc
     */
    public function getStopwords(): array
    {
        return \array_keys($this->stopwords);
    }

    /**
     * @inheritDoc
     */
    public function addStopwords(array $words): void
    {
        if (empty($words)) {
            throw StopwordsException::emptyWordList();
        }

        foreach ($words as $word) {
            $normalized = $this->normalizeWord($word);
            $this->stopwords[$normalized] = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function removeFromList(array $words): void
    {
        foreach ($words as $word) {
            $normalized = $this->normalizeWord($word);
            unset($this->stopwords[$normalized]);
        }
    }

    /**
     * @inheritDoc
     */
    public function reset(): void
    {
        $this->stopwords = $this->originalStopwords;
    }

    /**
     * @inheritDoc
     */
    public function getByCategory(string $category): array
    {
        if (!\in_array($category, Config::VALID_CATEGORIES, true)) {
            throw StopwordsException::invalidCategory($category);
        }

        return Config::getByCategory($category);
    }

    /**
     * @inheritDoc
     */
    public function countStopwords(string $text): int
    {
        $words = $this->tokenize($text);
        $count = 0;

        foreach ($words as $word) {
            if ($this->isStopword($word)) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Tokenize text into words
     *
     * @return array<string>
     */
    private function tokenize(string $text): array
    {
        // Split by whitespace and punctuation
        $words = \preg_split('/[\s،؛:.!?؟\-]+/u', $text, -1, \PREG_SPLIT_NO_EMPTY);

        return $words !== false ? $words : [];
    }

    /**
     * Get stopword statistics for text
     *
     * @return array{total: int, stopwords: int, ratio: float}
     */
    public function getStatistics(string $text): array
    {
        $words = $this->tokenize($text);
        $total = \count($words);
        $stopwordCount = $this->countStopwords($text);

        return [
            'total' => $total,
            'stopwords' => $stopwordCount,
            'ratio' => $total > 0 ? $stopwordCount / $total : 0.0,
        ];
    }

    /**
     * Extract stopwords from text
     *
     * @return array<string>
     */
    public function extractStopwords(string $text): array
    {
        $words = $this->tokenize($text);

        return \array_values(\array_filter($words, fn(string $word): bool => $this->isStopword($word)));
    }

    /**
     * Extract non-stopwords (content words) from text
     *
     * @return array<string>
     */
    public function extractContentWords(string $text): array
    {
        return $this->filterStopwords($this->tokenize($text));
    }

    /**
     * Get word frequency excluding stopwords
     *
     * @return array<string, int>
     */
    public function getWordFrequency(string $text): array
    {
        $contentWords = $this->extractContentWords($text);
        $frequency = [];

        foreach ($contentWords as $word) {
            $normalized = $this->normalizeWord($word);
            $frequency[$normalized] = ($frequency[$normalized] ?? 0) + 1;
        }

        \arsort($frequency);

        return $frequency;
    }

    /**
     * Check if stopwords list contains all of given words
     *
     * @param array<string> $words
     */
    public function containsAll(array $words): bool
    {
        foreach ($words as $word) {
            if (!$this->isStopword($word)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if stopwords list contains any of given words
     *
     * @param array<string> $words
     */
    public function containsAny(array $words): bool
    {
        foreach ($words as $word) {
            if ($this->isStopword($word)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get count of stopwords
     */
    public function count(): int
    {
        return \count($this->stopwords);
    }
}
