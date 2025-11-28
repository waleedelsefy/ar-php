<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\PrayerTimes;

/**
 * Configuration for Prayer Times Module - PHP 8.4
 *
 * @package ArPHP\Core\Modules\PrayerTimes
 */
final readonly class Config
{
    // Calculation Methods
    public const string METHOD_MWL = 'mwl';           // Muslim World League
    public const string METHOD_ISNA = 'isna';         // Islamic Society of North America
    public const string METHOD_EGYPT = 'egypt';       // Egyptian General Authority
    public const string METHOD_MAKKAH = 'makkah';     // Umm al-Qura, Makkah
    public const string METHOD_KARACHI = 'karachi';   // University of Islamic Sciences, Karachi
    public const string METHOD_TEHRAN = 'tehran';     // Institute of Geophysics, Tehran
    public const string METHOD_JAFARI = 'jafari';     // Shia Ithna-Ashari (Jafari)
    public const string METHOD_GULF = 'gulf';         // Gulf Region
    public const string METHOD_KUWAIT = 'kuwait';     // Kuwait
    public const string METHOD_QATAR = 'qatar';       // Qatar
    public const string METHOD_SINGAPORE = 'singapore'; // Singapore
    public const string METHOD_TURKEY = 'turkey';     // Turkey (Diyanet)
    public const string METHOD_CUSTOM = 'custom';     // Custom

    /**
     * Method parameters: [fajr_angle, isha_angle, maghrib_minutes, isha_minutes]
     * @var array<string, array{fajr: float, isha: float, maghrib: float, midnight: string}>
     */
    public const array METHOD_PARAMS = [
        self::METHOD_MWL => [
            'fajr' => 18.0,
            'isha' => 17.0,
            'maghrib' => 0.0,
            'midnight' => 'standard',
        ],
        self::METHOD_ISNA => [
            'fajr' => 15.0,
            'isha' => 15.0,
            'maghrib' => 0.0,
            'midnight' => 'standard',
        ],
        self::METHOD_EGYPT => [
            'fajr' => 19.5,
            'isha' => 17.5,
            'maghrib' => 0.0,
            'midnight' => 'standard',
        ],
        self::METHOD_MAKKAH => [
            'fajr' => 18.5,
            'isha' => 90.0, // 90 minutes after maghrib
            'maghrib' => 0.0,
            'midnight' => 'standard',
        ],
        self::METHOD_KARACHI => [
            'fajr' => 18.0,
            'isha' => 18.0,
            'maghrib' => 0.0,
            'midnight' => 'standard',
        ],
        self::METHOD_TEHRAN => [
            'fajr' => 17.7,
            'isha' => 14.0,
            'maghrib' => 4.5,
            'midnight' => 'jafari',
        ],
        self::METHOD_JAFARI => [
            'fajr' => 16.0,
            'isha' => 14.0,
            'maghrib' => 4.0,
            'midnight' => 'jafari',
        ],
        self::METHOD_GULF => [
            'fajr' => 19.5,
            'isha' => 90.0,
            'maghrib' => 0.0,
            'midnight' => 'standard',
        ],
        self::METHOD_KUWAIT => [
            'fajr' => 18.0,
            'isha' => 17.5,
            'maghrib' => 0.0,
            'midnight' => 'standard',
        ],
        self::METHOD_QATAR => [
            'fajr' => 18.0,
            'isha' => 90.0,
            'maghrib' => 0.0,
            'midnight' => 'standard',
        ],
        self::METHOD_SINGAPORE => [
            'fajr' => 20.0,
            'isha' => 18.0,
            'maghrib' => 0.0,
            'midnight' => 'standard',
        ],
        self::METHOD_TURKEY => [
            'fajr' => 18.0,
            'isha' => 17.0,
            'maghrib' => 0.0,
            'midnight' => 'standard',
        ],
    ];

    // Asr Juristic Methods
    public const string ASR_STANDARD = 'standard';  // Shafi'i, Maliki, Hanbali
    public const string ASR_HANAFI = 'hanafi';      // Hanafi

    // High Latitude Methods
    public const string HIGHLAT_NONE = 'none';
    public const string HIGHLAT_MIDNIGHT = 'midnight';  // Middle of night
    public const string HIGHLAT_ONESEVENTH = 'oneseventh';  // 1/7th of night
    public const string HIGHLAT_ANGLE = 'angle';  // Angle-based

    // Time Formats
    public const string FORMAT_24H = '24h';
    public const string FORMAT_12H = '12h';
    public const string FORMAT_FLOAT = 'float';

    // Prayer Names
    /** @var array<string, string> */
    public const array PRAYER_NAMES_AR = [
        'fajr' => 'الفجر',
        'sunrise' => 'الشروق',
        'dhuhr' => 'الظهر',
        'asr' => 'العصر',
        'maghrib' => 'المغرب',
        'isha' => 'العشاء',
        'midnight' => 'منتصف الليل',
    ];

    /** @var array<string, string> */
    public const array PRAYER_NAMES_EN = [
        'fajr' => 'Fajr',
        'sunrise' => 'Sunrise',
        'dhuhr' => 'Dhuhr',
        'asr' => 'Asr',
        'maghrib' => 'Maghrib',
        'isha' => 'Isha',
        'midnight' => 'Midnight',
    ];

    // Kaaba coordinates (Makkah)
    public const float KAABA_LATITUDE = 21.4225;
    public const float KAABA_LONGITUDE = 39.8262;

    // Default settings
    public const string DEFAULT_METHOD = self::METHOD_MWL;
    public const string DEFAULT_ASR_METHOD = self::ASR_STANDARD;
    public const string DEFAULT_HIGHLAT_METHOD = self::HIGHLAT_MIDNIGHT;
    public const string DEFAULT_TIME_FORMAT = self::FORMAT_24H;
    public const float DEFAULT_ELEVATION = 0.0;

    /** @var array<string> */
    public const array VALID_METHODS = [
        self::METHOD_MWL,
        self::METHOD_ISNA,
        self::METHOD_EGYPT,
        self::METHOD_MAKKAH,
        self::METHOD_KARACHI,
        self::METHOD_TEHRAN,
        self::METHOD_JAFARI,
        self::METHOD_GULF,
        self::METHOD_KUWAIT,
        self::METHOD_QATAR,
        self::METHOD_SINGAPORE,
        self::METHOD_TURKEY,
        self::METHOD_CUSTOM,
    ];

    /** @var array<string> */
    public const array VALID_ASR_METHODS = [
        self::ASR_STANDARD,
        self::ASR_HANAFI,
    ];

    /** @var array<string> */
    public const array VALID_HIGHLAT_METHODS = [
        self::HIGHLAT_NONE,
        self::HIGHLAT_MIDNIGHT,
        self::HIGHLAT_ONESEVENTH,
        self::HIGHLAT_ANGLE,
    ];

    /** @var array<string> */
    public const array VALID_TIME_FORMATS = [
        self::FORMAT_24H,
        self::FORMAT_12H,
        self::FORMAT_FLOAT,
    ];
}
