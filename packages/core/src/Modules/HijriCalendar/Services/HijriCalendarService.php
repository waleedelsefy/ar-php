<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\HijriCalendar\Services;

use DateTimeImmutable;
use DateTimeInterface;
use ArPHP\Core\Contracts\ServiceInterface;
use ArPHP\Core\Modules\HijriCalendar\Config;
use ArPHP\Core\Modules\HijriCalendar\Contracts\HijriCalendarInterface;
use ArPHP\Core\Modules\HijriCalendar\Exceptions\HijriCalendarException;
use ArPHP\Core\Modules\HijriCalendar\Helpers\JulianDayHelper;
use ArPHP\Core\Modules\HijriCalendar\Helpers\DateFormatterHelper;

/**
 * Hijri Calendar Service - PHP 8.4
 *
 * Provides comprehensive Hijri (Islamic) calendar functionality including:
 * - Gregorian to Hijri conversion
 * - Hijri to Gregorian conversion
 * - Date formatting with Arabic/English locales
 * - Leap year calculations
 * - Islamic events lookup
 *
 * @package ArPHP\Core\Modules\HijriCalendar\Services
 */
final class HijriCalendarService implements HijriCalendarInterface, ServiceInterface
{
    /** @var array<string, mixed>|null */
    private ?array $calendarData = null;

    public function __construct(
        private int $adjustment = Config::DEFAULT_ADJUSTMENT,
        private bool $useArabicNumerals = true
    ) {
        $this->adjustment = \max(-2, \min(2, $adjustment));
    }

    public function getServiceName(): string
    {
        return 'hijri_calendar';
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return [
            'adjustment' => $this->adjustment,
            'use_arabic_numerals' => $this->useArabicNumerals,
            'supported_locales' => Config::SUPPORTED_LOCALES,
        ];
    }

    public function isAvailable(): bool
    {
        return true;
    }

    /**
     * @return array{year: int, month: int, day: int}
     */
    public function gregorianToHijri(DateTimeInterface $date): array
    {
        $jd = JulianDayHelper::gregorianToJD(
            (int) $date->format('Y'),
            (int) $date->format('n'),
            (int) $date->format('j')
        );

        return JulianDayHelper::jdToHijri($jd + $this->adjustment);
    }

    public function hijriToGregorian(int $year, int $month, int $day): DateTimeInterface
    {
        $this->validateHijriDate($year, $month, $day);

        $jd = JulianDayHelper::hijriToJD($year, $month, $day) - $this->adjustment;
        $gregorian = JulianDayHelper::jdToGregorian($jd);

        return new DateTimeImmutable(
            \sprintf('%04d-%02d-%02d', $gregorian['year'], $gregorian['month'], $gregorian['day'])
        );
    }

    public function formatHijri(
        DateTimeInterface $date,
        string $format = Config::DEFAULT_FORMAT,
        string $locale = 'ar'
    ): string {
        $this->validateLocale($locale);

        $hijri = $this->gregorianToHijri($date);
        $jd = JulianDayHelper::gregorianToJD(
            (int) $date->format('Y'),
            (int) $date->format('n'),
            (int) $date->format('j')
        );

        return DateFormatterHelper::format(
            $hijri['year'],
            $hijri['month'],
            $hijri['day'],
            JulianDayHelper::getDayOfWeek($jd),
            $format,
            $locale,
            $this->useArabicNumerals && $locale === 'ar'
        );
    }

    public function getMonthName(int $month, string $locale = 'ar'): string
    {
        $this->validateLocale($locale);

        if ($month < 1 || $month > 12) {
            throw HijriCalendarException::invalidMonth($month);
        }

        return $locale === 'ar' ? Config::MONTHS_AR[$month] : Config::MONTHS_EN[$month];
    }

    public function getDayName(DateTimeInterface $date, string $locale = 'ar'): string
    {
        $this->validateLocale($locale);
        $dayOfWeek = (int) $date->format('w');

        return $locale === 'ar' ? Config::DAYS_AR[$dayOfWeek] : Config::DAYS_EN[$dayOfWeek];
    }

    public function isLeapYear(int $year): bool
    {
        return \in_array($year % 30, Config::LEAP_YEAR_POSITIONS, true);
    }

    public function getDaysInMonth(int $year, int $month): int
    {
        if ($month < 1 || $month > 12) {
            throw HijriCalendarException::invalidMonth($month);
        }

        // Check Umm al-Qura adjustments first
        if (isset(Config::UMM_AL_QURA_ADJUSTMENTS[$year])) {
            return Config::UMM_AL_QURA_ADJUSTMENTS[$year][$month - 1];
        }

        // Month 12 in leap years has 30 days
        if ($month === 12 && $this->isLeapYear($year)) {
            return 30;
        }

        // Odd months have 30 days, even months have 29
        return $month % 2 === 1 ? 30 : 29;
    }

    public function getDaysInYear(int $year): int
    {
        return $this->isLeapYear($year) ? 355 : 354;
    }

    public function isValidHijriDate(int $year, int $month, int $day): bool
    {
        if ($year < 1 || $month < 1 || $month > 12) {
            return false;
        }

        return $day >= 1 && $day <= $this->getDaysInMonth($year, $month);
    }

    /**
     * @return array{year: int, month: int, day: int}
     */
    public function getCurrentHijriDate(): array
    {
        return $this->gregorianToHijri(new DateTimeImmutable());
    }

    public function calculateHijriAge(int $birthYear, int $birthMonth, int $birthDay): int
    {
        $this->validateHijriDate($birthYear, $birthMonth, $birthDay);
        $current = $this->getCurrentHijriDate();

        $age = $current['year'] - $birthYear;

        // Adjust if birthday hasn't occurred yet this year
        if (
            $current['month'] < $birthMonth ||
            ($current['month'] === $birthMonth && $current['day'] < $birthDay)
        ) {
            $age--;
        }

        return \max(0, $age);
    }

    /**
     * Get Islamic event for a specific Hijri date
     */
    public function getIslamicEvent(int $month, int $day, string $locale = 'ar'): ?string
    {
        $data = $this->loadCalendarData();
        return $data['islamic_events']["{$month}-{$day}"][$locale] ?? null;
    }

    /**
     * Get all Islamic events
     *
     * @return array<string, string>
     */
    public function getAllIslamicEvents(string $locale = 'ar'): array
    {
        $data = $this->loadCalendarData();
        $events = [];

        foreach ($data['islamic_events'] as $date => $names) {
            $events[$date] = $names[$locale] ?? $names['en'];
        }

        return $events;
    }

    /**
     * Get Hijri date with full details
     *
     * @return array<string, mixed>
     */
    public function getFullHijriDate(DateTimeInterface $date, string $locale = 'ar'): array
    {
        $hijri = $this->gregorianToHijri($date);
        $jd = JulianDayHelper::gregorianToJD(
            (int) $date->format('Y'),
            (int) $date->format('n'),
            (int) $date->format('j')
        );
        $dayOfWeek = JulianDayHelper::getDayOfWeek($jd);

        return [
            'year' => $hijri['year'],
            'month' => $hijri['month'],
            'day' => $hijri['day'],
            'month_name' => $this->getMonthName($hijri['month'], $locale),
            'day_name' => $locale === 'ar' ? Config::DAYS_AR[$dayOfWeek] : Config::DAYS_EN[$dayOfWeek],
            'day_of_week' => $dayOfWeek,
            'is_leap_year' => $this->isLeapYear($hijri['year']),
            'days_in_month' => $this->getDaysInMonth($hijri['year'], $hijri['month']),
            'days_in_year' => $this->getDaysInYear($hijri['year']),
            'event' => $this->getIslamicEvent($hijri['month'], $hijri['day'], $locale),
            'formatted' => $this->formatHijri($date, Config::DEFAULT_FORMAT, $locale),
        ];
    }

    /**
     * Calculate difference between two Hijri dates in days
     *
     * @param array{year: int, month: int, day: int} $from
     * @param array{year: int, month: int, day: int} $to
     */
    public function diffInDays(array $from, array $to): int
    {
        $jd1 = JulianDayHelper::hijriToJD($from['year'], $from['month'], $from['day']);
        $jd2 = JulianDayHelper::hijriToJD($to['year'], $to['month'], $to['day']);

        return (int) \abs($jd2 - $jd1);
    }

    /**
     * Add days to Hijri date
     *
     * @return array{year: int, month: int, day: int}
     */
    public function addDays(int $year, int $month, int $day, int $daysToAdd): array
    {
        $this->validateHijriDate($year, $month, $day);
        $jd = JulianDayHelper::hijriToJD($year, $month, $day) + $daysToAdd;

        return JulianDayHelper::jdToHijri($jd);
    }

    /**
     * Add months to Hijri date
     *
     * @return array{year: int, month: int, day: int}
     */
    public function addMonths(int $year, int $month, int $day, int $monthsToAdd): array
    {
        $this->validateHijriDate($year, $month, $day);

        $totalMonths = ($year - 1) * 12 + $month + $monthsToAdd;
        $newYear = (int) \ceil($totalMonths / 12);
        $newMonth = $totalMonths - ($newYear - 1) * 12;

        if ($newMonth <= 0) {
            $newMonth += 12;
            $newYear--;
        }

        return [
            'year' => $newYear,
            'month' => $newMonth,
            'day' => \min($day, $this->getDaysInMonth($newYear, $newMonth)),
        ];
    }

    /**
     * Get the start date of Ramadan for a given Hijri year
     */
    public function getRamadanStart(int $year): DateTimeInterface
    {
        return $this->hijriToGregorian($year, 9, 1);
    }

    /**
     * Get Eid al-Fitr date for a given Hijri year
     */
    public function getEidAlFitr(int $year): DateTimeInterface
    {
        return $this->hijriToGregorian($year, 10, 1);
    }

    /**
     * Get Eid al-Adha date for a given Hijri year
     */
    public function getEidAlAdha(int $year): DateTimeInterface
    {
        return $this->hijriToGregorian($year, 12, 10);
    }

    public function setAdjustment(int $adjustment): self
    {
        $this->adjustment = \max(-2, \min(2, $adjustment));
        return $this;
    }

    public function getAdjustment(): int
    {
        return $this->adjustment;
    }

    public function setUseArabicNumerals(bool $use): self
    {
        $this->useArabicNumerals = $use;
        return $this;
    }

    private function validateHijriDate(int $year, int $month, int $day): void
    {
        if ($year < 1) {
            throw HijriCalendarException::invalidYear($year);
        }

        if ($month < 1 || $month > 12) {
            throw HijriCalendarException::invalidMonth($month);
        }

        $maxDay = $this->getDaysInMonth($year, $month);
        if ($day < 1 || $day > $maxDay) {
            throw HijriCalendarException::invalidDay($day, $maxDay);
        }
    }

    private function validateLocale(string $locale): void
    {
        if (!\in_array($locale, Config::SUPPORTED_LOCALES, true)) {
            throw HijriCalendarException::unsupportedLocale($locale);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function loadCalendarData(): array
    {
        if ($this->calendarData === null) {
            $path = __DIR__ . '/../Resources/calendar_data.json';
            $content = \file_get_contents($path);

            $this->calendarData = $content !== false
                ? \json_decode($content, true) ?? ['islamic_events' => []]
                : ['islamic_events' => []];
        }

        return $this->calendarData;
    }
}
