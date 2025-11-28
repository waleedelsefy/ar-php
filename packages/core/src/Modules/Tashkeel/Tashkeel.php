<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Tashkeel;

/**
 * Tashkeel Facade - PHP 8.4
 *
 * Static facade for Arabic diacritization.
 *
 * @package ArPHP\Core\Modules\Tashkeel
 *
 * @method static string removeTashkeel(string $text)
 * @method static string removeDiacritic(string $text, string $diacritic)
 * @method static bool hasTashkeel(string $text)
 * @method static int countTashkeel(string $text)
 * @method static array getDiacriticStats(string $text)
 * @method static array extractTashkeel(string $text)
 * @method static string addSukoon(string $text)
 * @method static string normalizeShadda(string $text)
 * @method static string removeShortVowels(string $text)
 * @method static string removeTanween(string $text)
 * @method static string removeShadda(string $text)
 * @method static float getTashkeelDensity(string $text)
 * @method static bool isSunLetter(string $letter)
 * @method static bool isMoonLetter(string $letter)
 */
final class Tashkeel
{
    private static ?TashkeelModule $instance = null;

    /**
     * Get singleton instance
     */
    public static function getInstance(): TashkeelModule
    {
        if (self::$instance === null) {
            self::$instance = new TashkeelModule();
        }
        return self::$instance;
    }

    /**
     * Reset instance
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Remove all diacritics from text
     */
    public static function strip(string $text): string
    {
        return self::getInstance()->removeTashkeel($text);
    }

    /**
     * Remove specific diacritic
     */
    public static function stripDiacritic(string $text, string $diacritic): string
    {
        return self::getInstance()->removeDiacritic($text, $diacritic);
    }

    /**
     * Check if text has diacritics
     */
    public static function has(string $text): bool
    {
        return self::getInstance()->hasTashkeel($text);
    }

    /**
     * Count diacritics in text
     */
    public static function count(string $text): int
    {
        return self::getInstance()->countTashkeel($text);
    }

    /**
     * Get diacritic statistics
     *
     * @return array<string, int>
     */
    public static function stats(string $text): array
    {
        return self::getInstance()->getDiacriticStats($text);
    }

    /**
     * Extract diacritics from text
     *
     * @return array<string>
     */
    public static function extract(string $text): array
    {
        return self::getInstance()->extractTashkeel($text);
    }

    /**
     * Normalize shadda combinations
     */
    public static function normalize(string $text): string
    {
        return self::getInstance()->normalizeShadda($text);
    }

    /**
     * Remove only short vowels
     */
    public static function stripVowels(string $text): string
    {
        return self::getInstance()->removeShortVowels($text);
    }

    /**
     * Remove only tanween
     */
    public static function stripTanween(string $text): string
    {
        return self::getInstance()->removeTanween($text);
    }

    /**
     * Remove only shadda
     */
    public static function stripShadda(string $text): string
    {
        return self::getInstance()->removeShadda($text);
    }

    /**
     * Get tashkeel density ratio
     */
    public static function density(string $text): float
    {
        return self::getInstance()->getTashkeelDensity($text);
    }

    /**
     * Get diacritic constants
     */
    public static function getFatha(): string
    {
        return Config::FATHA;
    }

    public static function getDamma(): string
    {
        return Config::DAMMA;
    }

    public static function getKasra(): string
    {
        return Config::KASRA;
    }

    public static function getSukoon(): string
    {
        return Config::SUKOON;
    }

    public static function getShadda(): string
    {
        return Config::SHADDA;
    }

    public static function getFathatan(): string
    {
        return Config::FATHATAN;
    }

    public static function getDammatan(): string
    {
        return Config::DAMMATAN;
    }

    public static function getKasratan(): string
    {
        return Config::KASRATAN;
    }

    /**
     * Static method handler
     *
     * @param array<mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return self::getInstance()->{$name}(...$arguments);
    }
}
