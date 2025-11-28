<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\WordFrequency;

/**
 * WordFrequency Facade - PHP 8.4
 *
 * Static facade for Arabic word frequency analysis.
 *
 * Usage:
 *   use ArPHP\Core\Modules\WordFrequency\WordFrequency;
 *
 *   $frequencies = WordFrequency::count($text);
 *   $top = WordFrequency::top($text, 10);
 *   $stats = WordFrequency::stats($text);
 *
 * @package ArPHP\Core\Modules\WordFrequency
 */
final class WordFrequency
{
    private static ?WordFrequencyModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): WordFrequencyModule
    {
        if (self::$instance === null) {
            self::$instance = new WordFrequencyModule();
            self::$instance->register();
        }

        return self::$instance;
    }

    /**
     * Reset the singleton instance
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Count word frequencies
     *
     * @return array<string, int>
     */
    public static function count(string $text): array
    {
        return self::getInstance()->count($text);
    }

    /**
     * Get top N most frequent words
     *
     * @return array<string, int>
     */
    public static function top(string $text, int $limit = 10): array
    {
        return self::getInstance()->topWords($text, $limit);
    }

    /**
     * Alias for top
     *
     * @return array<string, int>
     */
    public static function topWords(string $text, int $limit = 10): array
    {
        return self::top($text, $limit);
    }

    /**
     * Get word count
     */
    public static function words(string $text): int
    {
        return self::getInstance()->wordCount($text);
    }

    /**
     * Alias for words
     */
    public static function wordCount(string $text): int
    {
        return self::words($text);
    }

    /**
     * Get unique word count
     */
    public static function unique(string $text): int
    {
        return self::getInstance()->uniqueWordCount($text);
    }

    /**
     * Alias for unique
     */
    public static function uniqueWordCount(string $text): int
    {
        return self::unique($text);
    }

    /**
     * Get character count
     */
    public static function chars(string $text, bool $includeSpaces = false): int
    {
        return self::getInstance()->characterCount($text, $includeSpaces);
    }

    /**
     * Alias for chars
     */
    public static function characterCount(string $text, bool $includeSpaces = false): int
    {
        return self::chars($text, $includeSpaces);
    }

    /**
     * Get sentence count
     */
    public static function sentences(string $text): int
    {
        return self::getInstance()->sentenceCount($text);
    }

    /**
     * Alias for sentences
     */
    public static function sentenceCount(string $text): int
    {
        return self::sentences($text);
    }

    /**
     * Get text statistics
     *
     * @return array{words: int, unique_words: int, characters: int, sentences: int, avg_word_length: float}
     */
    public static function stats(string $text): array
    {
        return self::getInstance()->statistics($text);
    }

    /**
     * Alias for stats
     *
     * @return array{words: int, unique_words: int, characters: int, sentences: int, avg_word_length: float}
     */
    public static function statistics(string $text): array
    {
        return self::stats($text);
    }

    /**
     * Get frequency percentages
     *
     * @return array<string, float>
     */
    public static function percent(string $text): array
    {
        return self::getInstance()->frequencyPercent($text);
    }

    /**
     * Alias for percent
     *
     * @return array<string, float>
     */
    public static function frequencyPercent(string $text): array
    {
        return self::percent($text);
    }

    /**
     * Get n-grams
     *
     * @return array<string, int>
     */
    public static function ngrams(string $text, int $n = 2): array
    {
        /** @var \ArPHP\Core\Modules\WordFrequency\Services\WordFrequencyService $service */
        $service = self::getInstance()->getService();

        return $service->nGrams($text, $n);
    }

    /**
     * Get bigrams
     *
     * @return array<string, int>
     */
    public static function bigrams(string $text): array
    {
        /** @var \ArPHP\Core\Modules\WordFrequency\Services\WordFrequencyService $service */
        $service = self::getInstance()->getService();

        return $service->bigrams($text);
    }

    /**
     * Get trigrams
     *
     * @return array<string, int>
     */
    public static function trigrams(string $text): array
    {
        /** @var \ArPHP\Core\Modules\WordFrequency\Services\WordFrequencyService $service */
        $service = self::getInstance()->getService();

        return $service->trigrams($text);
    }

    /**
     * Get character frequency
     *
     * @return array<string, int>
     */
    public static function charFrequency(string $text): array
    {
        /** @var \ArPHP\Core\Modules\WordFrequency\Services\WordFrequencyService $service */
        $service = self::getInstance()->getService();

        return $service->characterFrequency($text);
    }

    /**
     * Get word length distribution
     *
     * @return array<int, int>
     */
    public static function lengthDistribution(string $text): array
    {
        /** @var \ArPHP\Core\Modules\WordFrequency\Services\WordFrequencyService $service */
        $service = self::getInstance()->getService();

        return $service->wordLengthDistribution($text);
    }

    /**
     * Get hapax legomena (words appearing once)
     *
     * @return array<string>
     */
    public static function hapax(string $text): array
    {
        /** @var \ArPHP\Core\Modules\WordFrequency\Services\WordFrequencyService $service */
        $service = self::getInstance()->getService();

        return $service->hapaxLegomena($text);
    }

    /**
     * Get type-token ratio
     */
    public static function ttr(string $text): float
    {
        /** @var \ArPHP\Core\Modules\WordFrequency\Services\WordFrequencyService $service */
        $service = self::getInstance()->getService();

        return $service->typeTokenRatio($text);
    }

    /**
     * Get readability score
     */
    public static function readability(string $text): float
    {
        /** @var \ArPHP\Core\Modules\WordFrequency\Services\WordFrequencyService $service */
        $service = self::getInstance()->getService();

        return $service->readabilityScore($text);
    }

    /**
     * Compare two texts
     *
     * @return array{common: array<string>, only_in_first: array<string>, only_in_second: array<string>}
     */
    public static function compare(string $text1, string $text2): array
    {
        /** @var \ArPHP\Core\Modules\WordFrequency\Services\WordFrequencyService $service */
        $service = self::getInstance()->getService();

        return $service->compare($text1, $text2);
    }

    /**
     * Get top words excluding stopwords
     *
     * @param array<string> $stopwords
     * @return array<string, int>
     */
    public static function topExcluding(string $text, array $stopwords, int $limit = 10): array
    {
        /** @var \ArPHP\Core\Modules\WordFrequency\Services\WordFrequencyService $service */
        $service = self::getInstance()->getService();

        return $service->topWordsExcluding($text, $stopwords, $limit);
    }
}
