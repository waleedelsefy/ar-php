<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\DialectNormalizer;

/**
 * Dialect Facade - PHP 8.4
 *
 * Static facade for Arabic dialect normalization.
 *
 * Usage:
 *   use ArPHP\Core\Modules\DialectNormalizer\Dialect;
 *
 *   $msa = Dialect::normalize('إزاي الحال');
 *   $dialect = Dialect::detect('شو هالحكي');
 *   $converted = Dialect::toMSA('عايز أروح');
 *
 * @package ArPHP\Core\Modules\DialectNormalizer
 */
final class Dialect
{
    private static ?DialectNormalizerModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): DialectNormalizerModule
    {
        if (self::$instance === null) {
            self::$instance = new DialectNormalizerModule();
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
     * Normalize dialect to MSA
     *
     * Example:
     *   Dialect::normalize('إزاي الحال') // 'كيف الحال'
     *   Dialect::normalize('شو بدك') // 'ماذا تريد'
     */
    public static function normalize(string $text): string
    {
        return self::getInstance()->normalize($text);
    }

    /**
     * Alias for normalize
     */
    public static function toMSA(string $text): string
    {
        return self::normalize($text);
    }

    /**
     * Normalize specific dialect
     */
    public static function normalizeDialect(string $text, string $dialect): string
    {
        return self::getInstance()->normalizeDialect($text, $dialect);
    }

    /**
     * Normalize Egyptian dialect
     */
    public static function normalizeEgyptian(string $text): string
    {
        return self::normalizeDialect($text, Config::DIALECT_EGYPTIAN);
    }

    /**
     * Normalize Levantine dialect
     */
    public static function normalizeLevantine(string $text): string
    {
        return self::normalizeDialect($text, Config::DIALECT_LEVANTINE);
    }

    /**
     * Normalize Gulf dialect
     */
    public static function normalizeGulf(string $text): string
    {
        return self::normalizeDialect($text, Config::DIALECT_GULF);
    }

    /**
     * Normalize Maghrebi dialect
     */
    public static function normalizeMaghrebi(string $text): string
    {
        return self::normalizeDialect($text, Config::DIALECT_MAGHREBI);
    }

    /**
     * Normalize Iraqi dialect
     */
    public static function normalizeIraqi(string $text): string
    {
        return self::normalizeDialect($text, Config::DIALECT_IRAQI);
    }

    /**
     * Detect dialect
     *
     * Example:
     *   Dialect::detect('إزاي الحال') // 'egy'
     *   Dialect::detect('شو هالحكي') // 'lev'
     */
    public static function detect(string $text): string
    {
        return self::getInstance()->detectDialect($text);
    }

    /**
     * Alias for detect
     */
    public static function detectDialect(string $text): string
    {
        return self::detect($text);
    }

    /**
     * Get dialect scores
     *
     * @return array<string, float>
     */
    public static function scores(string $text): array
    {
        return self::getInstance()->getDialectScores($text);
    }

    /**
     * Alias for scores
     *
     * @return array<string, float>
     */
    public static function getDialectScores(string $text): array
    {
        return self::scores($text);
    }

    /**
     * Convert between dialects
     */
    public static function convert(string $text, string $fromDialect, string $toDialect): string
    {
        return self::getInstance()->convert($text, $fromDialect, $toDialect);
    }

    /**
     * Check if text is MSA
     */
    public static function isMSA(string $text): bool
    {
        return self::getInstance()->isMSA($text);
    }

    /**
     * Alias for isMSA
     */
    public static function isStandard(string $text): bool
    {
        return self::isMSA($text);
    }

    /**
     * Check if text is Egyptian
     */
    public static function isEgyptian(string $text): bool
    {
        return self::detect($text) === Config::DIALECT_EGYPTIAN;
    }

    /**
     * Check if text is Levantine
     */
    public static function isLevantine(string $text): bool
    {
        return self::detect($text) === Config::DIALECT_LEVANTINE;
    }

    /**
     * Check if text is Gulf
     */
    public static function isGulf(string $text): bool
    {
        return self::detect($text) === Config::DIALECT_GULF;
    }

    /**
     * Get supported dialects
     *
     * @return array<string>
     */
    public static function getSupportedDialects(): array
    {
        return self::getInstance()->getSupportedDialects();
    }

    /**
     * Get dialect name
     */
    public static function getDialectName(string $code): string
    {
        /** @var \ArPHP\Core\Modules\DialectNormalizer\Services\DialectNormalizerService $service */
        $service = self::getInstance()->getService();

        return $service->getDialectName($code);
    }

    /**
     * Normalize all dialects
     */
    public static function normalizeAll(string $text): string
    {
        /** @var \ArPHP\Core\Modules\DialectNormalizer\Services\DialectNormalizerService $service */
        $service = self::getInstance()->getService();

        return $service->normalizeAll($text);
    }

    /**
     * Extract dialectal words
     *
     * @return array<string>
     */
    public static function extractDialectalWords(string $text): array
    {
        /** @var \ArPHP\Core\Modules\DialectNormalizer\Services\DialectNormalizerService $service */
        $service = self::getInstance()->getService();

        return $service->extractDialectalWords($text);
    }

    /**
     * Check if has dialectal words
     */
    public static function hasDialectalWords(string $text): bool
    {
        /** @var \ArPHP\Core\Modules\DialectNormalizer\Services\DialectNormalizerService $service */
        $service = self::getInstance()->getService();

        return $service->hasDialectalWords($text);
    }

    /**
     * Check if text is dialectal
     */
    public static function isDialectal(string $text): bool
    {
        return !self::isMSA($text);
    }

    // Dialect constants for convenience
    public const string MSA = Config::DIALECT_MSA;
    public const string EGYPTIAN = Config::DIALECT_EGYPTIAN;
    public const string LEVANTINE = Config::DIALECT_LEVANTINE;
    public const string GULF = Config::DIALECT_GULF;
    public const string MAGHREBI = Config::DIALECT_MAGHREBI;
    public const string IRAQI = Config::DIALECT_IRAQI;
}
