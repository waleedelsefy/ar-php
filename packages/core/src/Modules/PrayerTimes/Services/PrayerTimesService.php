<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\PrayerTimes\Services;

use DateTimeImmutable;
use DateTimeInterface;
use ArPHP\Core\Contracts\ServiceInterface;
use ArPHP\Core\Modules\PrayerTimes\Config;
use ArPHP\Core\Modules\PrayerTimes\Contracts\PrayerTimesInterface;
use ArPHP\Core\Modules\PrayerTimes\Exceptions\PrayerTimesException;
use ArPHP\Core\Modules\PrayerTimes\Helpers\AstronomyHelper;
use ArPHP\Core\Modules\PrayerTimes\Helpers\TimeFormatterHelper;

/**
 * Prayer Times Service - PHP 8.4
 *
 * Calculates Islamic prayer times based on astronomical algorithms.
 *
 * @package ArPHP\Core\Modules\PrayerTimes\Services
 */
final class PrayerTimesService implements PrayerTimesInterface, ServiceInterface
{
    private string $method;
    private string $asrMethod;
    private string $highLatMethod;
    private string $timeFormat;
    private float $elevation;

    /** @var array<string, float> */
    private array $offsets = [
        'fajr' => 0,
        'sunrise' => 0,
        'dhuhr' => 0,
        'asr' => 0,
        'maghrib' => 0,
        'isha' => 0,
    ];

    /** @var array{fajr: float, isha: float, maghrib: float, midnight: string}|null */
    private ?array $customParams = null;

    public function __construct(
        string $method = Config::DEFAULT_METHOD,
        string $asrMethod = Config::DEFAULT_ASR_METHOD,
        string $highLatMethod = Config::DEFAULT_HIGHLAT_METHOD,
        string $timeFormat = Config::DEFAULT_TIME_FORMAT,
        float $elevation = Config::DEFAULT_ELEVATION
    ) {
        $this->setMethod($method);
        $this->setAsrMethod($asrMethod);
        $this->setHighLatitudeMethod($highLatMethod);
        $this->setTimeFormat($timeFormat);
        $this->elevation = \max(0.0, $elevation);
    }

    public function getServiceName(): string
    {
        return 'prayer_times';
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return [
            'method' => $this->method,
            'asr_method' => $this->asrMethod,
            'high_lat_method' => $this->highLatMethod,
            'time_format' => $this->timeFormat,
            'elevation' => $this->elevation,
            'offsets' => $this->offsets,
        ];
    }

    public function isAvailable(): bool
    {
        return true;
    }

    /**
     * @return array{fajr: string, sunrise: string, dhuhr: string, asr: string, maghrib: string, isha: string, midnight: string}
     */
    public function getTimes(
        DateTimeInterface $date,
        float $latitude,
        float $longitude,
        ?float $elevation = null
    ): array {
        $this->validateCoordinates($latitude, $longitude);

        $elevation ??= $this->elevation;
        $timezone = $this->getTimezone($date);

        $jd = AstronomyHelper::julianDate(
            (int) $date->format('Y'),
            (int) $date->format('n'),
            (int) $date->format('j')
        );

        $times = $this->computeTimes($jd, $latitude, $longitude, $elevation, $timezone);

        return $this->formatTimes($times);
    }

    public function getFajr(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return $this->getTimes($date, $latitude, $longitude)['fajr'];
    }

    public function getSunrise(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return $this->getTimes($date, $latitude, $longitude)['sunrise'];
    }

    public function getDhuhr(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return $this->getTimes($date, $latitude, $longitude)['dhuhr'];
    }

    public function getAsr(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return $this->getTimes($date, $latitude, $longitude)['asr'];
    }

    public function getMaghrib(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return $this->getTimes($date, $latitude, $longitude)['maghrib'];
    }

    public function getIsha(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return $this->getTimes($date, $latitude, $longitude)['isha'];
    }

    /**
     * @return array{name: string, time: string, remaining: int}
     */
    public function getNextPrayer(float $latitude, float $longitude): array
    {
        $now = new DateTimeImmutable();
        $currentTime = (float) $now->format('G') + (float) $now->format('i') / 60;

        $times = $this->getTimes($now, $latitude, $longitude);
        $prayers = ['fajr', 'sunrise', 'dhuhr', 'asr', 'maghrib', 'isha'];

        foreach ($prayers as $prayer) {
            $prayerTime = TimeFormatterHelper::toDecimal($times[$prayer]);

            if ($prayerTime > $currentTime) {
                return [
                    'name' => $prayer,
                    'time' => $times[$prayer],
                    'remaining' => TimeFormatterHelper::diffInMinutes($currentTime, $prayerTime),
                ];
            }
        }

        // Next prayer is Fajr tomorrow
        $tomorrow = $now->modify('+1 day');
        $tomorrowTimes = $this->getTimes($tomorrow, $latitude, $longitude);
        $fajrTime = TimeFormatterHelper::toDecimal($tomorrowTimes['fajr']);

        return [
            'name' => 'fajr',
            'time' => $tomorrowTimes['fajr'],
            'remaining' => TimeFormatterHelper::diffInMinutes($currentTime, $fajrTime + 24),
        ];
    }

    public function getQiblaDirection(float $latitude, float $longitude): float
    {
        $this->validateCoordinates($latitude, $longitude);

        return AstronomyHelper::bearing(
            $latitude,
            $longitude,
            Config::KAABA_LATITUDE,
            Config::KAABA_LONGITUDE
        );
    }

    public function setMethod(string $method): self
    {
        if (!\in_array($method, Config::VALID_METHODS, true)) {
            throw PrayerTimesException::invalidMethod($method);
        }

        $this->method = $method;
        return $this;
    }

    public function setAsrMethod(string $method): self
    {
        if (!\in_array($method, Config::VALID_ASR_METHODS, true)) {
            throw PrayerTimesException::invalidAsrMethod($method);
        }

        $this->asrMethod = $method;
        return $this;
    }

    public function setHighLatitudeMethod(string $method): self
    {
        if (!\in_array($method, Config::VALID_HIGHLAT_METHODS, true)) {
            throw PrayerTimesException::invalidHighLatitudeMethod($method);
        }

        $this->highLatMethod = $method;
        return $this;
    }

    public function setTimeFormat(string $format): self
    {
        if (!\in_array($format, Config::VALID_TIME_FORMATS, true)) {
            throw PrayerTimesException::invalidTimeFormat($format);
        }

        $this->timeFormat = $format;
        return $this;
    }

    /**
     * Set time offset for a specific prayer (in minutes)
     */
    public function setOffset(string $prayer, float $minutes): self
    {
        if (isset($this->offsets[$prayer])) {
            $this->offsets[$prayer] = $minutes;
        }

        return $this;
    }

    /**
     * Set all time offsets
     *
     * @param array<string, float> $offsets
     */
    public function setOffsets(array $offsets): self
    {
        foreach ($offsets as $prayer => $minutes) {
            $this->setOffset($prayer, $minutes);
        }

        return $this;
    }

    /**
     * Set custom calculation parameters
     *
     * @param array{fajr?: float, isha?: float, maghrib?: float, midnight?: string} $params
     */
    public function setCustomParams(array $params): self
    {
        $this->customParams = [
            'fajr' => $params['fajr'] ?? 18.0,
            'isha' => $params['isha'] ?? 17.0,
            'maghrib' => $params['maghrib'] ?? 0.0,
            'midnight' => $params['midnight'] ?? 'standard',
        ];
        $this->method = Config::METHOD_CUSTOM;

        return $this;
    }

    public function setElevation(float $elevation): self
    {
        if ($elevation < 0) {
            throw PrayerTimesException::invalidElevation($elevation);
        }

        $this->elevation = $elevation;
        return $this;
    }

    /**
     * Get method parameters
     *
     * @return array{fajr: float, isha: float, maghrib: float, midnight: string}
     */
    private function getMethodParams(): array
    {
        if ($this->method === Config::METHOD_CUSTOM && $this->customParams !== null) {
            return $this->customParams;
        }

        return Config::METHOD_PARAMS[$this->method] ?? Config::METHOD_PARAMS[Config::METHOD_MWL];
    }

    /**
     * Compute prayer times for a given date and location
     *
     * @return array<string, float>
     */
    private function computeTimes(
        float $jd,
        float $latitude,
        float $longitude,
        float $elevation,
        float $timezone
    ): array {
        $params = $this->getMethodParams();
        $sun = AstronomyHelper::sunPosition($jd);

        $dhuhr = AstronomyHelper::midDay($sun['equation'], $longitude, $timezone);

        $sunrise = $this->computeSunriseAngle($elevation);
        $sunriseTime = $dhuhr + AstronomyHelper::sunAngleTime($sunrise, $latitude, $sun['declination'], true);
        $sunsetTime = $dhuhr + AstronomyHelper::sunAngleTime($sunrise, $latitude, $sun['declination']);

        $fajrAngle = -$params['fajr'];
        $fajrTime = $dhuhr + AstronomyHelper::sunAngleTime($fajrAngle, $latitude, $sun['declination'], true);

        $asrFactor = $this->asrMethod === Config::ASR_HANAFI ? 2.0 : 1.0;
        $asrOffset = AstronomyHelper::asrTime($asrFactor, $latitude, $sun['declination']);
        $asrTime = $asrOffset !== null ? $dhuhr - $asrOffset : $dhuhr + 4;

        $maghribTime = $sunsetTime;
        if ($params['maghrib'] > 0) {
            $maghribTime = TimeFormatterHelper::addMinutes($sunsetTime, $params['maghrib']);
        }

        $ishaTime = $this->computeIshaTime($dhuhr, $sunsetTime, $latitude, $sun['declination'], $params);

        $midnight = $this->computeMidnight($sunsetTime, $fajrTime, $params['midnight']);

        // Apply high latitude adjustments
        $times = [
            'fajr' => $fajrTime,
            'sunrise' => $sunriseTime,
            'dhuhr' => $dhuhr,
            'asr' => $asrTime,
            'maghrib' => $maghribTime,
            'isha' => $ishaTime,
            'midnight' => $midnight,
        ];

        $times = $this->adjustHighLatitude($times, $latitude);

        return $this->applyOffsets($times);
    }

    /**
     * Compute sunrise angle based on elevation
     */
    private function computeSunriseAngle(float $elevation): float
    {
        $angle = 0.833 + 0.0347 * \sqrt($elevation);
        return -$angle;
    }

    /**
     * Compute Isha time
     */
    private function computeIshaTime(
        float $dhuhr,
        float $sunset,
        float $latitude,
        float $declination,
        array $params
    ): float {
        if ($params['isha'] > 30) {
            // Isha parameter is in minutes after Maghrib
            return TimeFormatterHelper::addMinutes($sunset, $params['isha']);
        }

        $ishaAngle = -$params['isha'];
        $offset = AstronomyHelper::sunAngleTime($ishaAngle, $latitude, $declination);

        return $offset !== null ? $dhuhr - $offset : $sunset + 1.5;
    }

    /**
     * Compute midnight based on method
     */
    private function computeMidnight(float $sunset, float $fajr, string $method): float
    {
        if ($method === 'jafari') {
            // Jafari: sunset to fajr
            $diff = $fajr < $sunset ? $fajr + 24 - $sunset : $fajr - $sunset;
            return AstronomyHelper::fixHour($sunset + $diff / 2);
        }

        // Standard: sunset to sunrise (approximated as sunset + 12)
        return AstronomyHelper::fixHour($sunset + 6);
    }

    /**
     * Adjust times for high latitudes
     *
     * @param array<string, float> $times
     * @return array<string, float>
     */
    private function adjustHighLatitude(array $times, float $latitude): array
    {
        if ($this->highLatMethod === Config::HIGHLAT_NONE || \abs($latitude) < 48) {
            return $times;
        }

        $nightTime = $this->getNightPortion($times);

        $fajrDiff = $nightTime['fajr'] * ($times['sunrise'] - $times['fajr']);
        $ishaDiff = $nightTime['isha'] * ($times['isha'] - $times['maghrib']);

        if (!\is_finite($times['fajr']) || $fajrDiff > ($times['sunrise'] - $times['fajr'])) {
            $times['fajr'] = $times['sunrise'] - $fajrDiff;
        }

        if (!\is_finite($times['isha']) || $ishaDiff > ($times['isha'] - $times['maghrib'])) {
            $times['isha'] = $times['maghrib'] + $ishaDiff;
        }

        return $times;
    }

    /**
     * Get night portion for high latitude adjustment
     *
     * @param array<string, float> $times
     * @return array{fajr: float, isha: float}
     */
    private function getNightPortion(array $times): array
    {
        $params = $this->getMethodParams();

        return match ($this->highLatMethod) {
            Config::HIGHLAT_ONESEVENTH => [
                'fajr' => 1 / 7,
                'isha' => 1 / 7,
            ],
            Config::HIGHLAT_ANGLE => [
                'fajr' => $params['fajr'] / 60,
                'isha' => $params['isha'] / 60,
            ],
            default => [ // HIGHLAT_MIDNIGHT
                'fajr' => 0.5,
                'isha' => 0.5,
            ],
        };
    }

    /**
     * Apply time offsets
     *
     * @param array<string, float> $times
     * @return array<string, float>
     */
    private function applyOffsets(array $times): array
    {
        foreach ($this->offsets as $prayer => $offset) {
            if (isset($times[$prayer]) && $offset !== 0.0) {
                $times[$prayer] = TimeFormatterHelper::addMinutes($times[$prayer], $offset);
            }
        }

        return $times;
    }

    /**
     * Format all times according to time format setting
     *
     * @param array<string, float> $times
     * @return array<string, string>
     */
    private function formatTimes(array $times): array
    {
        $formatted = [];

        foreach ($times as $prayer => $time) {
            $formatted[$prayer] = TimeFormatterHelper::format($time, $this->timeFormat);
        }

        return $formatted;
    }

    /**
     * Get timezone offset from date
     */
    private function getTimezone(DateTimeInterface $date): float
    {
        return $date->getOffset() / 3600.0;
    }

    /**
     * Validate coordinates
     */
    private function validateCoordinates(float $latitude, float $longitude): void
    {
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            throw PrayerTimesException::invalidCoordinates($latitude, $longitude);
        }
    }
}
