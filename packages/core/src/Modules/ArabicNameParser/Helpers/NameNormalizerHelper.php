<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicNameParser\Helpers;

/**
 * Name Normalization Helper - PHP 8.4
 *
 * @package ArPHP\Core\Modules\ArabicNameParser
 */
final readonly class NameNormalizerHelper
{
    /**
     * Arabic diacritics to remove
     */
    private const array DIACRITICS = [
        "\u{064B}", // Fathatan
        "\u{064C}", // Dammatan
        "\u{064D}", // Kasratan
        "\u{064E}", // Fatha
        "\u{064F}", // Damma
        "\u{0650}", // Kasra
        "\u{0651}", // Shadda
        "\u{0652}", // Sukun
        "\u{0653}", // Maddah
        "\u{0654}", // Hamza Above
        "\u{0655}", // Hamza Below
        "\u{0656}", // Subscript Alef
        "\u{0670}", // Superscript Alef
    ];

    /**
     * Character normalizations
     */
    private const array NORMALIZATIONS = [
        'أ' => 'ا',
        'إ' => 'ا',
        'آ' => 'ا',
        'ٱ' => 'ا',
        'ى' => 'ي',
        'ئ' => 'ي',
        'ؤ' => 'و',
        'ة' => 'ه',
    ];

    /**
     * Remove diacritics from text
     */
    public function removeDiacritics(string $text): string
    {
        return \str_replace(self::DIACRITICS, '', $text);
    }

    /**
     * Normalize Arabic characters
     */
    public function normalizeCharacters(string $text): string
    {
        return \strtr($text, self::NORMALIZATIONS);
    }

    /**
     * Full normalization
     */
    public function normalize(string $text): string
    {
        $text = $this->removeDiacritics($text);
        $text = $this->normalizeCharacters($text);
        $text = \preg_replace('/\s+/u', ' ', $text);

        return \trim($text);
    }

    /**
     * Normalize for comparison
     */
    public function normalizeForComparison(string $text): string
    {
        $text = $this->normalize($text);
        $text = \mb_strtolower($text);

        return $text;
    }

    /**
     * Clean name string
     */
    public function cleanName(string $name): string
    {
        // Remove extra whitespace
        $name = \preg_replace('/\s+/u', ' ', $name);

        // Trim
        $name = \trim($name);

        // Remove surrounding quotes
        $name = \trim($name, "\"'");
        $name = \trim($name, "\u{201C}\u{201D}\u{2018}\u{2019}");

        return $name;
    }

    /**
     * Split name into parts
     *
     * @return array<string>
     */
    public function splitName(string $name): array
    {
        $name = $this->cleanName($name);

        return \preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    /**
     * Check if string contains Arabic
     */
    public function containsArabic(string $text): bool
    {
        return (bool) \preg_match('/[\x{0600}-\x{06FF}]/u', $text);
    }

    /**
     * Check if string is purely Arabic
     */
    public function isPureArabic(string $text): bool
    {
        $cleaned = \preg_replace('/\s/u', '', $text);

        return (bool) \preg_match('/^[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}]+$/u', $cleaned);
    }
}
