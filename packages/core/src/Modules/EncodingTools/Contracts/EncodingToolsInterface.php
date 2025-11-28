<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\EncodingTools\Contracts;

/**
 * Encoding Tools Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\EncodingTools
 */
interface EncodingToolsInterface
{
    /**
     * Detect encoding of text
     */
    public function detectEncoding(string $text): string;

    /**
     * Convert text from one encoding to another
     */
    public function convert(string $text, string $toEncoding, ?string $fromEncoding = null): string;

    /**
     * Convert to UTF-8
     */
    public function toUtf8(string $text, ?string $fromEncoding = null): string;

    /**
     * Convert from UTF-8 to specified encoding
     */
    public function fromUtf8(string $text, string $toEncoding): string;

    /**
     * Check if text is valid UTF-8
     */
    public function isValidUtf8(string $text): bool;

    /**
     * Convert Arabic Windows-1256 to UTF-8
     */
    public function windows1256ToUtf8(string $text): string;

    /**
     * Convert UTF-8 to Arabic Windows-1256
     */
    public function utf8ToWindows1256(string $text): string;

    /**
     * Convert Arabic ISO-8859-6 to UTF-8
     */
    public function iso88596ToUtf8(string $text): string;

    /**
     * Convert UTF-8 to Arabic ISO-8859-6
     */
    public function utf8ToIso88596(string $text): string;

    /**
     * Fix mixed encoding issues
     */
    public function fixMixedEncoding(string $text): string;

    /**
     * Convert HTML entities to Arabic text
     */
    public function decodeHtmlEntities(string $text): string;

    /**
     * Encode Arabic text to HTML entities
     */
    public function encodeHtmlEntities(string $text): string;

    /**
     * Get Unicode code point for character
     */
    public function charToCodepoint(string $char): int;

    /**
     * Get character from Unicode code point
     */
    public function codepointToChar(int $codepoint): string;

    /**
     * Get all supported encodings
     *
     * @return array<string>
     */
    public function getSupportedEncodings(): array;
}
