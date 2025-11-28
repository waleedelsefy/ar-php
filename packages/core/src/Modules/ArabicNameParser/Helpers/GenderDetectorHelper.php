<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicNameParser\Helpers;

use ArPHP\Core\Modules\ArabicNameParser\Config;

/**
 * Gender Detection Helper - PHP 8.4
 *
 * @package ArPHP\Core\Modules\ArabicNameParser
 */
final readonly class GenderDetectorHelper
{
    /**
     * Detect gender from Arabic name
     *
     * @return 'male'|'female'|'unknown'
     */
    public function detect(string $name): string
    {
        $name = \trim($name);

        if ($name === '') {
            return 'unknown';
        }

        // Split to get first name
        $parts = \preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
        $firstName = $parts[0] ?? $name;

        // Check against known names first
        if ($this->isKnownMaleName($firstName)) {
            return 'male';
        }

        if ($this->isKnownFemaleName($firstName)) {
            return 'female';
        }

        // Check for "عبد" prefix - always male
        if (\str_starts_with($firstName, 'عبد') || \str_starts_with($firstName, 'عبدال')) {
            return 'male';
        }

        // Check endings
        if ($this->hasFemaleEnding($firstName)) {
            return 'female';
        }

        // Names ending in specific male patterns
        if ($this->hasMaleEnding($firstName)) {
            return 'male';
        }

        return 'unknown';
    }

    /**
     * Check if name is in known male names list
     */
    public function isKnownMaleName(string $name): bool
    {
        $normalizedName = $this->normalizeForComparison($name);

        foreach (Config::MALE_NAMES as $maleName) {
            if ($this->normalizeForComparison($maleName) === $normalizedName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if name is in known female names list
     */
    public function isKnownFemaleName(string $name): bool
    {
        $normalizedName = $this->normalizeForComparison($name);

        foreach (Config::FEMALE_NAMES as $femaleName) {
            if ($this->normalizeForComparison($femaleName) === $normalizedName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if name has typical female ending
     */
    public function hasFemaleEnding(string $name): bool
    {
        foreach (Config::FEMALE_ENDINGS as $ending) {
            if (\mb_substr($name, -\mb_strlen($ending)) === $ending) {
                // Exceptions - some male names end in ة
                $exceptions = ['أسامة', 'حمزة', 'معاوية', 'طلحة', 'عبيدة', 'ربيعة'];

                if ($ending === 'ة' && \in_array($name, $exceptions, true)) {
                    return false;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Check if name has typical male ending
     */
    public function hasMaleEnding(string $name): bool
    {
        $malePatterns = ['الدين', 'الله', 'الرحمن'];

        foreach ($malePatterns as $pattern) {
            if (\str_contains($name, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get gender confidence score
     */
    public function confidence(string $name): int
    {
        $parts = \preg_split('/\s+/u', \trim($name), -1, PREG_SPLIT_NO_EMPTY);
        $firstName = $parts[0] ?? $name;

        // Known names have high confidence
        if ($this->isKnownMaleName($firstName) || $this->isKnownFemaleName($firstName)) {
            return 95;
        }

        // "عبد" prefix is very reliable for male
        if (\str_starts_with($firstName, 'عبد')) {
            return 98;
        }

        // Ending-based detection has medium confidence
        if ($this->hasFemaleEnding($firstName)) {
            return 75;
        }

        if ($this->hasMaleEnding($firstName)) {
            return 80;
        }

        return 0;
    }

    /**
     * Normalize name for comparison
     */
    private function normalizeForComparison(string $name): string
    {
        // Remove common diacritics
        $diacritics = [
            "\u{064B}", "\u{064C}", "\u{064D}", "\u{064E}",
            "\u{064F}", "\u{0650}", "\u{0651}", "\u{0652}",
        ];

        $name = \str_replace($diacritics, '', $name);

        // Normalize alef variants
        $name = \str_replace(['أ', 'إ', 'آ'], 'ا', $name);

        return $name;
    }
}
