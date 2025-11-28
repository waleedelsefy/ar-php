<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\SpellChecker;

/**
 * SpellChecker Facade - PHP 8.4
 *
 * Static facade for Arabic spell checking.
 *
 * Usage:
 *   use ArPHP\Core\Modules\SpellChecker\SpellChecker;
 *
 *   $isCorrect = SpellChecker::check('كتاب');
 *   $suggestions = SpellChecker::suggest('كتب');
 *   $errors = SpellChecker::checkText($text);
 *
 * @package ArPHP\Core\Modules\SpellChecker
 */
final class SpellChecker
{
    private static ?SpellCheckerModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): SpellCheckerModule
    {
        if (self::$instance === null) {
            self::$instance = new SpellCheckerModule();
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
     * Check if word is spelled correctly
     *
     * Example:
     *   SpellChecker::check('كتاب') // true
     *   SpellChecker::check('كتب') // true
     */
    public static function check(string $word): bool
    {
        return self::getInstance()->check($word);
    }

    /**
     * Alias for check
     */
    public static function isCorrect(string $word): bool
    {
        return self::check($word);
    }

    /**
     * Get spelling suggestions
     *
     * @return array<string>
     */
    public static function suggest(string $word, int $limit = 5): array
    {
        return self::getInstance()->suggest($word, $limit);
    }

    /**
     * Alias for suggest
     *
     * @return array<string>
     */
    public static function getSuggestions(string $word, int $limit = 5): array
    {
        return self::suggest($word, $limit);
    }

    /**
     * Check text for spelling errors
     *
     * @return array<array{word: string, position: int, suggestions: array<string>}>
     */
    public static function checkText(string $text): array
    {
        return self::getInstance()->checkText($text);
    }

    /**
     * Get errors from text
     *
     * @return array<array{word: string, position: int, suggestions: array<string>}>
     */
    public static function getErrors(string $text): array
    {
        return self::checkText($text);
    }

    /**
     * Add word to dictionary
     *
     * Example:
     *   SpellChecker::addWord('محمد');
     */
    public static function addWord(string $word): void
    {
        self::getInstance()->addWord($word);
    }

    /**
     * Add multiple words to dictionary
     *
     * @param array<string> $words
     */
    public static function addWords(array $words): void
    {
        self::getInstance()->addWords($words);
    }

    /**
     * Remove word from dictionary
     */
    public static function removeWord(string $word): void
    {
        self::getInstance()->removeWord($word);
    }

    /**
     * Check if word exists in dictionary
     */
    public static function exists(string $word): bool
    {
        return self::getInstance()->exists($word);
    }

    /**
     * Alias for exists
     */
    public static function inDictionary(string $word): bool
    {
        return self::exists($word);
    }

    /**
     * Get dictionary size
     */
    public static function dictionarySize(): int
    {
        return self::getInstance()->getDictionarySize();
    }

    /**
     * Calculate edit distance between words
     */
    public static function distance(string $word1, string $word2): int
    {
        return self::getInstance()->editDistance($word1, $word2);
    }

    /**
     * Alias for distance
     */
    public static function editDistance(string $word1, string $word2): int
    {
        return self::distance($word1, $word2);
    }

    /**
     * Auto-correct text
     */
    public static function autoCorrect(string $text): string
    {
        /** @var \ArPHP\Core\Modules\SpellChecker\Services\SpellCheckerService $service */
        $service = self::getInstance()->getService();

        return $service->autoCorrect($text);
    }

    /**
     * Alias for autoCorrect
     */
    public static function correct(string $text): string
    {
        return self::autoCorrect($text);
    }

    /**
     * Get unknown words from text
     *
     * @return array<string>
     */
    public static function getUnknownWords(string $text): array
    {
        /** @var \ArPHP\Core\Modules\SpellChecker\Services\SpellCheckerService $service */
        $service = self::getInstance()->getService();

        return $service->getUnknownWords($text);
    }

    /**
     * Add custom correction
     */
    public static function addCorrection(string $wrong, string $correct): void
    {
        /** @var \ArPHP\Core\Modules\SpellChecker\Services\SpellCheckerService $service */
        $service = self::getInstance()->getService();

        $service->addCorrection($wrong, $correct);
    }

    /**
     * Get phonetic variations
     *
     * @return array<string>
     */
    public static function getPhoneticVariations(string $word): array
    {
        /** @var \ArPHP\Core\Modules\SpellChecker\Services\SpellCheckerService $service */
        $service = self::getInstance()->getService();

        return $service->getPhoneticVariations($word);
    }

    /**
     * Calculate similarity ratio
     */
    public static function similarity(string $word1, string $word2): float
    {
        /** @var \ArPHP\Core\Modules\SpellChecker\Services\SpellCheckerService $service */
        $service = self::getInstance()->getService();

        return $service->similarity($word1, $word2);
    }

    /**
     * Check if text has spelling errors
     */
    public static function hasErrors(string $text): bool
    {
        return \count(self::checkText($text)) > 0;
    }

    /**
     * Get error count in text
     */
    public static function errorCount(string $text): int
    {
        return \count(self::checkText($text));
    }
}
