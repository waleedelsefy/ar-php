<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\PrayerTimes\Contracts;

use DateTimeInterface;

/**
 * Interface for Prayer Times calculations
 *
 * @package ArPHP\Core\Modules\PrayerTimes\Contracts
 */
interface PrayerTimesInterface
{
    /**
     * Get all prayer times for a specific date and location
     *
     * @return array{
     *     fajr: string,
     *     sunrise: string,
     *     dhuhr: string,
     *     asr: string,
     *     maghrib: string,
     *     isha: string,
     *     midnight: string
     * }
     */
    public function getTimes(
        DateTimeInterface $date,
        float $latitude,
        float $longitude,
        ?float $elevation
    ): array;

    /**
     * Get Fajr prayer time
     */
    public function getFajr(DateTimeInterface $date, float $latitude, float $longitude): string;

    /**
     * Get Sunrise time
     */
    public function getSunrise(DateTimeInterface $date, float $latitude, float $longitude): string;

    /**
     * Get Dhuhr prayer time
     */
    public function getDhuhr(DateTimeInterface $date, float $latitude, float $longitude): string;

    /**
     * Get Asr prayer time
     */
    public function getAsr(DateTimeInterface $date, float $latitude, float $longitude): string;

    /**
     * Get Maghrib prayer time
     */
    public function getMaghrib(DateTimeInterface $date, float $latitude, float $longitude): string;

    /**
     * Get Isha prayer time
     */
    public function getIsha(DateTimeInterface $date, float $latitude, float $longitude): string;

    /**
     * Get next prayer name and time
     *
     * @return array{name: string, time: string, remaining: int}
     */
    public function getNextPrayer(float $latitude, float $longitude): array;

    /**
     * Get Qibla direction from a location
     */
    public function getQiblaDirection(float $latitude, float $longitude): float;

    /**
     * Set calculation method
     */
    public function setMethod(string $method): self;

    /**
     * Set Asr calculation juristic method
     */
    public function setAsrMethod(string $method): self;

    /**
     * Set high latitude adjustment method
     */
    public function setHighLatitudeMethod(string $method): self;

    /**
     * Set time format
     */
    public function setTimeFormat(string $format): self;
}
