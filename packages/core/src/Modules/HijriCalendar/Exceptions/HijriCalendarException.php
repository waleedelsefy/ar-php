<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\HijriCalendar\Exceptions;

use RuntimeException;

/**
 * Exception for Hijri Calendar operations
 *
 * @package ArPHP\Core\Modules\HijriCalendar\Exceptions
 */
final class HijriCalendarException extends RuntimeException
{
    public static function invalidDate(int $year, int $month, int $day): self
    {
        return new self(
            \sprintf('Invalid Hijri date: %d/%d/%d', $year, $month, $day)
        );
    }

    public static function invalidMonth(int $month): self
    {
        return new self(
            \sprintf('Invalid Hijri month: %d. Must be between 1 and 12.', $month)
        );
    }

    public static function invalidDay(int $day, int $maxDay): self
    {
        return new self(
            \sprintf('Invalid Hijri day: %d. Must be between 1 and %d.', $day, $maxDay)
        );
    }

    public static function invalidYear(int $year): self
    {
        return new self(
            \sprintf('Invalid Hijri year: %d. Must be positive.', $year)
        );
    }

    public static function conversionFailed(string $message): self
    {
        return new self(
            \sprintf('Hijri calendar conversion failed: %s', $message)
        );
    }

    public static function unsupportedLocale(string $locale): self
    {
        return new self(
            \sprintf('Unsupported locale: %s. Supported locales: ar, en.', $locale)
        );
    }
}
