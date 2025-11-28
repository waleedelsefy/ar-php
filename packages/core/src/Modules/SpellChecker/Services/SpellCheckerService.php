<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\SpellChecker\Services;

use ArPHP\Core\Modules\SpellChecker\Config;
use ArPHP\Core\Modules\SpellChecker\Contracts\SpellCheckerInterface;

/**
 * SpellChecker Service - PHP 8.4
 *
 * Arabic spell checking with suggestion support.
 *
 * @package ArPHP\Core\Modules\SpellChecker
 */
final class SpellCheckerService implements SpellCheckerInterface
{
    /** @var array<string, bool> Dictionary as hash map for O(1) lookup */
    private array $dictionary = [];

    /** @var array<string, string> Common corrections map */
    private array $corrections = [];

    public function __construct()
    {
        $this->loadDefaultDictionary();
        $this->loadCorrections();
    }

    /**
     * Load default dictionary
     */
    private function loadDefaultDictionary(): void
    {
        foreach (Config::COMMON_WORDS as $word) {
            $normalized = $this->normalizeWord($word);
            $this->dictionary[$normalized] = true;
        }
    }

    /**
     * Load common corrections
     */
    private function loadCorrections(): void
    {
        foreach (Config::COMMON_CORRECTIONS as $wrong => $correct) {
            $this->corrections[$this->normalizeWord($wrong)] = $correct;
        }
    }

    /**
     * Normalize word for comparison
     */
    private function normalizeWord(string $word): string
    {
        // Remove diacritics
        $word = \str_replace(Config::DIACRITICS, '', $word);

        // Normalize Alef
        $word = \str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $word);

        // Trim
        return \trim($word);
    }

    /**
     * @inheritDoc
     */
    public function check(string $word): bool
    {
        $word = \trim($word);

        // Skip very short or long words
        if (\mb_strlen($word) < Config::MIN_WORD_LENGTH || \mb_strlen($word) > Config::MAX_WORD_LENGTH) {
            return true;
        }

        // Check if it's a number
        if (\is_numeric($word)) {
            return true;
        }

        $normalized = $this->normalizeWord($word);

        return isset($this->dictionary[$normalized]);
    }

    /**
     * @inheritDoc
     */
    public function suggest(string $word, int $limit = 5): array
    {
        $word = \trim($word);
        $normalized = $this->normalizeWord($word);

        // Check for known correction first
        if (isset($this->corrections[$normalized])) {
            return [$this->corrections[$normalized]];
        }

        $suggestions = [];
        $threshold = Config::DEFAULT_THRESHOLD;

        // Find similar words
        foreach (\array_keys($this->dictionary) as $dictWord) {
            $distance = $this->editDistance($normalized, $dictWord);

            if ($distance <= $threshold && $distance > 0) {
                $suggestions[$dictWord] = $distance;
            }
        }

        // Sort by distance
        \asort($suggestions);

        // Return limited suggestions
        return \array_slice(\array_keys($suggestions), 0, $limit);
    }

    /**
     * @inheritDoc
     */
    public function checkText(string $text): array
    {
        $errors = [];
        $words = $this->tokenize($text);
        $position = 0;

        foreach ($words as $word) {
            if (!$this->check($word)) {
                $errors[] = [
                    'word' => $word,
                    'position' => \mb_strpos($text, $word, $position) ?: $position,
                    'suggestions' => $this->suggest($word),
                ];
            }
            $position += \mb_strlen($word) + 1;
        }

        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function addWord(string $word): void
    {
        $normalized = $this->normalizeWord($word);
        $this->dictionary[$normalized] = true;
    }

    /**
     * @inheritDoc
     */
    public function addWords(array $words): void
    {
        foreach ($words as $word) {
            $this->addWord($word);
        }
    }

    /**
     * @inheritDoc
     */
    public function removeWord(string $word): void
    {
        $normalized = $this->normalizeWord($word);
        unset($this->dictionary[$normalized]);
    }

    /**
     * @inheritDoc
     */
    public function exists(string $word): bool
    {
        $normalized = $this->normalizeWord($word);

        return isset($this->dictionary[$normalized]);
    }

    /**
     * @inheritDoc
     */
    public function getDictionarySize(): int
    {
        return \count($this->dictionary);
    }

    /**
     * @inheritDoc
     *
     * Levenshtein distance implementation with Unicode support
     */
    public function editDistance(string $word1, string $word2): int
    {
        $chars1 = \preg_split('//u', $word1, -1, \PREG_SPLIT_NO_EMPTY) ?: [];
        $chars2 = \preg_split('//u', $word2, -1, \PREG_SPLIT_NO_EMPTY) ?: [];

        $len1 = \count($chars1);
        $len2 = \count($chars2);

        // Early return for empty strings
        if ($len1 === 0) {
            return $len2;
        }
        if ($len2 === 0) {
            return $len1;
        }

        // Create distance matrix
        $matrix = [];

        for ($i = 0; $i <= $len1; $i++) {
            $matrix[$i][0] = $i;
        }

        for ($j = 0; $j <= $len2; $j++) {
            $matrix[0][$j] = $j;
        }

        // Fill in the matrix
        for ($i = 1; $i <= $len1; $i++) {
            for ($j = 1; $j <= $len2; $j++) {
                $cost = $chars1[$i - 1] === $chars2[$j - 1] ? 0 : 1;

                // Check for confusable pairs (reduced cost)
                if ($cost === 1) {
                    foreach (Config::CONFUSABLE_PAIRS as $pair) {
                        if (
                            ($chars1[$i - 1] === $pair[0] && $chars2[$j - 1] === $pair[1]) ||
                            ($chars1[$i - 1] === $pair[1] && $chars2[$j - 1] === $pair[0])
                        ) {
                            $cost = 0.5; // Half cost for confusable pairs
                            break;
                        }
                    }
                }

                $matrix[$i][$j] = (int) \min(
                    $matrix[$i - 1][$j] + 1,      // deletion
                    $matrix[$i][$j - 1] + 1,      // insertion
                    $matrix[$i - 1][$j - 1] + $cost // substitution
                );
            }
        }

        return $matrix[$len1][$len2];
    }

    /**
     * Tokenize text into words
     *
     * @return array<string>
     */
    private function tokenize(string $text): array
    {
        $words = \preg_split('/[\s،؛:.!?؟\-\(\)\[\]«»"]+/u', $text, -1, \PREG_SPLIT_NO_EMPTY);

        return $words !== false ? $words : [];
    }

    /**
     * Auto-correct text
     */
    public function autoCorrect(string $text): string
    {
        $words = $this->tokenize($text);
        $result = $text;

        foreach ($words as $word) {
            $normalized = $this->normalizeWord($word);

            // Check known corrections first
            if (isset($this->corrections[$normalized])) {
                $result = \str_replace($word, $this->corrections[$normalized], $result);
                continue;
            }

            // Auto-correct if there's a close match
            if (!$this->check($word)) {
                $suggestions = $this->suggest($word, 1);
                if (!empty($suggestions)) {
                    // Only auto-correct if distance is 1
                    $suggestion = $suggestions[0];
                    if ($this->editDistance($normalized, $suggestion) === 1) {
                        $result = \str_replace($word, $suggestion, $result);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get words by frequency in text
     *
     * @return array<string, int>
     */
    public function getWordFrequency(string $text): array
    {
        $words = $this->tokenize($text);
        $frequency = [];

        foreach ($words as $word) {
            $normalized = $this->normalizeWord($word);
            $frequency[$normalized] = ($frequency[$normalized] ?? 0) + 1;
        }

        \arsort($frequency);

        return $frequency;
    }

    /**
     * Get unknown words from text
     *
     * @return array<string>
     */
    public function getUnknownWords(string $text): array
    {
        $words = $this->tokenize($text);
        $unknown = [];

        foreach ($words as $word) {
            if (!$this->check($word)) {
                $normalized = $this->normalizeWord($word);
                $unknown[$normalized] = $word;
            }
        }

        return \array_values($unknown);
    }

    /**
     * Add custom correction
     */
    public function addCorrection(string $wrong, string $correct): void
    {
        $normalized = $this->normalizeWord($wrong);
        $this->corrections[$normalized] = $correct;
    }

    /**
     * Generate phonetic variations
     *
     * @return array<string>
     */
    public function getPhoneticVariations(string $word): array
    {
        $variations = [$word];
        $chars = \preg_split('//u', $word, -1, \PREG_SPLIT_NO_EMPTY) ?: [];

        foreach (Config::CONFUSABLE_PAIRS as $pair) {
            foreach ($chars as $i => $char) {
                if ($char === $pair[0]) {
                    $newChars = $chars;
                    $newChars[$i] = $pair[1];
                    $variations[] = \implode('', $newChars);
                } elseif ($char === $pair[1]) {
                    $newChars = $chars;
                    $newChars[$i] = $pair[0];
                    $variations[] = \implode('', $newChars);
                }
            }
        }

        return \array_unique($variations);
    }

    /**
     * Calculate similarity ratio (0.0 to 1.0)
     */
    public function similarity(string $word1, string $word2): float
    {
        $maxLen = \max(\mb_strlen($word1), \mb_strlen($word2));

        if ($maxLen === 0) {
            return 1.0;
        }

        $distance = $this->editDistance($word1, $word2);

        return 1.0 - ($distance / $maxLen);
    }
}
