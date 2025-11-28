<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Slugify;

/**
 * Slugify Facade - PHP 8.4
 *
 * Static facade for Arabic slug generation.
 *
 * Usage:
 *   use ArPHP\Core\Modules\Slugify\Slugify;
 *
 *   $slug = Slugify::make('مرحبا بالعالم');
 *   $arabicSlug = Slugify::arabic('مرحبا بالعالم');
 *   $transliterated = Slugify::transliterate('محمد أحمد');
 *
 * @package ArPHP\Core\Modules\Slugify
 */
final class Slugify
{
    private static ?SlugifyModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): SlugifyModule
    {
        if (self::$instance === null) {
            self::$instance = new SlugifyModule();
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
     * Generate slug from Arabic text (transliterated)
     *
     * Example:
     *   Slugify::make('مرحبا بالعالم') // 'mrhba-balalm'
     *   Slugify::make('محمد أحمد علي') // 'mhmd-ahmd-aly'
     */
    public static function make(string $text, string $separator = '-'): string
    {
        return self::getInstance()->slugify($text, $separator);
    }

    /**
     * Alias for make
     */
    public static function slugify(string $text, string $separator = '-'): string
    {
        return self::make($text, $separator);
    }

    /**
     * Generate slug preserving Arabic characters
     *
     * Example:
     *   Slugify::arabic('مرحبا بالعالم') // 'مرحبا-بالعالم'
     */
    public static function arabic(string $text, string $separator = '-'): string
    {
        return self::getInstance()->slugifyArabic($text, $separator);
    }

    /**
     * Alias for arabic
     */
    public static function slugifyArabic(string $text, string $separator = '-'): string
    {
        return self::arabic($text, $separator);
    }

    /**
     * Transliterate Arabic to Latin
     *
     * Example:
     *   Slugify::transliterate('محمد') // 'mhmd'
     *   Slugify::transliterate('القاهرة') // 'alqahra'
     */
    public static function transliterate(string $text): string
    {
        return self::getInstance()->transliterate($text);
    }

    /**
     * Alias for transliterate
     */
    public static function toLatin(string $text): string
    {
        return self::transliterate($text);
    }

    /**
     * Generate unique slug
     *
     * @param callable $existsChecker Function that returns true if slug exists
     */
    public static function unique(string $text, callable $existsChecker): string
    {
        return self::getInstance()->uniqueSlug($text, $existsChecker);
    }

    /**
     * Alias for unique
     */
    public static function uniqueSlug(string $text, callable $existsChecker): string
    {
        return self::unique($text, $existsChecker);
    }

    /**
     * Reverse transliterate (approximate)
     *
     * Example:
     *   Slugify::toArabic('mhmd') // 'محمد' (approximate)
     */
    public static function toArabic(string $slug): string
    {
        return self::getInstance()->reverseTransliterate($slug);
    }

    /**
     * Alias for toArabic
     */
    public static function reverseTransliterate(string $slug): string
    {
        return self::toArabic($slug);
    }

    /**
     * Generate slug with underscore separator
     */
    public static function underscore(string $text): string
    {
        return self::make($text, '_');
    }

    /**
     * Generate slug with dot separator
     */
    public static function dot(string $text): string
    {
        return self::make($text, '.');
    }

    /**
     * Generate custom slug
     *
     * @param array{separator?: string, lowercase?: bool, maxLength?: int, preserveArabic?: bool} $options
     */
    public static function custom(string $text, array $options = []): string
    {
        /** @var \ArPHP\Core\Modules\Slugify\Services\SlugifyService $service */
        $service = self::getInstance()->getService();

        return $service->slugifyCustom($text, $options);
    }

    /**
     * Convert to filename
     */
    public static function filename(string $text, string $extension = ''): string
    {
        /** @var \ArPHP\Core\Modules\Slugify\Services\SlugifyService $service */
        $service = self::getInstance()->getService();

        return $service->toFilename($text, $extension);
    }

    /**
     * Generate SEO-friendly slug
     */
    public static function seo(string $text, int $maxWords = 6): string
    {
        /** @var \ArPHP\Core\Modules\Slugify\Services\SlugifyService $service */
        $service = self::getInstance()->getService();

        return $service->seoSlug($text, $maxWords);
    }

    /**
     * Check if valid slug
     */
    public static function isValid(string $text): bool
    {
        /** @var \ArPHP\Core\Modules\Slugify\Services\SlugifyService $service */
        $service = self::getInstance()->getService();

        return $service->isValidSlug($text);
    }

    /**
     * Check if valid Arabic slug
     */
    public static function isValidArabic(string $text): bool
    {
        /** @var \ArPHP\Core\Modules\Slugify\Services\SlugifyService $service */
        $service = self::getInstance()->getService();

        return $service->isValidArabicSlug($text);
    }

    /**
     * Sanitize existing slug
     */
    public static function sanitize(string $slug): string
    {
        /** @var \ArPHP\Core\Modules\Slugify\Services\SlugifyService $service */
        $service = self::getInstance()->getService();

        return $service->sanitize($slug);
    }

    /**
     * Extract slug from URL
     */
    public static function fromUrl(string $url): string
    {
        /** @var \ArPHP\Core\Modules\Slugify\Services\SlugifyService $service */
        $service = self::getInstance()->getService();

        return $service->extractFromUrl($url);
    }
}
