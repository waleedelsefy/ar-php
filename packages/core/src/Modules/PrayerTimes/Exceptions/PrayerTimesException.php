<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\PrayerTimes\Exceptions;

use RuntimeException;

/**
 * Exception for Prayer Times operations
 *
 * @package ArPHP\Core\Modules\PrayerTimes\Exceptions
 */
final class PrayerTimesException extends RuntimeException
{
    public static function invalidCoordinates(float $latitude, float $longitude): self
    {
        return new self(
            \sprintf(
                'Invalid coordinates: latitude=%f, longitude=%f. Latitude must be -90 to 90, longitude must be -180 to 180.',
                $latitude,
                $longitude
            )
        );
    }

    public static function invalidMethod(string $method): self
    {
        return new self(
            \sprintf('Invalid calculation method: %s', $method)
        );
    }

    public static function invalidAsrMethod(string $method): self
    {
        return new self(
            \sprintf('Invalid Asr juristic method: %s. Valid values: standard, hanafi.', $method)
        );
    }

    public static function invalidHighLatitudeMethod(string $method): self
    {
        return new self(
            \sprintf('Invalid high latitude method: %s', $method)
        );
    }

    public static function calculationFailed(string $prayer, string $reason): self
    {
        return new self(
            \sprintf('Failed to calculate %s prayer time: %s', $prayer, $reason)
        );
    }

    public static function invalidTimeFormat(string $format): self
    {
        return new self(
            \sprintf('Invalid time format: %s. Valid formats: 24h, 12h, float.', $format)
        );
    }

    public static function invalidElevation(float $elevation): self
    {
        return new self(
            \sprintf('Invalid elevation: %f. Elevation must be non-negative.', $elevation)
        );
    }
}
