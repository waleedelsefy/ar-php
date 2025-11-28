<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\HijriCalendar;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * HijriCalendar Facade - PHP 8.4
 *
 * Static facade for easy access to Hijri Calendar functionality.
 *
 * Usage:
 *   use ArPHP\Core\Modules\HijriCalendar\HijriCalendar;
 *
 *   $hijri = HijriCalendar::now();
 *   echo HijriCalendar::format(new DateTime());
 *   $gregorian = HijriCalendar::toGregorian(1447, 5, 28);
 *
 * @package ArPHP\Core\Modules\HijriCalendar
 *
 * @method static array{year: int, month: int, day: int} toHijri(DateTimeInterface $date)
 * @method static DateTimeInterface toGregorian(int $year, int $month, int $day)
 * @method static string format(DateTimeInterface $date, ?string $format = null, ?string $locale = null)
 * @method static array{year: int, month: int, day: int} now()
 * @method static string nowFormatted(?string $format = null, ?string $locale = null)
 * @method static string monthName(int $month, ?string $locale = null)
 * @method static string dayName(DateTimeInterface $date, ?string $locale = null)
 * @method static bool isLeapYear(int $year)
 * @method static int daysInMonth(int $year, int $month)
 * @method static bool isValid(int $year, int $month, int $day)
 * @method static int age(int $birthYear, int $birthMonth, int $birthDay)
 * @method static array<string, mixed> details(DateTimeInterface $date, ?string $locale = null)
 * @method static string|null event(int $month, int $day, ?string $locale = null)
 * @method static DateTimeInterface ramadanStart(int $year)
 * @method static DateTimeInterface eidAlFitr(int $year)
 * @method static DateTimeInterface eidAlAdha(int $year)
 */
final class HijriCalendar
{
    private static ?HijriCalendarModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): HijriCalendarModule
    {
        if (self::$instance === null) {
            self::$instance = new HijriCalendarModule();
            self::$instance->register();
        }

        return self::$instance;
    }

    /**
     * Configure the module with custom settings
     *
     * @param array{
     *     adjustment?: int,
     *     use_arabic_numerals?: bool,
     *     default_locale?: string,
     *     default_format?: string
     * } $config
     */
    public static function configure(array $config): HijriCalendarModule
    {
        self::$instance = new HijriCalendarModule($config);
        self::$instance->register();

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
     * Convert Gregorian date to Hijri
     *
     * @return array{year: int, month: int, day: int}
     */
    public static function toHijri(DateTimeInterface $date): array
    {
        return self::getInstance()->toHijri($date);
    }

    /**
     * Convert Hijri date to Gregorian
     */
    public static function toGregorian(int $year, int $month, int $day): DateTimeInterface
    {
        return self::getInstance()->toGregorian($year, $month, $day);
    }

    /**
     * Format date in Hijri calendar
     */
    public static function format(
        DateTimeInterface $date,
        ?string $format = null,
        ?string $locale = null
    ): string {
        return self::getInstance()->format($date, $format, $locale);
    }

    /**
     * Get current Hijri date
     *
     * @return array{year: int, month: int, day: int}
     */
    public static function now(): array
    {
        return self::getInstance()->now();
    }

    /**
     * Get current Hijri date formatted
     */
    public static function nowFormatted(?string $format = null, ?string $locale = null): string
    {
        return self::getInstance()->nowFormatted($format, $locale);
    }

    /**
     * Get month name
     */
    public static function monthName(int $month, ?string $locale = null): string
    {
        return self::getInstance()->monthName($month, $locale);
    }

    /**
     * Get day name for a date
     */
    public static function dayName(DateTimeInterface $date, ?string $locale = null): string
    {
        return self::getInstance()->dayName($date, $locale);
    }

    /**
     * Check if Hijri year is a leap year
     */
    public static function isLeapYear(int $year): bool
    {
        return self::getInstance()->isLeapYear($year);
    }

    /**
     * Get number of days in Hijri month
     */
    public static function daysInMonth(int $year, int $month): int
    {
        return self::getInstance()->daysInMonth($year, $month);
    }

    /**
     * Validate Hijri date
     */
    public static function isValid(int $year, int $month, int $day): bool
    {
        return self::getInstance()->isValid($year, $month, $day);
    }

    /**
     * Calculate age in Hijri years
     */
    public static function age(int $birthYear, int $birthMonth, int $birthDay): int
    {
        return self::getInstance()->age($birthYear, $birthMonth, $birthDay);
    }

    /**
     * Get full Hijri date details
     *
     * @return array<string, mixed>
     */
    public static function details(DateTimeInterface $date, ?string $locale = null): array
    {
        return self::getInstance()->details($date, $locale);
    }

    /**
     * Get Islamic event for a date
     */
    public static function event(int $month, int $day, ?string $locale = null): ?string
    {
        return self::getInstance()->event($month, $day, $locale);
    }

    /**
     * Get Ramadan start date
     */
    public static function ramadanStart(int $year): DateTimeInterface
    {
        return self::getInstance()->ramadanStart($year);
    }

    /**
     * Get Eid al-Fitr date
     */
    public static function eidAlFitr(int $year): DateTimeInterface
    {
        return self::getInstance()->eidAlFitr($year);
    }

    /**
     * Get Eid al-Adha date
     */
    public static function eidAlAdha(int $year): DateTimeInterface
    {
        return self::getInstance()->eidAlAdha($year);
    }

    /**
     * Set adjustment for moon sighting differences
     */
    public static function setAdjustment(int $days): HijriCalendarModule
    {
        return self::getInstance()->setAdjustment($days);
    }

    /**
     * Set default locale
     */
    public static function setLocale(string $locale): HijriCalendarModule
    {
        return self::getInstance()->setLocale($locale);
    }

    /**
     * Set default format
     */
    public static function setFormat(string $format): HijriCalendarModule
    {
        return self::getInstance()->setFormat($format);
    }

    /**
     * Enable/disable Arabic numerals
     */
    public static function useArabicNumerals(bool $enable): HijriCalendarModule
    {
        return self::getInstance()->useArabicNumerals($enable);
    }
}
