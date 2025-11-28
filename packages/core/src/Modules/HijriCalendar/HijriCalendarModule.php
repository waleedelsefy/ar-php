<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\HijriCalendar;

use DateTimeImmutable;
use DateTimeInterface;
use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\HijriCalendar\Contracts\HijriCalendarInterface;
use ArPHP\Core\Modules\HijriCalendar\Services\HijriCalendarService;

/**
 * Hijri Calendar Module - PHP 8.4
 *
 * Provides Islamic (Hijri) calendar functionality for Arabic PHP applications.
 *
 * Usage:
 *   use ArPHP\Core\Modules\HijriCalendar\HijriCalendar;
 *
 *   $hijri = HijriCalendar::now();
 *   $formatted = HijriCalendar::format(new DateTime());
 *
 * @package ArPHP\Core\Modules\HijriCalendar
 */
final class HijriCalendarModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?HijriCalendarService $service = null;

    /**
     * @param array{
     *     adjustment?: int,
     *     use_arabic_numerals?: bool,
     *     default_locale?: string,
     *     default_format?: string
     * } $config
     */
    public function __construct(
        private array $config = []
    ) {
        $this->config = [
            'adjustment' => $config['adjustment'] ?? Config::DEFAULT_ADJUSTMENT,
            'use_arabic_numerals' => $config['use_arabic_numerals'] ?? true,
            'default_locale' => $config['default_locale'] ?? 'ar',
            'default_format' => $config['default_format'] ?? Config::DEFAULT_FORMAT,
        ];
    }

    public function getName(): string
    {
        return 'hijri_calendar';
    }

    public function register(): void
    {
        $this->service = new HijriCalendarService(
            $this->config['adjustment'],
            $this->config['use_arabic_numerals']
        );
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): HijriCalendarInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * @return array{year: int, month: int, day: int}
     */
    public function toHijri(DateTimeInterface $date): array
    {
        return $this->getService()->gregorianToHijri($date);
    }

    public function toGregorian(int $year, int $month, int $day): DateTimeInterface
    {
        return $this->getService()->hijriToGregorian($year, $month, $day);
    }

    public function format(
        DateTimeInterface $date,
        ?string $format = null,
        ?string $locale = null
    ): string {
        return $this->getService()->formatHijri(
            $date,
            $format ?? $this->config['default_format'],
            $locale ?? $this->config['default_locale']
        );
    }

    /**
     * @return array{year: int, month: int, day: int}
     */
    public function now(): array
    {
        return $this->getService()->getCurrentHijriDate();
    }

    public function nowFormatted(?string $format = null, ?string $locale = null): string
    {
        return $this->format(
            new DateTimeImmutable(),
            $format ?? $this->config['default_format'],
            $locale ?? $this->config['default_locale']
        );
    }

    public function monthName(int $month, ?string $locale = null): string
    {
        return $this->getService()->getMonthName(
            $month,
            $locale ?? $this->config['default_locale']
        );
    }

    public function dayName(DateTimeInterface $date, ?string $locale = null): string
    {
        return $this->getService()->getDayName(
            $date,
            $locale ?? $this->config['default_locale']
        );
    }

    public function isLeapYear(int $year): bool
    {
        return $this->getService()->isLeapYear($year);
    }

    public function daysInMonth(int $year, int $month): int
    {
        return $this->getService()->getDaysInMonth($year, $month);
    }

    public function isValid(int $year, int $month, int $day): bool
    {
        return $this->getService()->isValidHijriDate($year, $month, $day);
    }

    public function age(int $birthYear, int $birthMonth, int $birthDay): int
    {
        return $this->getService()->calculateHijriAge($birthYear, $birthMonth, $birthDay);
    }

    /**
     * @return array<string, mixed>
     */
    public function details(DateTimeInterface $date, ?string $locale = null): array
    {
        return $this->getService()->getFullHijriDate(
            $date,
            $locale ?? $this->config['default_locale']
        );
    }

    public function event(int $month, int $day, ?string $locale = null): ?string
    {
        return $this->getService()->getIslamicEvent(
            $month,
            $day,
            $locale ?? $this->config['default_locale']
        );
    }

    public function ramadanStart(int $year): DateTimeInterface
    {
        return $this->getService()->getRamadanStart($year);
    }

    public function eidAlFitr(int $year): DateTimeInterface
    {
        return $this->getService()->getEidAlFitr($year);
    }

    public function eidAlAdha(int $year): DateTimeInterface
    {
        return $this->getService()->getEidAlAdha($year);
    }

    public function setAdjustment(int $days): self
    {
        $this->config['adjustment'] = \max(-2, \min(2, $days));

        if ($this->service !== null) {
            $this->service->setAdjustment($days);
        }

        return $this;
    }

    public function setLocale(string $locale): self
    {
        $this->config['default_locale'] = $locale;
        return $this;
    }

    public function setFormat(string $format): self
    {
        $this->config['default_format'] = $format;
        return $this;
    }

    public function useArabicNumerals(bool $enable): self
    {
        $this->config['use_arabic_numerals'] = $enable;

        if ($this->service !== null) {
            $this->service->setUseArabicNumerals($enable);
        }

        return $this;
    }

    public static function getIdentifier(): string
    {
        return 'hijri_calendar';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'toHijri',
            'toGregorian',
            'format',
            'now',
            'nowFormatted',
            'monthName',
            'dayName',
            'isLeapYear',
            'daysInMonth',
            'isValid',
            'age',
            'details',
            'event',
            'ramadanStart',
            'eidAlFitr',
            'eidAlAdha',
        ];
    }
}
