<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\PrayerTimes\Helpers;

use ArPHP\Core\Modules\PrayerTimes\Config;

/**
 * Time formatting helper - PHP 8.4
 *
 * @package ArPHP\Core\Modules\PrayerTimes\Helpers
 */
final readonly class TimeFormatterHelper
{
    /**
     * Format time based on format type
     */
    public static function format(float $time, string $format): string
    {
        if (!\is_finite($time)) {
            return '-----';
        }

        return match ($format) {
            Config::FORMAT_FLOAT => \sprintf('%.4f', $time),
            Config::FORMAT_12H => self::to12HourFormat($time),
            default => self::to24HourFormat($time),
        };
    }

    /**
     * Convert decimal time to 24-hour format
     */
    public static function to24HourFormat(float $time): string
    {
        $time = AstronomyHelper::fixHour($time + 0.5 / 60); // Add 30 seconds for rounding

        $hours = (int) \floor($time);
        $minutes = (int) \floor(($time - $hours) * 60);

        return \sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Convert decimal time to 12-hour format
     */
    public static function to12HourFormat(float $time): string
    {
        $time = AstronomyHelper::fixHour($time + 0.5 / 60);

        $hours = (int) \floor($time);
        $minutes = (int) \floor(($time - $hours) * 60);

        $suffix = $hours >= 12 ? 'PM' : 'AM';
        $hours = $hours % 12;
        $hours = $hours === 0 ? 12 : $hours;

        return \sprintf('%d:%02d %s', $hours, $minutes, $suffix);
    }

    /**
     * Convert 24-hour time string to decimal hours
     */
    public static function toDecimal(string $time): float
    {
        $parts = \explode(':', $time);

        if (\count($parts) < 2) {
            return 0.0;
        }

        $hours = (int) $parts[0];
        $minutes = (int) $parts[1];
        $seconds = isset($parts[2]) ? (int) $parts[2] : 0;

        return $hours + $minutes / 60.0 + $seconds / 3600.0;
    }

    /**
     * Add minutes to time
     */
    public static function addMinutes(float $time, float $minutes): float
    {
        return AstronomyHelper::fixHour($time + $minutes / 60.0);
    }

    /**
     * Calculate time difference in minutes
     */
    public static function diffInMinutes(float $time1, float $time2): int
    {
        $diff = $time2 - $time1;

        if ($diff < 0) {
            $diff += 24;
        }

        return (int) \round($diff * 60);
    }

    /**
     * Format remaining time in human readable format
     *
     * @return array{hours: int, minutes: int, total_minutes: int}
     */
    public static function formatRemaining(int $totalMinutes): array
    {
        return [
            'hours' => (int) \floor($totalMinutes / 60),
            'minutes' => $totalMinutes % 60,
            'total_minutes' => $totalMinutes,
        ];
    }

    /**
     * Format time in Arabic
     */
    public static function toArabicFormat(float $time, bool $use12h = false): string
    {
        $formatted = $use12h ? self::to12HourFormat($time) : self::to24HourFormat($time);

        // Convert to Arabic-Indic numerals
        $arabicNumerals = [
            '0' => '٠', '1' => '١', '2' => '٢', '3' => '٣', '4' => '٤',
            '5' => '٥', '6' => '٦', '7' => '٧', '8' => '٨', '9' => '٩',
        ];

        $formatted = \strtr($formatted, $arabicNumerals);

        if ($use12h) {
            $formatted = \str_replace('AM', 'ص', $formatted);
            $formatted = \str_replace('PM', 'م', $formatted);
        }

        return $formatted;
    }
}
