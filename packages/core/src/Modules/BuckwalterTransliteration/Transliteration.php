<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\BuckwalterTransliteration;

/**
 * Transliteration Facade - PHP 8.4
 *
 * Static facade for easy access to transliteration functionality.
 *
 * Usage:
 *   use ArPHP\Core\Modules\BuckwalterTransliteration\Transliteration;
 *
 *   $buckwalter = Transliteration::toBuckwalter('مرحبا');
 *   $arabic = Transliteration::fromBuckwalter('mrHbA');
 *   $phonetic = Transliteration::toPhonetic('مرحبا');
 *
 * @package ArPHP\Core\Modules\BuckwalterTransliteration
 */
final class Transliteration
{
    private static ?BuckwalterTransliterationModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): BuckwalterTransliterationModule
    {
        if (self::$instance === null) {
            self::$instance = new BuckwalterTransliterationModule();
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
     * Arabic to Buckwalter transliteration
     *
     * Example:
     *   Transliteration::toBuckwalter('مرحبا') // "mrHbA"
     *   Transliteration::toBuckwalter('السلام عليكم') // "AlslAm Elykm"
     */
    public static function toBuckwalter(string $text): string
    {
        return self::getInstance()->toLatinBuckwalter($text);
    }

    /**
     * Buckwalter to Arabic transliteration
     *
     * Example:
     *   Transliteration::fromBuckwalter('mrHbA') // "مرحبا"
     */
    public static function fromBuckwalter(string $text): string
    {
        return self::getInstance()->toArabicBuckwalter($text);
    }

    /**
     * Arabic to Safe Buckwalter (XML-safe)
     */
    public static function toSafeBuckwalter(string $text): string
    {
        return self::getInstance()->toLatinSafeBuckwalter($text);
    }

    /**
     * Safe Buckwalter to Arabic
     */
    public static function fromSafeBuckwalter(string $text): string
    {
        return self::getInstance()->fromSafeBuckwalter($text);
    }

    /**
     * Arabic to ISO 233 transliteration
     *
     * Example:
     *   Transliteration::toIso233('محمد') // "muḥammad"
     */
    public static function toIso233(string $text): string
    {
        return self::getInstance()->toLatinIso233($text);
    }

    /**
     * Arabic to DIN 31635 transliteration
     */
    public static function toDin31635(string $text): string
    {
        return self::getInstance()->toLatinDin31635($text);
    }

    /**
     * Arabic to Library of Congress transliteration
     */
    public static function toLoc(string $text): string
    {
        return self::getInstance()->toLatinLoc($text);
    }

    /**
     * Arabic to simple phonetic transliteration
     *
     * Example:
     *   Transliteration::toPhonetic('مرحبا') // "mrhba"
     *   Transliteration::toPhonetic('شكرا') // "shkra"
     */
    public static function toPhonetic(string $text): string
    {
        return self::getInstance()->toPhonetic($text);
    }

    /**
     * Convert Latin to Arabic using specified scheme
     */
    public static function toArabic(string $text, string $scheme = 'buckwalter'): string
    {
        return self::getInstance()->toArabic($text, $scheme);
    }

    /**
     * Get available transliteration schemes
     *
     * @return array<string>
     */
    public static function getSchemes(): array
    {
        return self::getInstance()->getSchemes();
    }

    /**
     * Romanize Arabic text (alias for toPhonetic)
     */
    public static function romanize(string $text): string
    {
        return self::toPhonetic($text);
    }

    /**
     * Arabize Latin text (alias for fromBuckwalter)
     */
    public static function arabize(string $text): string
    {
        return self::fromBuckwalter($text);
    }

    /**
     * Check if text contains Arabic characters
     */
    public static function containsArabic(string $text): bool
    {
        /** @var \ArPHP\Core\Modules\BuckwalterTransliteration\Services\BuckwalterTransliterationService $service */
        $service = self::getInstance()->getService();

        return $service->containsArabic($text);
    }

    /**
     * Check if text is valid Buckwalter
     */
    public static function isValidBuckwalter(string $text): bool
    {
        /** @var \ArPHP\Core\Modules\BuckwalterTransliteration\Services\BuckwalterTransliterationService $service */
        $service = self::getInstance()->getService();

        return $service->isValidBuckwalter($text);
    }

    /**
     * Detect transliteration scheme
     */
    public static function detectScheme(string $text): ?string
    {
        /** @var \ArPHP\Core\Modules\BuckwalterTransliteration\Services\BuckwalterTransliterationService $service */
        $service = self::getInstance()->getService();

        return $service->detectScheme($text);
    }

    /**
     * Auto-detect and convert to Arabic
     */
    public static function autoToArabic(string $text): string
    {
        $scheme = self::detectScheme($text);

        if ($scheme === null) {
            $scheme = Config::SCHEME_BUCKWALTER;
        }

        return self::toArabic($text, $scheme);
    }
}
