<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\AdvancedKeyboardFix;

/**
 * AdvancedKeyboardFix Facade - PHP 8.4
 *
 * Static facade for easy access to keyboard fix functionality.
 *
 * Usage:
 *   use ArPHP\Core\Modules\AdvancedKeyboardFix\KeyboardFix;
 *
 *   $fixed = KeyboardFix::fixArabicOnEnglish('hgph');
 *   $detected = KeyboardFix::detectLayout($text);
 *   $autoFixed = KeyboardFix::autoFix($text);
 *
 * @package ArPHP\Core\Modules\AdvancedKeyboardFix
 */
final class KeyboardFix
{
    private static ?AdvancedKeyboardFixModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): AdvancedKeyboardFixModule
    {
        if (self::$instance === null) {
            self::$instance = new AdvancedKeyboardFixModule();
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
     * Fix Arabic text typed on English keyboard
     *
     * Example:
     *   KeyboardFix::fixArabicOnEnglish('hgph') // 'الله'
     *   KeyboardFix::fixArabicOnEnglish('wfhp hgodv') // 'صباح الخير'
     */
    public static function fixArabicOnEnglish(string $text): string
    {
        return self::getInstance()->fixArabicOnEnglish($text);
    }

    /**
     * Fix English text typed on Arabic keyboard
     *
     * Example:
     *   KeyboardFix::fixEnglishOnArabic('اثممخ') // 'hello'
     */
    public static function fixEnglishOnArabic(string $text): string
    {
        return self::getInstance()->fixEnglishOnArabic($text);
    }

    /**
     * Auto-detect and fix keyboard layout issues
     *
     * Example:
     *   KeyboardFix::autoFix('hgsjgl ugd;l') // 'السلام عليكم'
     */
    public static function autoFix(string $text): string
    {
        return self::getInstance()->autoFix($text);
    }

    /**
     * Detect keyboard layout from text
     *
     * Example:
     *   KeyboardFix::detectLayout('مرحبا') // 'arabic'
     *   KeyboardFix::detectLayout('hello') // 'english'
     *
     * @return 'arabic'|'english'|'mixed'|'unknown'
     */
    public static function detectLayout(string $text): string
    {
        return self::getInstance()->detectLayout($text);
    }

    /**
     * Check if text has keyboard layout issues
     */
    public static function hasLayoutIssue(string $text): bool
    {
        return self::getInstance()->hasLayoutIssue($text);
    }

    /**
     * Fix Franco-Arabic (Arabizi) to Arabic
     *
     * Example:
     *   KeyboardFix::fixFrancoArabic('marhaba') // 'مرهبا'
     *   KeyboardFix::fixFrancoArabic('7abibi') // 'حبيبي'
     *   KeyboardFix::fixFrancoArabic('3eid mubarak') // 'عيد مبارك'
     */
    public static function fixFrancoArabic(string $text): string
    {
        return self::getInstance()->fixFrancoArabic($text);
    }

    /**
     * Alias for fixFrancoArabic
     */
    public static function francoToArabic(string $text): string
    {
        return self::fixFrancoArabic($text);
    }

    /**
     * Alias for fixFrancoArabic
     */
    public static function arabiziToArabic(string $text): string
    {
        return self::fixFrancoArabic($text);
    }

    /**
     * Fix common typing mistakes
     */
    public static function fixTypingMistakes(string $text): string
    {
        return self::getInstance()->fixTypingMistakes($text);
    }

    /**
     * Get keyboard mapping for a layout
     *
     * @return array<string, string>
     */
    public static function getKeyboardMap(string $layout): array
    {
        return self::getInstance()->getKeyboardMap($layout);
    }

    /**
     * Check if text is Arabic
     */
    public static function isArabic(string $text): bool
    {
        return self::detectLayout($text) === 'arabic';
    }

    /**
     * Check if text is English
     */
    public static function isEnglish(string $text): bool
    {
        return self::detectLayout($text) === 'english';
    }

    /**
     * Check if text is mixed Arabic/English
     */
    public static function isMixed(string $text): bool
    {
        return self::detectLayout($text) === 'mixed';
    }

    /**
     * Smart fix - tries to fix any kind of keyboard issue
     */
    public static function smartFix(string $text): string
    {
        // First try auto-fix
        $result = self::autoFix($text);

        // Then fix typing mistakes
        $result = self::fixTypingMistakes($result);

        return $result;
    }

    /**
     * Get suggestion for text
     *
     * @return array{original: string, suggested: string, confidence: float}
     */
    public static function suggest(string $text): array
    {
        /** @var \ArPHP\Core\Modules\AdvancedKeyboardFix\Services\AdvancedKeyboardFixService $service */
        $service = self::getInstance()->getService();

        return $service->getSuggestion($text);
    }

    /**
     * Convert layout from one to another
     */
    public static function convertLayout(string $text, string $from, string $to): string
    {
        /** @var \ArPHP\Core\Modules\AdvancedKeyboardFix\Services\AdvancedKeyboardFixService $service */
        $service = self::getInstance()->getService();

        return $service->convertLayout($text, $from, $to);
    }
}
