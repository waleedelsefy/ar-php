<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\PrayerTimes;

use DateTimeInterface;
use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\PrayerTimes\Contracts\PrayerTimesInterface;
use ArPHP\Core\Modules\PrayerTimes\Services\PrayerTimesService;

/**
 * Prayer Times Module - PHP 8.4
 *
 * Calculates Islamic prayer times based on astronomical algorithms.
 *
 * @package ArPHP\Core\Modules\PrayerTimes
 */
final class PrayerTimesModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?PrayerTimesService $service = null;

    /**
     * @param array{
     *     method?: string,
     *     asr_method?: string,
     *     high_lat_method?: string,
     *     time_format?: string,
     *     elevation?: float,
     *     offsets?: array<string, float>
     * } $config
     */
    public function __construct(
        private array $config = []
    ) {
        $this->config = [
            'method' => $config['method'] ?? Config::DEFAULT_METHOD,
            'asr_method' => $config['asr_method'] ?? Config::DEFAULT_ASR_METHOD,
            'high_lat_method' => $config['high_lat_method'] ?? Config::DEFAULT_HIGHLAT_METHOD,
            'time_format' => $config['time_format'] ?? Config::DEFAULT_TIME_FORMAT,
            'elevation' => $config['elevation'] ?? Config::DEFAULT_ELEVATION,
            'offsets' => $config['offsets'] ?? [],
        ];
    }

    public function getName(): string
    {
        return 'prayer_times';
    }

    public function register(): void
    {
        $this->service = new PrayerTimesService(
            $this->config['method'],
            $this->config['asr_method'],
            $this->config['high_lat_method'],
            $this->config['time_format'],
            $this->config['elevation']
        );

        if (!empty($this->config['offsets'])) {
            $this->service->setOffsets($this->config['offsets']);
        }
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): PrayerTimesInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Get all prayer times for a date and location
     *
     * @return array{fajr: string, sunrise: string, dhuhr: string, asr: string, maghrib: string, isha: string, midnight: string}
     */
    public function getTimes(
        DateTimeInterface $date,
        float $latitude,
        float $longitude,
        ?float $elevation = null
    ): array {
        return $this->getService()->getTimes($date, $latitude, $longitude, $elevation);
    }

    /**
     * Get prayer times for today
     *
     * @return array{fajr: string, sunrise: string, dhuhr: string, asr: string, maghrib: string, isha: string, midnight: string}
     */
    public function today(float $latitude, float $longitude): array
    {
        return $this->getTimes(new \DateTimeImmutable(), $latitude, $longitude);
    }

    /**
     * Get next prayer information
     *
     * @return array{name: string, time: string, remaining: int}
     */
    public function nextPrayer(float $latitude, float $longitude): array
    {
        return $this->getService()->getNextPrayer($latitude, $longitude);
    }

    /**
     * Get Qibla direction from a location
     */
    public function qibla(float $latitude, float $longitude): float
    {
        return $this->getService()->getQiblaDirection($latitude, $longitude);
    }

    /**
     * Get specific prayer time
     */
    public function fajr(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return $this->getService()->getFajr($date, $latitude, $longitude);
    }

    public function sunrise(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return $this->getService()->getSunrise($date, $latitude, $longitude);
    }

    public function dhuhr(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return $this->getService()->getDhuhr($date, $latitude, $longitude);
    }

    public function asr(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return $this->getService()->getAsr($date, $latitude, $longitude);
    }

    public function maghrib(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return $this->getService()->getMaghrib($date, $latitude, $longitude);
    }

    public function isha(DateTimeInterface $date, float $latitude, float $longitude): string
    {
        return $this->getService()->getIsha($date, $latitude, $longitude);
    }

    /**
     * Set calculation method
     */
    public function setMethod(string $method): self
    {
        $this->config['method'] = $method;

        if ($this->service !== null) {
            $this->service->setMethod($method);
        }

        return $this;
    }

    /**
     * Set Asr juristic method
     */
    public function setAsrMethod(string $method): self
    {
        $this->config['asr_method'] = $method;

        if ($this->service !== null) {
            $this->service->setAsrMethod($method);
        }

        return $this;
    }

    /**
     * Set high latitude adjustment method
     */
    public function setHighLatMethod(string $method): self
    {
        $this->config['high_lat_method'] = $method;

        if ($this->service !== null) {
            $this->service->setHighLatitudeMethod($method);
        }

        return $this;
    }

    /**
     * Set time format
     */
    public function setTimeFormat(string $format): self
    {
        $this->config['time_format'] = $format;

        if ($this->service !== null) {
            $this->service->setTimeFormat($format);
        }

        return $this;
    }

    /**
     * Set elevation
     */
    public function setElevation(float $elevation): self
    {
        $this->config['elevation'] = $elevation;

        if ($this->service !== null) {
            $this->service->setElevation($elevation);
        }

        return $this;
    }

    /**
     * Set time offset for a prayer (in minutes)
     */
    public function setOffset(string $prayer, float $minutes): self
    {
        $this->config['offsets'][$prayer] = $minutes;

        if ($this->service !== null) {
            $this->service->setOffset($prayer, $minutes);
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
        if ($this->service !== null) {
            $this->service->setCustomParams($params);
        }

        return $this;
    }

    /**
     * Get prayer name in Arabic
     */
    public function prayerNameAr(string $prayer): string
    {
        return Config::PRAYER_NAMES_AR[$prayer] ?? $prayer;
    }

    /**
     * Get prayer name in English
     */
    public function prayerNameEn(string $prayer): string
    {
        return Config::PRAYER_NAMES_EN[$prayer] ?? $prayer;
    }

    /**
     * Get all available calculation methods
     *
     * @return array<string>
     */
    public function getAvailableMethods(): array
    {
        return Config::VALID_METHODS;
    }

    public static function getIdentifier(): string
    {
        return 'prayer_times';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'getTimes',
            'today',
            'nextPrayer',
            'qibla',
            'fajr',
            'sunrise',
            'dhuhr',
            'asr',
            'maghrib',
            'isha',
        ];
    }
}
