<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\EncodingTools;

/**
 * EncodingTools Facade - PHP 8.4
 *
 * Static facade for easy access to encoding tools functionality.
 *
 * Usage:
 *   use ArPHP\Core\Modules\EncodingTools\EncodingTools;
 *
 *   $encoding = EncodingTools::detectEncoding($text);
 *   $utf8Text = EncodingTools::toUtf8($text);
 *   $fixed = EncodingTools::fixMixedEncoding($text);
 *
 * @package ArPHP\Core\Modules\EncodingTools
 */
final class EncodingTools
{
    private static ?EncodingToolsModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): EncodingToolsModule
    {
        if (self::$instance === null) {
            self::$instance = new EncodingToolsModule();
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
     * Detect text encoding
     *
     * Example:
     *   EncodingTools::detectEncoding($text) // "UTF-8" or "Windows-1256"
     */
    public static function detectEncoding(string $text): string
    {
        return self::getInstance()->detectEncoding($text);
    }

    /**
     * Convert text between encodings
     *
     * Example:
     *   EncodingTools::convert($text, 'UTF-8', 'Windows-1256')
     */
    public static function convert(string $text, string $toEncoding, ?string $fromEncoding = null): string
    {
        return self::getInstance()->convert($text, $toEncoding, $fromEncoding);
    }

    /**
     * Convert to UTF-8
     *
     * Example:
     *   $utf8Text = EncodingTools::toUtf8($windows1256Text);
     */
    public static function toUtf8(string $text, ?string $fromEncoding = null): string
    {
        return self::getInstance()->toUtf8($text, $fromEncoding);
    }

    /**
     * Convert from UTF-8 to another encoding
     */
    public static function fromUtf8(string $text, string $toEncoding): string
    {
        return self::getInstance()->fromUtf8($text, $toEncoding);
    }

    /**
     * Check if text is valid UTF-8
     */
    public static function isValidUtf8(string $text): bool
    {
        return self::getInstance()->isValidUtf8($text);
    }

    /**
     * Convert Windows-1256 to UTF-8
     */
    public static function windows1256ToUtf8(string $text): string
    {
        return self::getInstance()->windows1256ToUtf8($text);
    }

    /**
     * Convert UTF-8 to Windows-1256
     */
    public static function utf8ToWindows1256(string $text): string
    {
        return self::getInstance()->utf8ToWindows1256($text);
    }

    /**
     * Convert ISO-8859-6 to UTF-8
     */
    public static function iso88596ToUtf8(string $text): string
    {
        return self::getInstance()->iso88596ToUtf8($text);
    }

    /**
     * Convert UTF-8 to ISO-8859-6
     */
    public static function utf8ToIso88596(string $text): string
    {
        return self::getInstance()->utf8ToIso88596($text);
    }

    /**
     * Fix mixed encoding issues
     *
     * Example:
     *   $fixed = EncodingTools::fixMixedEncoding($corruptedText);
     */
    public static function fixMixedEncoding(string $text): string
    {
        return self::getInstance()->fixMixedEncoding($text);
    }

    /**
     * Decode HTML entities to Arabic text
     *
     * Example:
     *   EncodingTools::decodeHtmlEntities('&#1605;&#1585;&#1581;&#1576;&#1575;')
     *   // "مرحبا"
     */
    public static function decodeHtmlEntities(string $text): string
    {
        return self::getInstance()->decodeHtmlEntities($text);
    }

    /**
     * Encode Arabic text to HTML entities
     *
     * Example:
     *   EncodingTools::encodeHtmlEntities('مرحبا')
     *   // "&#1605;&#1585;&#1581;&#1576;&#1575;"
     */
    public static function encodeHtmlEntities(string $text): string
    {
        return self::getInstance()->encodeHtmlEntities($text);
    }

    /**
     * Get Unicode code point for character
     *
     * Example:
     *   EncodingTools::charToCodepoint('م') // 1605
     */
    public static function charToCodepoint(string $char): int
    {
        return self::getInstance()->charToCodepoint($char);
    }

    /**
     * Get character from Unicode code point
     *
     * Example:
     *   EncodingTools::codepointToChar(1605) // "م"
     */
    public static function codepointToChar(int $codepoint): string
    {
        return self::getInstance()->codepointToChar($codepoint);
    }

    /**
     * Get all supported encodings
     *
     * @return array<string>
     */
    public static function getSupportedEncodings(): array
    {
        return self::getInstance()->getSupportedEncodings();
    }

    /**
     * Check if encoding is supported
     */
    public static function isEncodingSupported(string $encoding): bool
    {
        return \in_array($encoding, self::getSupportedEncodings(), true);
    }

    /**
     * Convert to UTF-8 or return original if already valid
     */
    public static function ensureUtf8(string $text): string
    {
        if (self::isValidUtf8($text)) {
            return $text;
        }

        return self::toUtf8($text);
    }

    /**
     * Get hex representation of string
     */
    public static function toHex(string $text): string
    {
        return \bin2hex($text);
    }

    /**
     * Convert hex to string
     */
    public static function fromHex(string $hex): string
    {
        return \hex2bin($hex) ?: '';
    }

    /**
     * Get character details
     *
     * @return array{char: string, codepoint: int, hex: string, name: string}
     */
    public static function charInfo(string $char): array
    {
        $codepoint = self::charToCodepoint($char);

        return [
            'char' => $char,
            'codepoint' => $codepoint,
            'hex' => \sprintf('U+%04X', $codepoint),
            'name' => self::getUnicodeName($codepoint),
        ];
    }

    /**
     * Get Unicode character name (basic)
     */
    private static function getUnicodeName(int $codepoint): string
    {
        return match (true) {
            $codepoint >= 0x0600 && $codepoint <= 0x06FF => 'ARABIC LETTER',
            $codepoint >= 0x0750 && $codepoint <= 0x077F => 'ARABIC SUPPLEMENT',
            $codepoint >= 0x08A0 && $codepoint <= 0x08FF => 'ARABIC EXTENDED-A',
            $codepoint >= 0xFB50 && $codepoint <= 0xFDFF => 'ARABIC PRESENTATION FORM-A',
            $codepoint >= 0xFE70 && $codepoint <= 0xFEFF => 'ARABIC PRESENTATION FORM-B',
            default => 'UNKNOWN',
        };
    }
}
