<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\HijriCalendar\Contracts;

use DateTimeInterface;

/**
 * Interface for Hijri Calendar operations
 *
 * @package ArPHP\Core\Modules\HijriCalendar\Contracts
 */
interface HijriCalendarInterface
{
    /**
     * Convert Gregorian date to Hijri date
     *
     * @return array{year: int, month: int, day: int}
     */
    public function gregorianToHijri(DateTimeInterface $date): array;

    /**
     * Convert Hijri date to Gregorian date
     */
    public function hijriToGregorian(int $year, int $month, int $day): DateTimeInterface;

    /**
     * Get formatted Hijri date string
     */
    public function formatHijri(
        DateTimeInterface $date,
        string $format = 'j F Y',
        string $locale = 'ar'
    ): string;

    /**
     * Get Hijri month name
     */
    public function getMonthName(int $month, string $locale = 'ar'): string;

    /**
     * Get Hijri day name
     */
    public function getDayName(DateTimeInterface $date, string $locale = 'ar'): string;

    /**
     * Check if Hijri year is a leap year
     */
    public function isLeapYear(int $year): bool;

    /**
     * Get number of days in a Hijri month
     */
    public function getDaysInMonth(int $year, int $month): int;

    /**
     * Get number of days in a Hijri year
     */
    public function getDaysInYear(int $year): int;

    /**
     * Validate Hijri date
     */
    public function isValidHijriDate(int $year, int $month, int $day): bool;

    /**
     * Get current Hijri date
     *
     * @return array{year: int, month: int, day: int}
     */
    public function getCurrentHijriDate(): array;

    /**
     * Calculate age in Hijri years
     */
    public function calculateHijriAge(int $birthYear, int $birthMonth, int $birthDay): int;
}
