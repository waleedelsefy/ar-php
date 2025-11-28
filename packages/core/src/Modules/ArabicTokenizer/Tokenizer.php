<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicTokenizer;

/**
 * Tokenizer Facade - PHP 8.4
 *
 * Static facade for easy access to tokenization functionality.
 *
 * Usage:
 *   use ArPHP\Core\Modules\ArabicTokenizer\Tokenizer;
 *
 *   $words = Tokenizer::tokenize('مرحبا بالعالم');
 *   $count = Tokenizer::wordCount('مرحبا بالعالم');
 *   $sentences = Tokenizer::sentences($text);
 *
 * @package ArPHP\Core\Modules\ArabicTokenizer
 */
final class Tokenizer
{
    private static ?ArabicTokenizerModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): ArabicTokenizerModule
    {
        if (self::$instance === null) {
            self::$instance = new ArabicTokenizerModule();
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
     * Tokenize text into words
     *
     * Example:
     *   Tokenizer::tokenize('مرحبا بالعالم')
     *   // ['مرحبا', 'بالعالم']
     *
     * @return array<string>
     */
    public static function tokenize(string $text): array
    {
        return self::getInstance()->tokenize($text);
    }

    /**
     * Alias for tokenize
     *
     * @return array<string>
     */
    public static function words(string $text): array
    {
        return self::tokenize($text);
    }

    /**
     * Tokenize into sentences
     *
     * Example:
     *   Tokenizer::sentences('مرحبا. كيف حالك؟')
     *   // ['مرحبا.', 'كيف حالك؟']
     *
     * @return array<string>
     */
    public static function sentences(string $text): array
    {
        return self::getInstance()->sentences($text);
    }

    /**
     * Tokenize into paragraphs
     *
     * @return array<string>
     */
    public static function paragraphs(string $text): array
    {
        return self::getInstance()->paragraphs($text);
    }

    /**
     * Get word count
     *
     * Example:
     *   Tokenizer::wordCount('مرحبا بالعالم') // 2
     */
    public static function wordCount(string $text): int
    {
        return self::getInstance()->wordCount($text);
    }

    /**
     * Alias for wordCount
     */
    public static function countWords(string $text): int
    {
        return self::wordCount($text);
    }

    /**
     * Get character count
     */
    public static function charCount(string $text, bool $includeSpaces = false): int
    {
        return self::getInstance()->charCount($text, $includeSpaces);
    }

    /**
     * Alias for charCount
     */
    public static function countChars(string $text, bool $includeSpaces = false): int
    {
        return self::charCount($text, $includeSpaces);
    }

    /**
     * Get sentence count
     */
    public static function sentenceCount(string $text): int
    {
        return self::getInstance()->sentenceCount($text);
    }

    /**
     * Tokenize with positions
     *
     * @return array<array{token: string, start: int, end: int, type: string}>
     */
    public static function tokenizeWithPositions(string $text): array
    {
        return self::getInstance()->tokenizeWithPositions($text);
    }

    /**
     * Extract n-grams
     *
     * Example:
     *   Tokenizer::ngrams('أحمد يحب القراءة', 2)
     *   // ['أحمد يحب', 'يحب القراءة']
     *
     * @return array<string>
     */
    public static function ngrams(string $text, int $n = 2): array
    {
        return self::getInstance()->ngrams($text, $n);
    }

    /**
     * Extract bigrams (2-grams)
     *
     * @return array<string>
     */
    public static function bigrams(string $text): array
    {
        return self::ngrams($text, 2);
    }

    /**
     * Extract trigrams (3-grams)
     *
     * @return array<string>
     */
    public static function trigrams(string $text): array
    {
        return self::ngrams($text, 3);
    }

    /**
     * Get word frequency distribution
     *
     * @return array<string, int>
     */
    public static function wordFrequency(string $text): array
    {
        return self::getInstance()->wordFrequency($text);
    }

    /**
     * Check if string is a single word
     */
    public static function isWord(string $text): bool
    {
        return self::getInstance()->isWord($text);
    }

    /**
     * Get unique words
     *
     * @return array<string>
     */
    public static function uniqueWords(string $text): array
    {
        /** @var \ArPHP\Core\Modules\ArabicTokenizer\Services\ArabicTokenizerService $service */
        $service = self::getInstance()->getService();

        return $service->uniqueWords($text);
    }

    /**
     * Get character frequency
     *
     * @return array<string, int>
     */
    public static function charFrequency(string $text): array
    {
        /** @var \ArPHP\Core\Modules\ArabicTokenizer\Services\ArabicTokenizerService $service */
        $service = self::getInstance()->getService();

        return $service->charFrequency($text);
    }

    /**
     * Get text statistics
     *
     * @return array{
     *     words: int,
     *     chars: int,
     *     chars_no_space: int,
     *     sentences: int,
     *     paragraphs: int,
     *     unique_words: int,
     *     avg_word_length: float
     * }
     */
    public static function stats(string $text): array
    {
        /** @var \ArPHP\Core\Modules\ArabicTokenizer\Services\ArabicTokenizerService $service */
        $service = self::getInstance()->getService();

        return $service->getStats($text);
    }

    /**
     * Split by custom pattern
     *
     * @return array<string>
     */
    public static function splitBy(string $text, string $pattern): array
    {
        return self::getInstance()->getService()->splitBy($text, $pattern);
    }

    /**
     * Get first N words
     *
     * @return array<string>
     */
    public static function firstWords(string $text, int $n): array
    {
        $words = self::tokenize($text);

        return \array_slice($words, 0, $n);
    }

    /**
     * Get last N words
     *
     * @return array<string>
     */
    public static function lastWords(string $text, int $n): array
    {
        $words = self::tokenize($text);

        return \array_slice($words, -$n);
    }

    /**
     * Truncate text to N words
     */
    public static function truncateWords(string $text, int $n, string $suffix = '...'): string
    {
        $words = self::tokenize($text);

        if (\count($words) <= $n) {
            return $text;
        }

        return \implode(' ', \array_slice($words, 0, $n)) . $suffix;
    }
}
