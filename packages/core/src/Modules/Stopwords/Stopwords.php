<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Stopwords;

/**
 * Stopwords Facade - PHP 8.4
 *
 * Static facade for Arabic stopwords filtering.
 *
 * Usage:
 *   use ArPHP\Core\Modules\Stopwords\Stopwords;
 *
 *   $clean = Stopwords::remove('هذا هو النص الذي نريد تنظيفه');
 *   $isStop = Stopwords::isStopword('من');
 *   $content = Stopwords::extractContent($text);
 *
 * @package ArPHP\Core\Modules\Stopwords
 */
final class Stopwords
{
    private static ?StopwordsModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): StopwordsModule
    {
        if (self::$instance === null) {
            self::$instance = new StopwordsModule();
            self::$instance->register();
        }

        return self::$instance;
    }

    /**
     * Reset the singleton instance
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    /**
     * Check if word is a stopword
     *
     * Example:
     *   Stopwords::isStopword('من') // true
     *   Stopwords::isStopword('كتاب') // false
     */
    public static function isStopword(string $word): bool
    {
        return self::getInstance()->isStopword($word);
    }

    /**
     * Remove stopwords from text
     *
     * Example:
     *   Stopwords::remove('هذا هو الكتاب الذي قرأته')
     *   // 'الكتاب قرأته'
     */
    public static function remove(string $text): string
    {
        return self::getInstance()->removeStopwords($text);
    }

    /**
     * Alias for remove
     */
    public static function removeStopwords(string $text): string
    {
        return self::remove($text);
    }

    /**
     * Filter stopwords from array
     *
     * @param array<string> $words
     * @return array<string>
     */
    public static function filter(array $words): array
    {
        return self::getInstance()->filterStopwords($words);
    }

    /**
     * Alias for filter
     *
     * @param array<string> $words
     * @return array<string>
     */
    public static function filterStopwords(array $words): array
    {
        return self::filter($words);
    }

    /**
     * Get all stopwords
     *
     * @return array<string>
     */
    public static function getAll(): array
    {
        return self::getInstance()->getStopwords();
    }

    /**
     * Get stopwords list
     *
     * @return array<string>
     */
    public static function getList(): array
    {
        return self::getAll();
    }

    /**
     * Add custom stopwords
     *
     * @param array<string> $words
     */
    public static function add(array $words): void
    {
        self::getInstance()->addStopwords($words);
    }

    /**
     * Remove words from stoplist
     *
     * @param array<string> $words
     */
    public static function removeFromList(array $words): void
    {
        self::getInstance()->removeFromList($words);
    }

    /**
     * Reset to default stopwords
     */
    public static function reset(): void
    {
        self::getInstance()->reset();
    }

    /**
     * Get stopwords by category
     *
     * @return array<string>
     */
    public static function byCategory(string $category): array
    {
        return self::getInstance()->getByCategory($category);
    }

    /**
     * Get prepositions
     *
     * @return array<string>
     */
    public static function getPrepositions(): array
    {
        return self::byCategory(Config::CATEGORY_PREPOSITIONS);
    }

    /**
     * Get conjunctions
     *
     * @return array<string>
     */
    public static function getConjunctions(): array
    {
        return self::byCategory(Config::CATEGORY_CONJUNCTIONS);
    }

    /**
     * Get pronouns
     *
     * @return array<string>
     */
    public static function getPronouns(): array
    {
        return self::byCategory(Config::CATEGORY_PRONOUNS);
    }

    /**
     * Get particles
     *
     * @return array<string>
     */
    public static function getParticles(): array
    {
        return self::byCategory(Config::CATEGORY_PARTICLES);
    }

    /**
     * Count stopwords in text
     */
    public static function count(string $text): int
    {
        return self::getInstance()->countStopwords($text);
    }

    /**
     * Get statistics
     *
     * @return array{total: int, stopwords: int, ratio: float}
     */
    public static function getStatistics(string $text): array
    {
        /** @var \ArPHP\Core\Modules\Stopwords\Services\StopwordsService $service */
        $service = self::getInstance()->getService();

        return $service->getStatistics($text);
    }

    /**
     * Extract stopwords from text
     *
     * @return array<string>
     */
    public static function extract(string $text): array
    {
        /** @var \ArPHP\Core\Modules\Stopwords\Services\StopwordsService $service */
        $service = self::getInstance()->getService();

        return $service->extractStopwords($text);
    }

    /**
     * Extract content words (non-stopwords)
     *
     * @return array<string>
     */
    public static function extractContent(string $text): array
    {
        /** @var \ArPHP\Core\Modules\Stopwords\Services\StopwordsService $service */
        $service = self::getInstance()->getService();

        return $service->extractContentWords($text);
    }

    /**
     * Get word frequency (excluding stopwords)
     *
     * @return array<string, int>
     */
    public static function getWordFrequency(string $text): array
    {
        /** @var \ArPHP\Core\Modules\Stopwords\Services\StopwordsService $service */
        $service = self::getInstance()->getService();

        return $service->getWordFrequency($text);
    }

    /**
     * Get count of stopwords in list
     */
    public static function listCount(): int
    {
        /** @var \ArPHP\Core\Modules\Stopwords\Services\StopwordsService $service */
        $service = self::getInstance()->getService();

        return $service->count();
    }
}
