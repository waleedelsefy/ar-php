<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\EncodingTools\Services;

use ArPHP\Core\Modules\EncodingTools\Config;
use ArPHP\Core\Modules\EncodingTools\Contracts\EncodingToolsInterface;
use ArPHP\Core\Modules\EncodingTools\Exceptions\EncodingToolsException;

/**
 * Encoding Tools Service - PHP 8.4
 *
 * @package ArPHP\Core\Modules\EncodingTools
 */
final class EncodingToolsService implements EncodingToolsInterface
{
    /** @var array<int, string> */
    private array $windows1256ToUnicodeCache = [];

    /** @var array<int, int> */
    private array $unicodeToWindows1256Cache = [];

    public function __construct()
    {
        $this->buildMappingCaches();
    }

    /**
     * @inheritDoc
     */
    public function detectEncoding(string $text): string
    {
        if ($text === '') {
            return Config::UTF_8;
        }

        // Check for BOM
        foreach (Config::BOM_SIGNATURES as $encoding => $bom) {
            if (\str_starts_with($text, $bom)) {
                return $encoding;
            }
        }

        // Check if valid UTF-8
        if ($this->isValidUtf8($text)) {
            return Config::UTF_8;
        }

        // Try mb_detect_encoding
        $detected = \mb_detect_encoding($text, Config::SUPPORTED_ENCODINGS, true);

        if ($detected !== false) {
            return $detected;
        }

        // Heuristic: check for Windows-1256 Arabic patterns
        if ($this->looksLikeWindows1256($text)) {
            return Config::WINDOWS_1256;
        }

        return Config::UTF_8;
    }

    /**
     * @inheritDoc
     */
    public function convert(string $text, string $toEncoding, ?string $fromEncoding = null): string
    {
        $toEncoding = $this->normalizeEncodingName($toEncoding);
        $fromEncoding = $fromEncoding !== null
            ? $this->normalizeEncodingName($fromEncoding)
            : $this->detectEncoding($text);

        if ($fromEncoding === $toEncoding) {
            return $text;
        }

        // Use mb_convert_encoding
        $result = @\mb_convert_encoding($text, $toEncoding, $fromEncoding);

        if ($result === false) {
            // Try iconv as fallback
            if (\function_exists('iconv')) {
                $result = @\iconv($fromEncoding, $toEncoding . '//TRANSLIT//IGNORE', $text);
            }
        }

        if ($result === false) {
            throw EncodingToolsException::conversionFailed($fromEncoding, $toEncoding);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function toUtf8(string $text, ?string $fromEncoding = null): string
    {
        return $this->convert($text, Config::UTF_8, $fromEncoding);
    }

    /**
     * @inheritDoc
     */
    public function fromUtf8(string $text, string $toEncoding): string
    {
        return $this->convert($text, $toEncoding, Config::UTF_8);
    }

    /**
     * @inheritDoc
     */
    public function isValidUtf8(string $text): bool
    {
        return \mb_check_encoding($text, 'UTF-8');
    }

    /**
     * @inheritDoc
     */
    public function windows1256ToUtf8(string $text): string
    {
        $result = '';
        $length = \strlen($text);

        for ($i = 0; $i < $length; $i++) {
            $byte = \ord($text[$i]);

            if ($byte < 128) {
                $result .= $text[$i];
            } else {
                $result .= $this->windows1256ToUnicodeCache[$byte] ?? '?';
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function utf8ToWindows1256(string $text): string
    {
        $result = '';
        $length = \mb_strlen($text, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = \mb_substr($text, $i, 1, 'UTF-8');
            $codepoint = $this->charToCodepoint($char);

            if ($codepoint < 128) {
                $result .= \chr($codepoint);
            } elseif (isset($this->unicodeToWindows1256Cache[$codepoint])) {
                $result .= \chr($this->unicodeToWindows1256Cache[$codepoint]);
            } else {
                $result .= '?';
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function iso88596ToUtf8(string $text): string
    {
        return $this->convert($text, Config::UTF_8, Config::ISO_8859_6);
    }

    /**
     * @inheritDoc
     */
    public function utf8ToIso88596(string $text): string
    {
        return $this->convert($text, Config::ISO_8859_6, Config::UTF_8);
    }

    /**
     * @inheritDoc
     */
    public function fixMixedEncoding(string $text): string
    {
        // Check if already valid UTF-8
        if ($this->isValidUtf8($text)) {
            return $text;
        }

        // Try to fix by detecting encoding of each segment
        $result = '';
        $buffer = '';
        $length = \strlen($text);

        for ($i = 0; $i < $length; $i++) {
            $byte = \ord($text[$i]);
            $buffer .= $text[$i];

            // If we have a complete UTF-8 sequence or non-UTF8 byte
            if ($byte < 128) {
                $result .= $buffer;
                $buffer = '';
            } elseif (($byte & 0xC0) === 0x80) {
                // Continuation byte
                if ($this->isValidUtf8($buffer)) {
                    $result .= $buffer;
                    $buffer = '';
                }
            } elseif ($byte >= 128 && $byte <= 255 && \strlen($buffer) === 1) {
                // Likely Windows-1256 byte
                $result .= $this->windows1256ToUnicodeCache[$byte] ?? '?';
                $buffer = '';
            }
        }

        // Handle remaining buffer
        if ($buffer !== '') {
            if ($this->isValidUtf8($buffer)) {
                $result .= $buffer;
            } else {
                $result .= $this->windows1256ToUtf8($buffer);
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function decodeHtmlEntities(string $text): string
    {
        // Decode numeric entities
        $text = \preg_replace_callback(
            '/&#(\d+);/',
            fn(array $matches) => $this->codepointToChar((int) $matches[1]),
            $text
        );

        // Decode hex entities
        $text = \preg_replace_callback(
            '/&#x([0-9A-Fa-f]+);/',
            fn(array $matches) => $this->codepointToChar(\hexdec($matches[1])),
            $text
        );

        // Decode named entities
        return \html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * @inheritDoc
     */
    public function encodeHtmlEntities(string $text): string
    {
        $result = '';
        $length = \mb_strlen($text, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = \mb_substr($text, $i, 1, 'UTF-8');
            $codepoint = $this->charToCodepoint($char);

            // Encode Arabic characters as numeric entities
            if ($this->isArabicCodepoint($codepoint)) {
                $result .= '&#' . $codepoint . ';';
            } else {
                $result .= \htmlspecialchars($char, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function charToCodepoint(string $char): int
    {
        $bytes = \unpack('C*', $char);

        if ($bytes === false || empty($bytes)) {
            return 0;
        }

        $bytes = \array_values($bytes);
        $count = \count($bytes);

        if ($count === 1) {
            return $bytes[0];
        }

        if ($count === 2) {
            return (($bytes[0] & 0x1F) << 6) | ($bytes[1] & 0x3F);
        }

        if ($count === 3) {
            return (($bytes[0] & 0x0F) << 12) | (($bytes[1] & 0x3F) << 6) | ($bytes[2] & 0x3F);
        }

        if ($count === 4) {
            return (($bytes[0] & 0x07) << 18) | (($bytes[1] & 0x3F) << 12)
                | (($bytes[2] & 0x3F) << 6) | ($bytes[3] & 0x3F);
        }

        return 0;
    }

    /**
     * @inheritDoc
     */
    public function codepointToChar(int $codepoint): string
    {
        if ($codepoint < 0 || $codepoint > 0x10FFFF) {
            throw EncodingToolsException::invalidCodepoint($codepoint);
        }

        if ($codepoint < 0x80) {
            return \chr($codepoint);
        }

        if ($codepoint < 0x800) {
            return \chr(0xC0 | ($codepoint >> 6))
                . \chr(0x80 | ($codepoint & 0x3F));
        }

        if ($codepoint < 0x10000) {
            return \chr(0xE0 | ($codepoint >> 12))
                . \chr(0x80 | (($codepoint >> 6) & 0x3F))
                . \chr(0x80 | ($codepoint & 0x3F));
        }

        return \chr(0xF0 | ($codepoint >> 18))
            . \chr(0x80 | (($codepoint >> 12) & 0x3F))
            . \chr(0x80 | (($codepoint >> 6) & 0x3F))
            . \chr(0x80 | ($codepoint & 0x3F));
    }

    /**
     * @inheritDoc
     */
    public function getSupportedEncodings(): array
    {
        return Config::SUPPORTED_ENCODINGS;
    }

    /**
     * Remove BOM from text
     */
    public function removeBom(string $text): string
    {
        foreach (Config::BOM_SIGNATURES as $bom) {
            if (\str_starts_with($text, $bom)) {
                return \substr($text, \strlen($bom));
            }
        }

        return $text;
    }

    /**
     * Add UTF-8 BOM to text
     */
    public function addUtf8Bom(string $text): string
    {
        if (!\str_starts_with($text, Config::BOM_SIGNATURES['UTF-8'])) {
            return Config::BOM_SIGNATURES['UTF-8'] . $text;
        }

        return $text;
    }

    /**
     * Normalize encoding name
     */
    private function normalizeEncodingName(string $encoding): string
    {
        $lower = \strtolower($encoding);

        return Config::ENCODING_ALIASES[$lower] ?? $encoding;
    }

    /**
     * Check if text looks like Windows-1256
     */
    private function looksLikeWindows1256(string $text): bool
    {
        $length = \strlen($text);
        $arabicByteCount = 0;

        for ($i = 0; $i < $length; $i++) {
            $byte = \ord($text[$i]);

            // Windows-1256 Arabic letters are in range 193-250
            if ($byte >= 193 && $byte <= 250) {
                $arabicByteCount++;
            }
        }

        // If more than 10% of bytes are in Arabic range, likely Windows-1256
        return $arabicByteCount > ($length * 0.1);
    }

    /**
     * Check if codepoint is Arabic
     */
    private function isArabicCodepoint(int $codepoint): bool
    {
        foreach (Config::ARABIC_UNICODE_RANGES as [$start, $end]) {
            if ($codepoint >= $start && $codepoint <= $end) {
                return true;
            }
        }

        return false;
    }

    /**
     * Build encoding mapping caches
     */
    private function buildMappingCaches(): void
    {
        foreach (Config::WINDOWS_1256_MAP as $byte => $codepoint) {
            $this->windows1256ToUnicodeCache[$byte] = $this->codepointToChar($codepoint);
            $this->unicodeToWindows1256Cache[$codepoint] = $byte;
        }
    }
}
