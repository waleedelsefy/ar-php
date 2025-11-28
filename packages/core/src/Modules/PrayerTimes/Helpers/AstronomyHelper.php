<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\PrayerTimes\Helpers;

/**
 * Astronomical calculations helper - PHP 8.4
 *
 * @package ArPHP\Core\Modules\PrayerTimes\Helpers
 */
final readonly class AstronomyHelper
{
    private const float DEG_TO_RAD = M_PI / 180.0;
    private const float RAD_TO_DEG = 180.0 / M_PI;

    /**
     * Convert degrees to radians
     */
    public static function toRadians(float $degrees): float
    {
        return $degrees * self::DEG_TO_RAD;
    }

    /**
     * Convert radians to degrees
     */
    public static function toDegrees(float $radians): float
    {
        return $radians * self::RAD_TO_DEG;
    }

    /**
     * Sine of angle in degrees
     */
    public static function dsin(float $degrees): float
    {
        return \sin(self::toRadians($degrees));
    }

    /**
     * Cosine of angle in degrees
     */
    public static function dcos(float $degrees): float
    {
        return \cos(self::toRadians($degrees));
    }

    /**
     * Tangent of angle in degrees
     */
    public static function dtan(float $degrees): float
    {
        return \tan(self::toRadians($degrees));
    }

    /**
     * Arc sine in degrees
     */
    public static function dasin(float $value): float
    {
        return self::toDegrees(\asin($value));
    }

    /**
     * Arc cosine in degrees
     */
    public static function dacos(float $value): float
    {
        return self::toDegrees(\acos($value));
    }

    /**
     * Arc tangent in degrees
     */
    public static function datan(float $value): float
    {
        return self::toDegrees(\atan($value));
    }

    /**
     * Arc tangent 2 in degrees
     */
    public static function datan2(float $y, float $x): float
    {
        return self::toDegrees(\atan2($y, $x));
    }

    /**
     * Arc cotangent in degrees
     */
    public static function dacot(float $value): float
    {
        return self::toDegrees(\atan(1.0 / $value));
    }

    /**
     * Fix angle to be within 0-360 range
     */
    public static function fixAngle(float $angle): float
    {
        $angle = \fmod($angle, 360.0);
        return $angle < 0 ? $angle + 360.0 : $angle;
    }

    /**
     * Fix hour to be within 0-24 range
     */
    public static function fixHour(float $hour): float
    {
        $hour = \fmod($hour, 24.0);
        return $hour < 0 ? $hour + 24.0 : $hour;
    }

    /**
     * Calculate Julian Date from calendar date
     */
    public static function julianDate(int $year, int $month, int $day): float
    {
        if ($month <= 2) {
            $year -= 1;
            $month += 12;
        }

        $a = (int) \floor($year / 100.0);
        $b = 2 - $a + (int) \floor($a / 4.0);

        return \floor(365.25 * ($year + 4716))
            + \floor(30.6001 * ($month + 1))
            + $day + $b - 1524.5;
    }

    /**
     * Calculate sun position for a given Julian date
     *
     * @return array{declination: float, equation: float}
     */
    public static function sunPosition(float $jd): array
    {
        $d = $jd - 2451545.0;

        $g = self::fixAngle(357.529 + 0.98560028 * $d);
        $q = self::fixAngle(280.459 + 0.98564736 * $d);
        $l = self::fixAngle($q + 1.915 * self::dsin($g) + 0.020 * self::dsin(2 * $g));

        $e = 23.439 - 0.00000036 * $d;
        $ra = self::datan2(self::dcos($e) * self::dsin($l), self::dcos($l)) / 15.0;

        return [
            'declination' => self::dasin(self::dsin($e) * self::dsin($l)),
            'equation' => $q / 15.0 - self::fixHour($ra),
        ];
    }

    /**
     * Calculate the time of sun angle
     *
     * @param float $angle Sun angle
     * @param float $latitude Location latitude
     * @param float $declination Sun declination
     * @param bool $ccw Counter-clockwise direction
     * @return float|null Time in hours or null if no solution
     */
    public static function sunAngleTime(
        float $angle,
        float $latitude,
        float $declination,
        bool $ccw = false
    ): ?float {
        $cosHA = (self::dsin($angle) - self::dsin($latitude) * self::dsin($declination))
            / (self::dcos($latitude) * self::dcos($declination));

        if ($cosHA < -1 || $cosHA > 1) {
            return null;
        }

        $ha = self::dacos($cosHA) / 15.0;

        return $ccw ? $ha : -$ha;
    }

    /**
     * Calculate mid-day (Dhuhr) time
     */
    public static function midDay(float $equation, float $longitude, float $timezone): float
    {
        return self::fixHour(12.0 - $equation - ($longitude / 15.0) + $timezone);
    }

    /**
     * Calculate Asr time based on shadow factor
     *
     * @param float $factor Shadow factor (1 for standard, 2 for Hanafi)
     * @param float $latitude Location latitude
     * @param float $declination Sun declination
     * @return float|null Asr time offset or null
     */
    public static function asrTime(float $factor, float $latitude, float $declination): ?float
    {
        $angle = -self::dacot($factor + self::dtan(\abs($latitude - $declination)));
        return self::sunAngleTime($angle, $latitude, $declination);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    public static function haversineDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371.0; // km

        $dLat = self::toRadians($lat2 - $lat1);
        $dLon = self::toRadians($lon2 - $lon1);

        $a = \sin($dLat / 2) * \sin($dLat / 2)
            + \cos(self::toRadians($lat1)) * \cos(self::toRadians($lat2))
            * \sin($dLon / 2) * \sin($dLon / 2);

        $c = 2 * \atan2(\sqrt($a), \sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Calculate bearing between two coordinates
     */
    public static function bearing(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $lat1 = self::toRadians($lat1);
        $lat2 = self::toRadians($lat2);
        $dLon = self::toRadians($lon2 - $lon1);

        $y = \sin($dLon) * \cos($lat2);
        $x = \cos($lat1) * \sin($lat2) - \sin($lat1) * \cos($lat2) * \cos($dLon);

        $bearing = self::toDegrees(\atan2($y, $x));

        return self::fixAngle($bearing);
    }
}
