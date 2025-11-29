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
 * Supports numbers up to Decillion (10^33)
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
     * Basic number words (0-12)
     * @var array<int, string>
     */
    private const ONES = [
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
    ];

    /**
     * Tens (20-90)
     * @var array<int, string>
     */
    private const TENS = [
        20 => 'عشرون',
        30 => 'ثلاثون',
        40 => 'أربعون',
        50 => 'خمسون',
        60 => 'ستون',
        70 => 'سبعون',
        80 => 'ثمانون',
        90 => 'تسعون',
    ];

    /**
     * Large number scales with singular, dual, and plural forms
     * Scale => [singular, dual, plural_3_10, plural_11+]
     * @var array<string, array<string, string>>
     */
    private const SCALES = [
        '1000' => [
            'singular' => 'ألف',
            'dual' => 'ألفان',
            'plural_3_10' => 'آلاف',
            'plural_11' => 'ألفاً',
        ],
        '1000000' => [
            'singular' => 'مليون',
            'dual' => 'مليونان',
            'plural_3_10' => 'ملايين',
            'plural_11' => 'مليوناً',
        ],
        '1000000000' => [
            'singular' => 'مليار',
            'dual' => 'ملياران',
            'plural_3_10' => 'مليارات',
            'plural_11' => 'ملياراً',
        ],
        '1000000000000' => [
            'singular' => 'تريليون',
            'dual' => 'تريليونان',
            'plural_3_10' => 'تريليونات',
            'plural_11' => 'تريليوناً',
        ],
        '1000000000000000' => [
            'singular' => 'كوادريليون',
            'dual' => 'كوادريليونان',
            'plural_3_10' => 'كوادريليونات',
            'plural_11' => 'كوادريليوناً',
        ],
        '1000000000000000000' => [
            'singular' => 'كوينتيليون',
            'dual' => 'كوينتيليونان',
            'plural_3_10' => 'كوينتيليونات',
            'plural_11' => 'كوينتيليوناً',
        ],
        '1000000000000000000000' => [
            'singular' => 'سكستيليون',
            'dual' => 'سكستيليونان',
            'plural_3_10' => 'سكستيليونات',
            'plural_11' => 'سكستيليوناً',
        ],
        '1000000000000000000000000' => [
            'singular' => 'سبتيليون',
            'dual' => 'سبتيليونان',
            'plural_3_10' => 'سبتيليونات',
            'plural_11' => 'سبتيليوناً',
        ],
        '1000000000000000000000000000' => [
            'singular' => 'أوكتيليون',
            'dual' => 'أوكتيليونان',
            'plural_3_10' => 'أوكتيليونات',
            'plural_11' => 'أوكتيليوناً',
        ],
        '1000000000000000000000000000000' => [
            'singular' => 'نونيليون',
            'dual' => 'نونيليونان',
            'plural_3_10' => 'نونيليونات',
            'plural_11' => 'نونيليوناً',
        ],
        '1000000000000000000000000000000000' => [
            'singular' => 'ديسيليون',
            'dual' => 'ديسيليونان',
            'plural_3_10' => 'ديسيليونات',
            'plural_11' => 'ديسيليوناً',
        ],
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
     * Convert number to Arabic words (supports up to Decillion - 10^33)
     * 
     * @param int|float|string $number The number to convert
     * @return string Arabic words representation
     */
    public function toWords(int|float|string $number): string
    {
        // Handle string numbers for very large values
        $numStr = is_string($number) ? $number : (string) $number;
        $numStr = preg_replace('/[^0-9]/', '', $numStr) ?? '0';
        
        // Handle zero
        if ($numStr === '' || $numStr === '0') {
            return self::ONES[0];
        }
        
        // Handle negative
        $negative = false;
        if (is_numeric($number) && $number < 0) {
            $negative = true;
            $numStr = ltrim($numStr, '-');
        }
        
        // Remove leading zeros
        $numStr = ltrim($numStr, '0') ?: '0';
        
        if ($numStr === '0') {
            return self::ONES[0];
        }
        
        $result = $this->convertLargeNumber($numStr);
        
        if ($negative) {
            $result = 'سالب ' . $result;
        }
        
        return $result;
    }

    /**
     * Convert large number string to Arabic words
     */
    private function convertLargeNumber(string $numStr): string
    {
        $length = strlen($numStr);
        
        // For numbers up to 999
        if ($length <= 3) {
            return $this->convertUpTo999((int) $numStr);
        }
        
        // Get scale values sorted descending by length
        $scales = array_keys(self::SCALES);
        usort($scales, fn($a, $b) => strlen((string)$b) - strlen((string)$a));
        
        $parts = [];
        
        foreach ($scales as $scaleStr) {
            $scaleLen = strlen((string)$scaleStr);
            
            if ($length >= $scaleLen) {
                // Calculate how many digits belong to this scale
                $digitCount = $length - $scaleLen + 1;
                $scaleValue = substr($numStr, 0, $digitCount);
                $numStr = substr($numStr, $digitCount);
                $length = strlen($numStr);
                
                $scaleInt = (int) $scaleValue;
                if ($scaleInt > 0) {
                    $parts[] = $this->formatWithScale($scaleInt, self::SCALES[$scaleStr]);
                }
            }
        }
        
        // Handle remaining (less than 1000)
        if ($numStr !== '' && (int) $numStr > 0) {
            $parts[] = $this->convertUpTo999((int) $numStr);
        }
        
        return implode(' و', $parts);
    }

    /**
     * Format number with appropriate scale word
     */
    private function formatWithScale(int $count, array $scale): string
    {
        if ($count === 1) {
            return $scale['singular'];
        }
        
        if ($count === 2) {
            return $scale['dual'];
        }
        
        if ($count >= 3 && $count <= 10) {
            return $this->convertUpTo999($count) . ' ' . $scale['plural_3_10'];
        }
        
        // 11 and above
        return $this->convertUpTo999($count) . ' ' . $scale['plural_11'];
    }

    /**
     * Convert numbers 0-999 to Arabic words
     */
    private function convertUpTo999(int $number): string
    {
        if ($number < 0 || $number > 999) {
            return (string) $number;
        }

        // Direct mapping for 0-12
        if ($number <= 12) {
            return self::ONES[$number];
        }

        // Numbers 13-19
        if ($number < 20) {
            $ones = $number - 10;
            return self::ONES[$ones] . ' عشر';
        }

        // Numbers 20-99
        if ($number < 100) {
            $tens = (int) floor($number / 10) * 10;
            $ones = $number % 10;
            
            if ($ones === 0) {
                return self::TENS[$tens];
            }
            
            return self::ONES[$ones] . ' و' . self::TENS[$tens];
        }

        // Numbers 100-999
        $hundreds = (int) floor($number / 100);
        $remainder = $number % 100;

        $result = match ($hundreds) {
            1 => 'مائة',
            2 => 'مائتان',
            default => self::ONES[$hundreds] . ' مائة',
        };

        if ($remainder > 0) {
            $result .= ' و' . $this->convertUpTo999($remainder);
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
    
    private ?NumbersService $service = null;

    public function getName(): string
    {
        return 'numbers';
    }

    public function register(): void
    {
        $this->service = new NumbersService();
        Arabic::container()->register('numbers', function () {
            return $this->service;
        });
    }

    public function boot(): void
    {
        // Module is ready
    }
    
    private function getService(): NumbersService
    {
        if ($this->service === null) {
            $this->register();
        }
        return $this->service;
    }
    
    /**
     * Convert number to Arabic words
     */
    public function toWords(int|float|string $number): string
    {
        return $this->getService()->toWords($number);
    }
    
    /**
     * Convert Western numerals to Arabic-Indic numerals
     */
    public function toArabicIndic(string $text): string
    {
        return $this->getService()->toArabicIndic($text);
    }
    
    /**
     * Convert Arabic-Indic numerals to Western numerals
     */
    public function toWestern(string $text): string
    {
        return $this->getService()->toWestern($text);
    }
    
    /**
     * Format number with Arabic separators
     */
    public function format(float $number, int $decimals = 0): string
    {
        return $this->getService()->format($number, $decimals);
    }
    
    /**
     * Extract numbers from text
     * @return array<int, string>
     */
    public function extract(string $text): array
    {
        return $this->getService()->extract($text);
    }

    /**
     * @return array<int, string>
     */
    public function getDependencies(): array
    {
        return [];
    }
}
