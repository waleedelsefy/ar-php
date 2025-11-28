<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Normalization;

/**
 * Normalizer Facade - PHP 8.4
 *
 * Static facade for easy access to normalization functionality.
 *
 * Usage:
 *   use ArPHP\Core\Modules\Normalization\Normalizer;
 *
 *   $normalized = Normalizer::normalize('مُحَمَّد');
 *   $clean = Normalizer::removeDiacritics('السَّلَامُ عَلَيْكُمْ');
 *   $search = Normalizer::forSearch('أحمد');
 *
 * @package ArPHP\Core\Modules\Normalization
 */
final class Normalizer
{
    private static ?NormalizationModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): NormalizationModule
    {
        if (self::$instance === null) {
            self::$instance = new NormalizationModule();
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
     * Full normalization
     *
     * Example:
     *   Normalizer::normalize('مُحَمَّد') // 'محمد'
     *   Normalizer::normalize('أحمد') // 'احمد'
     */
    public static function normalize(string $text): string
    {
        return self::getInstance()->normalize($text);
    }

    /**
     * Remove diacritics (tashkeel)
     *
     * Example:
     *   Normalizer::removeDiacritics('السَّلَامُ عَلَيْكُمْ')
     *   // 'السلام عليكم'
     */
    public static function removeDiacritics(string $text): string
    {
        return self::getInstance()->removeDiacritics($text);
    }

    /**
     * Alias for removeDiacritics
     */
    public static function stripTashkeel(string $text): string
    {
        return self::removeDiacritics($text);
    }

    /**
     * Normalize Alef variants
     *
     * Example:
     *   Normalizer::normalizeAlef('أحمد إبراهيم آدم')
     *   // 'احمد ابراهيم ادم'
     */
    public static function normalizeAlef(string $text): string
    {
        return self::getInstance()->normalizeAlef($text);
    }

    /**
     * Normalize Ta Marbuta
     *
     * Example:
     *   Normalizer::normalizeTaMarbuta('مدرسة')
     *   // 'مدرسه'
     */
    public static function normalizeTaMarbuta(string $text): string
    {
        return self::getInstance()->normalizeTaMarbuta($text);
    }

    /**
     * Normalize Alef Maqsura
     *
     * Example:
     *   Normalizer::normalizeAlefMaqsura('على')
     *   // 'علي'
     */
    public static function normalizeAlefMaqsura(string $text): string
    {
        return self::getInstance()->normalizeAlefMaqsura($text);
    }

    /**
     * Remove tatweel/kashida
     *
     * Example:
     *   Normalizer::removeTatweel('مـرحـبـا')
     *   // 'مرحبا'
     */
    public static function removeTatweel(string $text): string
    {
        return self::getInstance()->removeTatweel($text);
    }

    /**
     * Alias for removeTatweel
     */
    public static function removeKashida(string $text): string
    {
        return self::removeTatweel($text);
    }

    /**
     * Remove non-Arabic characters
     */
    public static function removeNonArabic(string $text): string
    {
        return self::getInstance()->removeNonArabic($text);
    }

    /**
     * Alias for removeNonArabic
     */
    public static function onlyArabic(string $text): string
    {
        return self::removeNonArabic($text);
    }

    /**
     * Normalize whitespace
     */
    public static function normalizeWhitespace(string $text): string
    {
        return self::getInstance()->normalizeWhitespace($text);
    }

    /**
     * Alias for normalizeWhitespace
     */
    public static function cleanSpaces(string $text): string
    {
        return self::normalizeWhitespace($text);
    }

    /**
     * Normalize numbers to Arabic-Indic or Western
     *
     * Example:
     *   Normalizer::normalizeNumbers('123', 'arabic') // '١٢٣'
     *   Normalizer::normalizeNumbers('١٢٣', 'western') // '123'
     */
    public static function normalizeNumbers(string $text, string $style = 'arabic'): string
    {
        return self::getInstance()->normalizeNumbers($text, $style);
    }

    /**
     * Convert to Arabic-Indic digits
     */
    public static function toArabicDigits(string $text): string
    {
        return self::normalizeNumbers($text, 'arabic');
    }

    /**
     * Convert to Western digits
     */
    public static function toWesternDigits(string $text): string
    {
        return self::normalizeNumbers($text, 'western');
    }

    /**
     * Normalize for search
     *
     * Example:
     *   Normalizer::forSearch('أحمد')
     *   // 'احمد'
     */
    public static function forSearch(string $text): string
    {
        return self::getInstance()->normalizeForSearch($text);
    }

    /**
     * Custom normalization
     *
     * @param array<string, mixed> $options
     */
    public static function custom(string $text, array $options): string
    {
        return self::getInstance()->normalizeCustom($text, $options);
    }

    /**
     * Light normalization (minimal changes)
     */
    public static function light(string $text): string
    {
        /** @var \ArPHP\Core\Modules\Normalization\Services\NormalizationService $service */
        $service = self::getInstance()->getService();

        return $service->normalizeLight($text);
    }

    /**
     * Heavy normalization (maximum changes)
     */
    public static function heavy(string $text): string
    {
        /** @var \ArPHP\Core\Modules\Normalization\Services\NormalizationService $service */
        $service = self::getInstance()->getService();

        return $service->normalizeHeavy($text);
    }

    /**
     * Remove Hamza
     */
    public static function removeHamza(string $text): string
    {
        /** @var \ArPHP\Core\Modules\Normalization\Services\NormalizationService $service */
        $service = self::getInstance()->getService();

        return $service->removeHamza($text);
    }

    /**
     * Remove punctuation
     */
    public static function removePunctuation(string $text): string
    {
        /** @var \ArPHP\Core\Modules\Normalization\Services\NormalizationService $service */
        $service = self::getInstance()->getService();

        return $service->removePunctuation($text);
    }

    /**
     * Check if text is normalized
     */
    public static function isNormalized(string $text): bool
    {
        /** @var \ArPHP\Core\Modules\Normalization\Services\NormalizationService $service */
        $service = self::getInstance()->getService();

        return $service->isNormalized($text);
    }

    /**
     * Compare two strings after normalization
     */
    public static function compare(string $text1, string $text2): bool
    {
        return self::normalize($text1) === self::normalize($text2);
    }
}
