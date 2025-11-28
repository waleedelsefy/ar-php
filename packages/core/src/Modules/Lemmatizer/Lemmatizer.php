<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Lemmatizer;

/**
 * Lemmatizer Facade - PHP 8.4
 *
 * Static facade for Arabic lemmatization.
 *
 * Usage:
 *   use ArPHP\Core\Modules\Lemmatizer\Lemmatizer;
 *
 *   $root = Lemmatizer::root('مكتبة');
 *   $lemma = Lemmatizer::lemmatize('الكتابات');
 *   $stem = Lemmatizer::stem('يكتبون');
 *
 * @package ArPHP\Core\Modules\Lemmatizer
 */
final class Lemmatizer
{
    private static ?LemmatizerModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): LemmatizerModule
    {
        if (self::$instance === null) {
            self::$instance = new LemmatizerModule();
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
     * Lemmatize a word
     *
     * Example:
     *   Lemmatizer::lemmatize('الكتابات') // 'كتاب'
     *   Lemmatizer::lemmatize('يكتبون') // 'كتب'
     */
    public static function lemmatize(string $word): string
    {
        return self::getInstance()->lemmatize($word);
    }

    /**
     * Lemmatize all words in text
     */
    public static function lemmatizeText(string $text): string
    {
        return self::getInstance()->lemmatizeText($text);
    }

    /**
     * Get word root (جذر)
     *
     * Example:
     *   Lemmatizer::root('مكتبة') // 'كتب'
     *   Lemmatizer::root('كاتب') // 'كتب'
     *   Lemmatizer::root('استكتب') // 'كتب'
     */
    public static function root(string $word): string
    {
        return self::getInstance()->getRoot($word);
    }

    /**
     * Alias for root
     */
    public static function getRoot(string $word): string
    {
        return self::root($word);
    }

    /**
     * Stem a word
     *
     * Example:
     *   Lemmatizer::stem('الكاتبون') // 'كاتب'
     */
    public static function stem(string $word): string
    {
        return self::getInstance()->stem($word);
    }

    /**
     * Remove prefix from word
     *
     * Example:
     *   Lemmatizer::removePrefix('والكتاب') // 'كتاب'
     */
    public static function removePrefix(string $word): string
    {
        return self::getInstance()->removePrefix($word);
    }

    /**
     * Remove suffix from word
     *
     * Example:
     *   Lemmatizer::removeSuffix('كتابهم') // 'كتاب'
     */
    public static function removeSuffix(string $word): string
    {
        return self::getInstance()->removeSuffix($word);
    }

    /**
     * Remove prefix and suffix
     *
     * Example:
     *   Lemmatizer::strip('والكتابات') // 'كتاب'
     */
    public static function strip(string $word): string
    {
        return self::getInstance()->removeAffixes($word);
    }

    /**
     * Alias for strip
     */
    public static function removeAffixes(string $word): string
    {
        return self::strip($word);
    }

    /**
     * Check if word has prefix
     *
     * Example:
     *   Lemmatizer::hasPrefix('والكتاب') // true
     */
    public static function hasPrefix(string $word): bool
    {
        return self::getInstance()->hasPrefix($word);
    }

    /**
     * Check if word has suffix
     *
     * Example:
     *   Lemmatizer::hasSuffix('كتابهم') // true
     */
    public static function hasSuffix(string $word): bool
    {
        return self::getInstance()->hasSuffix($word);
    }

    /**
     * Get word pattern (وزن)
     *
     * Example:
     *   Lemmatizer::pattern('كاتب') // 'فاعل'
     *   Lemmatizer::pattern('مكتوب') // 'مفعول'
     */
    public static function pattern(string $word): string
    {
        return self::getInstance()->getPattern($word);
    }

    /**
     * Alias for pattern
     */
    public static function getPattern(string $word): string
    {
        return self::pattern($word);
    }

    /**
     * Analyze word morphology
     *
     * @return array{original: string, normalized: string, prefix: string, stem: string, suffix: string, root: string, pattern: string}
     */
    public static function analyze(string $word): array
    {
        /** @var \ArPHP\Core\Modules\Lemmatizer\Services\LemmatizerService $service */
        $service = self::getInstance()->getService();

        return $service->analyze($word);
    }

    /**
     * Get possible roots
     *
     * @return array<string>
     */
    public static function possibleRoots(string $word): array
    {
        /** @var \ArPHP\Core\Modules\Lemmatizer\Services\LemmatizerService $service */
        $service = self::getInstance()->getService();

        return $service->getPossibleRoots($word);
    }

    /**
     * Check if two words share the same root
     *
     * Example:
     *   Lemmatizer::shareRoot('كتاب', 'مكتبة') // true
     */
    public static function shareRoot(string $word1, string $word2): bool
    {
        /** @var \ArPHP\Core\Modules\Lemmatizer\Services\LemmatizerService $service */
        $service = self::getInstance()->getService();

        return $service->shareRoot($word1, $word2);
    }

    /**
     * Find words with same root
     *
     * @param array<string> $words
     * @return array<string>
     */
    public static function findRelated(string $word, array $words): array
    {
        /** @var \ArPHP\Core\Modules\Lemmatizer\Services\LemmatizerService $service */
        $service = self::getInstance()->getService();

        return $service->findRelatedWords($word, $words);
    }

    /**
     * Get detected prefix
     */
    public static function getPrefix(string $word): string
    {
        /** @var \ArPHP\Core\Modules\Lemmatizer\Services\LemmatizerService $service */
        $service = self::getInstance()->getService();

        return $service->getPrefix($word);
    }

    /**
     * Get detected suffix
     */
    public static function getSuffix(string $word): string
    {
        /** @var \ArPHP\Core\Modules\Lemmatizer\Services\LemmatizerService $service */
        $service = self::getInstance()->getService();

        return $service->getSuffix($word);
    }
}
