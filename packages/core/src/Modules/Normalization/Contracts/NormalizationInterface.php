<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Normalization\Contracts;

/**
 * Normalization Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Normalization
 */
interface NormalizationInterface
{
    /**
     * Full normalization (apply all normalizations)
     */
    public function normalize(string $text): string;

    /**
     * Remove Arabic diacritics (tashkeel)
     */
    public function removeDiacritics(string $text): string;

    /**
     * Normalize Alef variants (أ إ آ ٱ → ا)
     */
    public function normalizeAlef(string $text): string;

    /**
     * Normalize Ta Marbuta (ة → ه)
     */
    public function normalizeTaMarbuta(string $text): string;

    /**
     * Normalize Alef Maqsura (ى → ي)
     */
    public function normalizeAlefMaqsura(string $text): string;

    /**
     * Normalize Waw variants
     */
    public function normalizeWaw(string $text): string;

    /**
     * Normalize Yaa variants
     */
    public function normalizeYaa(string $text): string;

    /**
     * Remove tatweel/kashida (ـ)
     */
    public function removeTatweel(string $text): string;

    /**
     * Remove non-Arabic characters
     */
    public function removeNonArabic(string $text): string;

    /**
     * Normalize whitespace
     */
    public function normalizeWhitespace(string $text): string;

    /**
     * Normalize numbers (convert to Arabic or Western)
     */
    public function normalizeNumbers(string $text, string $style = 'arabic'): string;

    /**
     * Normalize for search
     */
    public function normalizeForSearch(string $text): string;

    /**
     * Custom normalization with options
     *
     * @param array{
     *     diacritics?: bool,
     *     alef?: bool,
     *     ta_marbuta?: bool,
     *     alef_maqsura?: bool,
     *     tatweel?: bool,
     *     whitespace?: bool
     * } $options
     */
    public function normalizeCustom(string $text, array $options): string;
}
