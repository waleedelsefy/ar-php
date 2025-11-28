<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\HijriCalendar\Helpers;

use ArPHP\Core\Modules\HijriCalendar\Config;

/**
 * Julian Day Number calculations helper - PHP 8.4
 *
 * @package ArPHP\Core\Modules\HijriCalendar\Helpers
 */
final readonly class JulianDayHelper
{
    /**
     * Convert Gregorian date to Julian Day Number
     */
    public static function gregorianToJD(int $year, int $month, int $day): float
    {
        if ($month <= 2) {
            $year -= 1;
            $month += 12;
        }

        $a = (int) \floor($year / 100);
        $b = 2 - $a + (int) \floor($a / 4);

        return \floor(365.25 * ($year + 4716))
            + \floor(30.6001 * ($month + 1))
            + $day + $b - 1524.5;
    }

    /**
     * Convert Julian Day Number to Gregorian date
     *
     * @return array{year: int, month: int, day: int}
     */
    public static function jdToGregorian(float $jd): array
    {
        $z = (int) \floor($jd + 0.5);
        $f = $jd + 0.5 - $z;

        if ($z < 2299161) {
            $a = $z;
        } else {
            $alpha = (int) \floor(($z - 1867216.25) / 36524.25);
            $a = $z + 1 + $alpha - (int) \floor($alpha / 4);
        }

        $b = $a + 1524;
        $c = (int) \floor(($b - 122.1) / 365.25);
        $d = (int) \floor(365.25 * $c);
        $e = (int) \floor(($b - $d) / 30.6001);

        $day = $b - $d - (int) \floor(30.6001 * $e) + (int) $f;

        $month = $e < 14 ? $e - 1 : $e - 13;
        $year = $month > 2 ? $c - 4716 : $c - 4715;

        return [
            'year' => $year,
            'month' => $month,
            'day' => $day,
        ];
    }

    /**
     * Convert Hijri date to Julian Day Number using arithmetic algorithm
     */
    public static function hijriToJD(int $year, int $month, int $day): float
    {
        $n = $day + (int) \ceil(29.5001 * ($month - 1) + 0.99);
        $q = (int) \floor($year / 30);
        $r = $year % 30;
        $a = (int) \floor((11 * $r + 3) / 30);
        $w = 404 * $q;
        $q1 = (int) \floor(($r - 1) / 19);
        $q2 = (int) \floor(($r - 1) / 11);
        $w += (int) \floor((11 * $r + 14) / 30);

        return (float) ($n + (int) \floor(29.5001 * 11 * $q) + $w + Config::HIJRI_EPOCH_JD - 385);
    }

    /**
     * Convert Julian Day Number to Hijri date using arithmetic algorithm
     *
     * @return array{year: int, month: int, day: int}
     */
    public static function jdToHijri(float $jd): array
    {
        $jd = \floor($jd) + 0.5;
        $l = (int) \floor($jd - Config::HIJRI_EPOCH_JD + 0.5) + 10632;
        $n = (int) \floor(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;

        $j = (int) \floor((10985 - $l) / 5316)
            * (int) \floor((50 * $l) / 17719)
            + (int) \floor($l / 5670)
            * (int) \floor((43 * $l) / 15238);

        $l = $l - (int) \floor((30 - $j) / 15)
            * (int) \floor((17719 * $j) / 50)
            - (int) \floor($j / 16)
            * (int) \floor((15238 * $j) / 43) + 29;

        $month = (int) \floor((24 * $l) / 709);
        $day = $l - (int) \floor((709 * $month) / 24);
        $year = 30 * $n + $j - 30;

        return [
            'year' => $year,
            'month' => $month,
            'day' => $day,
        ];
    }

    /**
     * Get day of week from Julian Day Number (0 = Sunday, 6 = Saturday)
     */
    public static function getDayOfWeek(float $jd): int
    {
        return (int) (($jd + 1.5) % 7);
    }
}
