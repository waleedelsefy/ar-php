<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\HijriCalendar\Helpers;

use ArPHP\Core\Modules\HijriCalendar\Config;

/**
 * Date formatting helper for Hijri dates - PHP 8.4
 *
 * @package ArPHP\Core\Modules\HijriCalendar\Helpers
 */
final readonly class DateFormatterHelper
{
    /**
     * Format Hijri date according to format string
     *
     * Format characters:
     * d - Day of month with leading zero (01-30)
     * j - Day of month without leading zero (1-30)
     * D - Day name abbreviated (3 chars)
     * l - Full day name
     * m - Month with leading zero (01-12)
     * n - Month without leading zero (1-12)
     * F - Full month name
     * M - Month name abbreviated (3 chars)
     * Y - Full year
     * y - Year (2 digits)
     */
    public static function format(
        int $year,
        int $month,
        int $day,
        int $dayOfWeek,
        string $format,
        string $locale = 'ar',
        bool $arabicNumerals = true
    ): string {
        $months = $locale === 'ar' ? Config::MONTHS_AR : Config::MONTHS_EN;
        $days = $locale === 'ar' ? Config::DAYS_AR : Config::DAYS_EN;

        $replacements = [
            'd' => \str_pad((string) $day, 2, '0', STR_PAD_LEFT),
            'j' => (string) $day,
            'D' => \mb_substr($days[$dayOfWeek], 0, 3),
            'l' => $days[$dayOfWeek],
            'm' => \str_pad((string) $month, 2, '0', STR_PAD_LEFT),
            'n' => (string) $month,
            'F' => $months[$month],
            'M' => \mb_substr($months[$month], 0, 3),
            'Y' => (string) $year,
            'y' => \substr((string) $year, -2),
        ];

        $result = '';
        $length = \strlen($format);

        for ($i = 0; $i < $length; $i++) {
            $char = $format[$i];
            $result .= $replacements[$char] ?? $char;
        }

        if ($locale === 'ar' && $arabicNumerals) {
            $result = self::toArabicNumerals($result);
        }

        return $result;
    }

    /**
     * Convert Western numerals to Arabic-Indic numerals
     */
    public static function toArabicNumerals(string $text): string
    {
        return \strtr($text, Config::ARABIC_NUMERALS);
    }

    /**
     * Convert Arabic-Indic numerals to Western numerals
     */
    public static function toWesternNumerals(string $text): string
    {
        return \strtr($text, \array_flip(Config::ARABIC_NUMERALS));
    }

    /**
     * Get relative date string in Arabic
     */
    public static function getRelativeDate(int $daysDiff, string $locale = 'ar'): ?string
    {
        if ($locale === 'ar') {
            return match ($daysDiff) {
                0 => 'اليوم',
                1 => 'غداً',
                -1 => 'أمس',
                2 => 'بعد غد',
                -2 => 'أول أمس',
                default => null,
            };
        }

        return match ($daysDiff) {
            0 => 'Today',
            1 => 'Tomorrow',
            -1 => 'Yesterday',
            default => null,
        };
    }
}
