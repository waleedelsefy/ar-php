<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\BuckwalterTransliteration\Contracts;

/**
 * Buckwalter Transliteration Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\BuckwalterTransliteration
 */
interface BuckwalterTransliterationInterface
{
    /**
     * Transliterate Arabic text to Buckwalter
     */
    public function toLatinBuckwalter(string $text): string;

    /**
     * Transliterate Buckwalter to Arabic
     */
    public function toArabicBuckwalter(string $text): string;

    /**
     * Transliterate using Safe Buckwalter (XML-safe)
     */
    public function toLatinSafeBuckwalter(string $text): string;

    /**
     * Convert Safe Buckwalter to Arabic
     */
    public function fromSafeBuckwalter(string $text): string;

    /**
     * Transliterate to ISO 233 standard
     */
    public function toLatinIso233(string $text): string;

    /**
     * Transliterate to DIN 31635 standard
     */
    public function toLatinDin31635(string $text): string;

    /**
     * Transliterate to Library of Congress standard
     */
    public function toLatinLoc(string $text): string;

    /**
     * Simple phonetic transliteration
     */
    public function toPhonetic(string $text): string;

    /**
     * Transliterate from any supported Latin scheme to Arabic
     */
    public function toArabic(string $text, string $scheme): string;

    /**
     * Get available transliteration schemes
     *
     * @return array<string>
     */
    public function getSchemes(): array;
}
