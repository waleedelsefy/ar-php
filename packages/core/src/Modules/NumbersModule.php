<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;
use ArPHP\Core\Contracts\ServiceInterface;

/**
 * Numbers Service - Arabic Number Conversion
 * 
 * Handles conversion between Arabic-Indic numerals and Western numerals
 */
class NumbersService implements ServiceInterface
{
    /**
     * Arabic-Indic numerals (Eastern Arabic numerals)
     * 
     * @var array<int|string, string>
     */
    private const ARABIC_INDIC = [
        '0' => '٠',
        '1' => '١',
        '2' => '٢',
        '3' => '٣',
        '4' => '٤',
        '5' => '٥',
        '6' => '٦',
        '7' => '٧',
        '8' => '٨',
        '9' => '٩',
    ];

    /**
     * Number words in Arabic
     * 
     * @var array<int, string>
     */
    private const ARABIC_WORDS = [
        0 => 'صفر',
        1 => 'واحد',
        2 => 'اثنان',
        3 => 'ثلاثة',
        4 => 'أربعة',
        5 => 'خمسة',
        6 => 'ستة',
        7 => 'سبعة',
        8 => 'ثمانية',
        9 => 'تسعة',
        10 => 'عشرة',
        11 => 'أحد عشر',
        12 => 'اثنا عشر',
        20 => 'عشرون',
        30 => 'ثلاثون',
        40 => 'أربعون',
        50 => 'خمسون',
        60 => 'ستون',
        70 => 'سبعون',
        80 => 'ثمانون',
        90 => 'تسعون',
        100 => 'مائة',
        1000 => 'ألف',
        1000000 => 'مليون',
        1000000000 => 'مليار',
    ];

    /**
     * Convert Western numerals to Arabic-Indic numerals
     */
    public function toArabicIndic(string $text): string
    {
        return strtr($text, self::ARABIC_INDIC);
    }

    /**
     * Convert Arabic-Indic numerals to Western numerals
     */
    public function toWestern(string $text): string
    {
        return strtr($text, array_flip(self::ARABIC_INDIC));
    }

    /**
     * Convert number to Arabic words (0-999)
     */
    public function toWords(int $number): string
    {
        if ($number < 0 || $number > 999) {
            return (string) $number;
        }

        // Direct mapping for 0-12
        if ($number <= 12) {
            return self::ARABIC_WORDS[$number];
        }

        // Numbers 13-19
        if ($number < 20) {
            $ones = $number - 10;
            return self::ARABIC_WORDS[$ones] . ' عشر';
        }

        // Numbers 20-99
        if ($number < 100) {
            $tens = (int) floor($number / 10) * 10;
            $ones = $number % 10;
            
            if ($ones === 0) {
                return self::ARABIC_WORDS[$tens];
            }
            
            return self::ARABIC_WORDS[$ones] . ' و' . self::ARABIC_WORDS[$tens];
        }

        // Numbers 100-999
        $hundreds = (int) floor($number / 100);
        $remainder = $number % 100;

        $result = match ($hundreds) {
            1 => 'مائة',
            2 => 'مائتان',
            default => self::ARABIC_WORDS[$hundreds] . ' مائة',
        };

        if ($remainder > 0) {
            $result .= ' و' . $this->toWords($remainder);
        }

        return $result;
    }

    /**
     * Check if text contains Arabic-Indic numerals
     */
    public function hasArabicIndic(string $text): bool
    {
        return preg_match('/[٠-٩]/u', $text) === 1;
    }

    /**
     * Check if text contains Western numerals
     */
    public function hasWestern(string $text): bool
    {
        return preg_match('/[0-9]/', $text) === 1;
    }

    /**
     * Extract all numbers from text
     * 
     * @return array<int, string>
     */
    public function extract(string $text): array
    {
        preg_match_all('/[0-9٠-٩]+/', $text, $matches);
        return $matches[0];
    }

    /**
     * Format number with Arabic thousands separator
     */
    public function format(float $number, int $decimals = 0): string
    {
        $formatted = number_format($number, $decimals, '٫', '٬');
        return $this->toArabicIndic($formatted);
    }

    public function getServiceName(): string
    {
        return 'numbers';
    }

    public function getConfig(): array
    {
        return [
            'version' => '1.0.0',
            'features' => ['toArabicIndic', 'toWestern', 'toWords', 'extract', 'format'],
        ];
    }

    public function isAvailable(): bool
    {
        return extension_loaded('mbstring');
    }
}

/**
 * Numbers Module
 * 
 * Registers the Numbers service for Arabic number conversion
 */
class NumbersModule extends AbstractModule
{
    protected string $version = '1.0.0';

    public function getName(): string
    {
        return 'numbers';
    }

    public function register(): void
    {
        Arabic::container()->register('numbers', function () {
            return new NumbersService();
        });
    }

    public function boot(): void
    {
        // Module is ready
    }

    /**
     * @return array<int, string>
     */
    public function getDependencies(): array
    {
        return [];
    }
}
