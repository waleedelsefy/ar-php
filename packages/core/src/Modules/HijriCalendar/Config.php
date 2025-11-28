<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\HijriCalendar;

/**
 * Configuration for Hijri Calendar Module - PHP 8.4
 *
 * @package ArPHP\Core\Modules\HijriCalendar
 */
final readonly class Config
{
    /** @var array<int, string> Hijri month names in Arabic */
    public const array MONTHS_AR = [
        1 => 'محرم',
        2 => 'صفر',
        3 => 'ربيع الأول',
        4 => 'ربيع الثاني',
        5 => 'جمادى الأولى',
        6 => 'جمادى الآخرة',
        7 => 'رجب',
        8 => 'شعبان',
        9 => 'رمضان',
        10 => 'شوال',
        11 => 'ذو القعدة',
        12 => 'ذو الحجة',
    ];

    /** @var array<int, string> Hijri month names in English */
    public const array MONTHS_EN = [
        1 => 'Muharram',
        2 => 'Safar',
        3 => 'Rabi\' al-Awwal',
        4 => 'Rabi\' al-Thani',
        5 => 'Jumada al-Ula',
        6 => 'Jumada al-Akhirah',
        7 => 'Rajab',
        8 => 'Sha\'ban',
        9 => 'Ramadan',
        10 => 'Shawwal',
        11 => 'Dhu al-Qi\'dah',
        12 => 'Dhu al-Hijjah',
    ];

    /** @var array<int, string> Day names in Arabic */
    public const array DAYS_AR = [
        0 => 'الأحد',
        1 => 'الإثنين',
        2 => 'الثلاثاء',
        3 => 'الأربعاء',
        4 => 'الخميس',
        5 => 'الجمعة',
        6 => 'السبت',
    ];

    /** @var array<int, string> Day names in English */
    public const array DAYS_EN = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public const int DEFAULT_ADJUSTMENT = 0;

    /** @var array<int, array<int, int>> Umm al-Qura calendar correction table */
    public const array UMM_AL_QURA_ADJUSTMENTS = [
        1445 => [30, 29, 30, 29, 30, 29, 30, 29, 30, 29, 30, 29],
        1446 => [30, 29, 30, 30, 29, 30, 29, 30, 29, 30, 29, 30],
        1447 => [29, 30, 29, 30, 30, 29, 30, 29, 30, 29, 30, 29],
    ];

    /** @var array<int, int> Leap year positions in 30-year cycle */
    public const array LEAP_YEAR_POSITIONS = [2, 5, 7, 10, 13, 16, 18, 21, 24, 26, 29];

    public const float HIJRI_EPOCH_JD = 1948439.5;

    public const float AVERAGE_YEAR_LENGTH = 354.36667;

    public const float AVERAGE_MONTH_LENGTH = 29.530589;

    /** @var array<int, string> Supported locales */
    public const array SUPPORTED_LOCALES = ['ar', 'en'];

    public const string DEFAULT_FORMAT = 'j F Y';

    /** @var array<string, string> Arabic-Indic numerals mapping */
    public const array ARABIC_NUMERALS = [
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
}
