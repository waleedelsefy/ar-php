<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\EncodingTools;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\EncodingTools\Contracts\EncodingToolsInterface;
use ArPHP\Core\Modules\EncodingTools\Services\EncodingToolsService;

/**
 * Encoding Tools Module - PHP 8.4
 *
 * Provides encoding conversion and utilities for Arabic text.
 *
 * @package ArPHP\Core\Modules\EncodingTools
 */
final class EncodingToolsModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?EncodingToolsService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'encoding_tools';
    }

    public function register(): void
    {
        $this->service = new EncodingToolsService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): EncodingToolsInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Detect text encoding
     */
    public function detectEncoding(string $text): string
    {
        return $this->getService()->detectEncoding($text);
    }

    /**
     * Convert text between encodings
     */
    public function convert(string $text, string $toEncoding, ?string $fromEncoding = null): string
    {
        return $this->getService()->convert($text, $toEncoding, $fromEncoding);
    }

    /**
     * Convert to UTF-8
     */
    public function toUtf8(string $text, ?string $fromEncoding = null): string
    {
        return $this->getService()->toUtf8($text, $fromEncoding);
    }

    /**
     * Convert from UTF-8
     */
    public function fromUtf8(string $text, string $toEncoding): string
    {
        return $this->getService()->fromUtf8($text, $toEncoding);
    }

    /**
     * Check if valid UTF-8
     */
    public function isValidUtf8(string $text): bool
    {
        return $this->getService()->isValidUtf8($text);
    }

    /**
     * Windows-1256 to UTF-8
     */
    public function windows1256ToUtf8(string $text): string
    {
        return $this->getService()->windows1256ToUtf8($text);
    }

    /**
     * UTF-8 to Windows-1256
     */
    public function utf8ToWindows1256(string $text): string
    {
        return $this->getService()->utf8ToWindows1256($text);
    }

    /**
     * ISO-8859-6 to UTF-8
     */
    public function iso88596ToUtf8(string $text): string
    {
        return $this->getService()->iso88596ToUtf8($text);
    }

    /**
     * UTF-8 to ISO-8859-6
     */
    public function utf8ToIso88596(string $text): string
    {
        return $this->getService()->utf8ToIso88596($text);
    }

    /**
     * Fix mixed encoding issues
     */
    public function fixMixedEncoding(string $text): string
    {
        return $this->getService()->fixMixedEncoding($text);
    }

    /**
     * Decode HTML entities
     */
    public function decodeHtmlEntities(string $text): string
    {
        return $this->getService()->decodeHtmlEntities($text);
    }

    /**
     * Encode to HTML entities
     */
    public function encodeHtmlEntities(string $text): string
    {
        return $this->getService()->encodeHtmlEntities($text);
    }

    /**
     * Character to codepoint
     */
    public function charToCodepoint(string $char): int
    {
        return $this->getService()->charToCodepoint($char);
    }

    /**
     * Codepoint to character
     */
    public function codepointToChar(int $codepoint): string
    {
        return $this->getService()->codepointToChar($codepoint);
    }

    /**
     * Get supported encodings
     *
     * @return array<string>
     */
    public function getSupportedEncodings(): array
    {
        return $this->getService()->getSupportedEncodings();
    }

    public static function getIdentifier(): string
    {
        return 'encoding_tools';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'detectEncoding',
            'convert',
            'toUtf8',
            'fromUtf8',
            'isValidUtf8',
            'windows1256ToUtf8',
            'utf8ToWindows1256',
            'fixMixedEncoding',
            'decodeHtmlEntities',
            'encodeHtmlEntities',
        ];
    }
}
