<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\WordFrequency\Services;

use ArPHP\Core\Modules\WordFrequency\Contracts\WordFrequencyInterface;

/**
 * WordFrequency Service - PHP 8.4
 *
 * @package ArPHP\Core\Modules\WordFrequency
 */
final class WordFrequencyService implements WordFrequencyInterface
{
    /** @var array<string> Arabic diacritics */
    private const array DIACRITICS = ['ً', 'ٌ', 'ٍ', 'َ', 'ُ', 'ِ', 'ّ', 'ْ', 'ـ'];

    /**
     * @inheritDoc
     */
    public function count(string $text): array
    {
        $words = $this->tokenize($text);
        $frequencies = [];

        foreach ($words as $word) {
            $normalized = $this->normalizeWord($word);
            if (!empty($normalized)) {
                $frequencies[$normalized] = ($frequencies[$normalized] ?? 0) + 1;
            }
        }

        // Sort by frequency descending
        \arsort($frequencies);

        return $frequencies;
    }

    /**
     * @inheritDoc
     */
    public function topWords(string $text, int $limit = 10): array
    {
        $frequencies = $this->count($text);

        return \array_slice($frequencies, 0, $limit, true);
    }

    /**
     * @inheritDoc
     */
    public function wordCount(string $text): int
    {
        return \count($this->tokenize($text));
    }

    /**
     * @inheritDoc
     */
    public function uniqueWordCount(string $text): int
    {
        return \count($this->count($text));
    }

    /**
     * @inheritDoc
     */
    public function characterCount(string $text, bool $includeSpaces = false): int
    {
        if (!$includeSpaces) {
            $text = \preg_replace('/\s+/u', '', $text);
        }

        return \mb_strlen($text ?? '');
    }

    /**
     * @inheritDoc
     */
    public function sentenceCount(string $text): int
    {
        // Split by sentence terminators
        $sentences = \preg_split('/[.!?؟]+/u', $text, -1, \PREG_SPLIT_NO_EMPTY);

        return \count($sentences ?: []);
    }

    /**
     * @inheritDoc
     */
    public function statistics(string $text): array
    {
        $words = $this->tokenize($text);
        $wordCount = \count($words);
        $uniqueWords = \count($this->count($text));
        $characters = $this->characterCount($text);
        $sentences = $this->sentenceCount($text);

        // Calculate average word length
        $totalLength = 0;
        foreach ($words as $word) {
            $totalLength += \mb_strlen($word);
        }
        $avgWordLength = $wordCount > 0 ? $totalLength / $wordCount : 0.0;

        return [
            'words' => $wordCount,
            'unique_words' => $uniqueWords,
            'characters' => $characters,
            'sentences' => $sentences,
            'avg_word_length' => \round($avgWordLength, 2),
        ];
    }

    /**
     * @inheritDoc
     */
    public function frequencyPercent(string $text): array
    {
        $frequencies = $this->count($text);
        $total = \array_sum($frequencies);

        if ($total === 0) {
            return [];
        }

        $percentages = [];
        foreach ($frequencies as $word => $count) {
            $percentages[$word] = \round(($count / $total) * 100, 2);
        }

        return $percentages;
    }

    /**
     * Tokenize text into words
     *
     * @return array<string>
     */
    private function tokenize(string $text): array
    {
        // Split by whitespace and punctuation
        $words = \preg_split('/[\s،؛:.!?؟\-\(\)\[\]«»"\'\d]+/u', $text, -1, \PREG_SPLIT_NO_EMPTY);

        return $words !== false ? $words : [];
    }

    /**
     * Normalize word for comparison
     */
    private function normalizeWord(string $word): string
    {
        // Remove diacritics
        $word = \str_replace(self::DIACRITICS, '', $word);

        // Normalize Alef
        $word = \str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $word);

        // Normalize Ta Marbuta
        $word = \str_replace('ة', 'ه', $word);

        // Normalize Alef Maqsura
        $word = \str_replace('ى', 'ي', $word);

        return \trim($word);
    }

    /**
     * Get n-gram frequencies
     *
     * @return array<string, int>
     */
    public function nGrams(string $text, int $n = 2): array
    {
        $words = $this->tokenize($text);
        $ngrams = [];

        for ($i = 0; $i <= \count($words) - $n; $i++) {
            $gram = \implode(' ', \array_slice($words, $i, $n));
            $ngrams[$gram] = ($ngrams[$gram] ?? 0) + 1;
        }

        \arsort($ngrams);

        return $ngrams;
    }

    /**
     * Get bigram (2-gram) frequencies
     *
     * @return array<string, int>
     */
    public function bigrams(string $text): array
    {
        return $this->nGrams($text, 2);
    }

    /**
     * Get trigram (3-gram) frequencies
     *
     * @return array<string, int>
     */
    public function trigrams(string $text): array
    {
        return $this->nGrams($text, 3);
    }

    /**
     * Get character frequencies
     *
     * @return array<string, int>
     */
    public function characterFrequency(string $text): array
    {
        // Remove spaces
        $text = \preg_replace('/\s+/u', '', $text);
        $chars = \preg_split('//u', $text ?? '', -1, \PREG_SPLIT_NO_EMPTY);

        $frequencies = [];
        foreach ($chars ?: [] as $char) {
            $frequencies[$char] = ($frequencies[$char] ?? 0) + 1;
        }

        \arsort($frequencies);

        return $frequencies;
    }

    /**
     * Get word length distribution
     *
     * @return array<int, int> Length => Count
     */
    public function wordLengthDistribution(string $text): array
    {
        $words = $this->tokenize($text);
        $distribution = [];

        foreach ($words as $word) {
            $length = \mb_strlen($word);
            $distribution[$length] = ($distribution[$length] ?? 0) + 1;
        }

        \ksort($distribution);

        return $distribution;
    }

    /**
     * Get hapax legomena (words appearing only once)
     *
     * @return array<string>
     */
    public function hapaxLegomena(string $text): array
    {
        $frequencies = $this->count($text);

        return \array_keys(\array_filter($frequencies, fn($count) => $count === 1));
    }

    /**
     * Get type-token ratio (lexical diversity)
     */
    public function typeTokenRatio(string $text): float
    {
        $wordCount = $this->wordCount($text);

        if ($wordCount === 0) {
            return 0.0;
        }

        return \round($this->uniqueWordCount($text) / $wordCount, 4);
    }

    /**
     * Get readability score (simple estimate)
     */
    public function readabilityScore(string $text): float
    {
        $stats = $this->statistics($text);

        if ($stats['sentences'] === 0 || $stats['words'] === 0) {
            return 0.0;
        }

        // Simple readability formula
        $avgSentenceLength = $stats['words'] / $stats['sentences'];
        $avgWordLength = $stats['avg_word_length'];

        // Lower score = easier to read
        return \round($avgSentenceLength * 0.4 + $avgWordLength * 0.6, 2);
    }

    /**
     * Compare frequency distributions of two texts
     *
     * @return array{common: array<string>, only_in_first: array<string>, only_in_second: array<string>}
     */
    public function compare(string $text1, string $text2): array
    {
        $freq1 = $this->count($text1);
        $freq2 = $this->count($text2);

        $words1 = \array_keys($freq1);
        $words2 = \array_keys($freq2);

        return [
            'common' => \array_values(\array_intersect($words1, $words2)),
            'only_in_first' => \array_values(\array_diff($words1, $words2)),
            'only_in_second' => \array_values(\array_diff($words2, $words1)),
        ];
    }

    /**
     * Find most common words excluding stopwords
     *
     * @param array<string> $stopwords
     * @return array<string, int>
     */
    public function topWordsExcluding(string $text, array $stopwords, int $limit = 10): array
    {
        $frequencies = $this->count($text);

        // Normalize stopwords
        $normalizedStopwords = \array_map(fn($s) => $this->normalizeWord($s), $stopwords);

        // Filter out stopwords
        $filtered = \array_filter(
            $frequencies,
            fn($count, $word) => !\in_array($word, $normalizedStopwords, true),
            \ARRAY_FILTER_USE_BOTH
        );

        return \array_slice($filtered, 0, $limit, true);
    }
}
