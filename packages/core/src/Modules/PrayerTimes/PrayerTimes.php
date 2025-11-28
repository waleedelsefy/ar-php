<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\PrayerTimes;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * PrayerTimes Facade - PHP 8.4
 *
 * Static facade for easy access to Prayer Times functionality.
 *
 * Usage:
 *   use ArPHP\Core\Modules\PrayerTimes\PrayerTimes;
 *
 *   $times = PrayerTimes::today(30.0444, 31.2357); // Cairo
 *   $qibla = PrayerTimes::qibla(30.0444, 31.2357);
 *   $next = PrayerTimes::nextPrayer(30.0444, 31.2357);
 *
 * @package ArPHP\Core\Modules\PrayerTimes
 */
final class PrayerTimes
{
    private static ?PrayerTimesModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): PrayerTimesModule
    {
        if (self::$instance === null) {
            self::$instance = new PrayerTimesModule();
            self::$instance->register();
        }

        return self::$instance;
    }

    /**
     * Configure the module with custom settings
     *
     * @param array{
     *     method?: string,
     *     asr_method?: string,
     *     high_lat_method?: string,
     *     time_format?: string,
     *     elevation?: float,
     *     offsets?: array<string, float>
     * } $config
     */
    public static function configure(array $config): PrayerTimesModule
    {
        self::$instance = new PrayerTimesModule($config);
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
     * Get all prayer times for a date and location
     *
     * @return array{fajr: string, sunrise: string, dhuhr: string, asr: string, maghrib: string, isha: string, midnight: string}
     */
    public static function getTimes(
        DateTimeInterface $date,
        float $latitude,
        float $longitude,
        ?float $elevation = null
    ): array {
        return self::getInstance()->getTimes($date, $latitude, $longitude, $elevation);
    }

    /**
     * Get prayer times for today
     *
     * @return array{fajr: string, sunrise: string, dhuhr: string, asr: string, maghrib: string, isha: string, midnight: string}
     */
    public static function today(float $latitude, float $longitude): array
    {
        return self::getInstance()->today($latitude, $longitude);
    }

    /**
     * Get next prayer information
     *
     * @return array{name: string, time: string, remaining: int}
     */
    public static function nextPrayer(float $latitude, float $longitude): array
    {
        return self::getInstance()->nextPrayer($latitude, $longitude);
    }

    /**
     * Get Qibla direction from a location (in degrees from North)
     */
    public static function qibla(float $latitude, float $longitude): float
    {
        return self::getInstance()->qibla($latitude, $longitude);
    }

    /**
     * Get Fajr time
     */
    public static function fajr(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return self::getInstance()->fajr($date, $latitude, $longitude);
    }

    /**
     * Get Sunrise time
     */
    public static function sunrise(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return self::getInstance()->sunrise($date, $latitude, $longitude);
    }

    /**
     * Get Dhuhr time
     */
    public static function dhuhr(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return self::getInstance()->dhuhr($date, $latitude, $longitude);
    }

    /**
     * Get Asr time
     */
    public static function asr(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return self::getInstance()->asr($date, $latitude, $longitude);
    }

    /**
     * Get Maghrib time
     */
    public static function maghrib(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return self::getInstance()->maghrib($date, $latitude, $longitude);
    }

    /**
     * Get Isha time
     */
    public static function isha(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return self::getInstance()->isha($date, $latitude, $longitude);
    }

    /**
     * Set calculation method
     *
     * Available methods:
     * - mwl: Muslim World League
     * - isna: Islamic Society of North America
     * - egypt: Egyptian General Authority
     * - makkah: Umm al-Qura, Makkah
     * - karachi: University of Islamic Sciences, Karachi
     * - tehran: Institute of Geophysics, Tehran
     * - jafari: Shia Ithna-Ashari
     * - gulf: Gulf Region
     * - kuwait: Kuwait
     * - qatar: Qatar
     * - singapore: Singapore
     * - turkey: Turkey (Diyanet)
     */
    public static function setMethod(string $method): PrayerTimesModule
    {
        return self::getInstance()->setMethod($method);
    }

    /**
     * Set Asr juristic method
     *
     * - standard: Shafi'i, Maliki, Hanbali (shadow = object height)
     * - hanafi: Hanafi (shadow = 2x object height)
     */
    public static function setAsrMethod(string $method): PrayerTimesModule
    {
        return self::getInstance()->setAsrMethod($method);
    }

    /**
     * Set high latitude adjustment method
     *
     * - none: No adjustment
     * - midnight: Middle of night
     * - oneseventh: 1/7th of night
     * - angle: Angle-based
     */
    public static function setHighLatMethod(string $method): PrayerTimesModule
    {
        return self::getInstance()->setHighLatMethod($method);
    }

    /**
     * Set time format
     *
     * - 24h: 24-hour format (e.g., "14:30")
     * - 12h: 12-hour format (e.g., "2:30 PM")
     * - float: Decimal hours (e.g., "14.5000")
     */
    public static function setTimeFormat(string $format): PrayerTimesModule
    {
        return self::getInstance()->setTimeFormat($format);
    }

    /**
     * Set elevation in meters
     */
    public static function setElevation(float $elevation): PrayerTimesModule
    {
        return self::getInstance()->setElevation($elevation);
    }

    /**
     * Set time offset for a specific prayer (in minutes)
     */
    public static function setOffset(string $prayer, float $minutes): PrayerTimesModule
    {
        return self::getInstance()->setOffset($prayer, $minutes);
    }

    /**
     * Set custom calculation parameters
     *
     * @param array{fajr?: float, isha?: float, maghrib?: float, midnight?: string} $params
     */
    public static function setCustomParams(array $params): PrayerTimesModule
    {
        return self::getInstance()->setCustomParams($params);
    }

    /**
     * Get prayer name in Arabic
     */
    public static function prayerNameAr(string $prayer): string
    {
        return self::getInstance()->prayerNameAr($prayer);
    }

    /**
     * Get prayer name in English
     */
    public static function prayerNameEn(string $prayer): string
    {
        return self::getInstance()->prayerNameEn($prayer);
    }

    /**
     * Get all available calculation methods
     *
     * @return array<string>
     */
    public static function getAvailableMethods(): array
    {
        return self::getInstance()->getAvailableMethods();
    }

    /**
     * Shorthand for common cities
     */

    /**
     * Get prayer times for Makkah
     *
     * @return array{fajr: string, sunrise: string, dhuhr: string, asr: string, maghrib: string, isha: string, midnight: string}
     */
    public static function makkah(?DateTimeInterface $date = null): array
    {
        self::setMethod(Config::METHOD_MAKKAH);
        return self::getTimes($date ?? new DateTimeImmutable(), 21.4225, 39.8262);
    }

    /**
     * Get prayer times for Madinah
     *
     * @return array{fajr: string, sunrise: string, dhuhr: string, asr: string, maghrib: string, isha: string, midnight: string}
     */
    public static function madinah(?DateTimeInterface $date = null): array
    {
        self::setMethod(Config::METHOD_MAKKAH);
        return self::getTimes($date ?? new DateTimeImmutable(), 24.4686, 39.6142);
    }

    /**
     * Get prayer times for Cairo
     *
     * @return array{fajr: string, sunrise: string, dhuhr: string, asr: string, maghrib: string, isha: string, midnight: string}
     */
    public static function cairo(?DateTimeInterface $date = null): array
    {
        self::setMethod(Config::METHOD_EGYPT);
        return self::getTimes($date ?? new DateTimeImmutable(), 30.0444, 31.2357);
    }

    /**
     * Get prayer times for Dubai
     *
     * @return array{fajr: string, sunrise: string, dhuhr: string, asr: string, maghrib: string, isha: string, midnight: string}
     */
    public static function dubai(?DateTimeInterface $date = null): array
    {
        self::setMethod(Config::METHOD_GULF);
        return self::getTimes($date ?? new DateTimeImmutable(), 25.2048, 55.2708);
    }

    /**
     * Get prayer times for Riyadh
     *
     * @return array{fajr: string, sunrise: string, dhuhr: string, asr: string, maghrib: string, isha: string, midnight: string}
     */
    public static function riyadh(?DateTimeInterface $date = null): array
    {
        self::setMethod(Config::METHOD_MAKKAH);
        return self::getTimes($date ?? new DateTimeImmutable(), 24.7136, 46.6753);
    }

    /**
     * Get prayer times for Istanbul
     *
     * @return array{fajr: string, sunrise: string, dhuhr: string, asr: string, maghrib: string, isha: string, midnight: string}
     */
    public static function istanbul(?DateTimeInterface $date = null): array
    {
        self::setMethod(Config::METHOD_TURKEY);
        return self::getTimes($date ?? new DateTimeImmutable(), 41.0082, 28.9784);
    }
}
