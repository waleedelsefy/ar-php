<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicNameParser;

/**
 * Arabic Name Parser Configuration - PHP 8.4
 *
 * @package ArPHP\Core\Modules\ArabicNameParser
 */
final readonly class Config
{
    /**
     * Common Arabic name prefixes (titles)
     */
    public const array PREFIXES = [
        'الشيخ',
        'الدكتور',
        'الأستاذ',
        'المهندس',
        'الحاج',
        'الأمير',
        'السيد',
        'السيدة',
        'الآنسة',
        'الطبيب',
        'المعلم',
        'الأخ',
        'الأخت',
        'شيخ',
        'دكتور',
        'أستاذ',
        'مهندس',
        'حاج',
        'أمير',
        'سيد',
        'سيدة',
        'آنسة',
        'طبيب',
        'معلم',
        'أخ',
        'أخت',
    ];

    /**
     * Common Arabic name suffixes (titles)
     */
    public const array SUFFIXES = [
        'باشا',
        'بك',
        'أفندي',
        'الأول',
        'الثاني',
        'الثالث',
        'الأصغر',
        'الأكبر',
        'جونيور',
        'سينيور',
    ];

    /**
     * Kunya prefixes (أبو، أم، ابن، بنت)
     */
    public const array KUNYA_PREFIXES = [
        'أبو',
        'أبي',
        'أبا',
        'أم',
        'ابن',
        'بن',
        'بنت',
        'بنة',
        'آل',
    ];

    /**
     * Common nisba (origin) suffixes
     */
    public const array NISBA_SUFFIXES = [
        'ي',
        'ية',
        'اوي',
        'اوية',
        'ائي',
        'ائية',
    ];

    /**
     * Common family name prefixes
     */
    public const array FAMILY_PREFIXES = [
        'آل',
        'أل',
        'ال',
        'عائلة',
        'بني',
        'بنو',
        'أولاد',
    ];

    /**
     * Common male name endings
     */
    public const array MALE_ENDINGS = [
        'محمد',
        'أحمد',
        'علي',
        'حسن',
        'حسين',
        'عمر',
        'خالد',
        'سعيد',
        'عبد',
        'الله',
        'الرحمن',
        'الدين',
    ];

    /**
     * Common female name endings
     */
    public const array FEMALE_ENDINGS = [
        'ة',
        'اء',
        'ى',
    ];

    /**
     * Common male first names
     */
    public const array MALE_NAMES = [
        'محمد',
        'أحمد',
        'علي',
        'حسن',
        'حسين',
        'عمر',
        'خالد',
        'سعيد',
        'يوسف',
        'إبراهيم',
        'عبدالله',
        'عبدالرحمن',
        'فهد',
        'سلطان',
        'ناصر',
        'سعود',
        'عبدالعزيز',
        'فيصل',
        'بندر',
        'تركي',
        'عادل',
        'وليد',
        'طارق',
        'ماجد',
        'راشد',
        'مبارك',
        'سالم',
        'جاسم',
        'عيسى',
        'موسى',
    ];

    /**
     * Common female first names
     */
    public const array FEMALE_NAMES = [
        'فاطمة',
        'عائشة',
        'مريم',
        'خديجة',
        'زينب',
        'نورة',
        'سارة',
        'ريم',
        'هند',
        'منى',
        'أمل',
        'سلمى',
        'ليلى',
        'رنا',
        'دانة',
        'لمياء',
        'أسماء',
        'رقية',
        'حفصة',
        'سمية',
        'آمنة',
        'جميلة',
        'كريمة',
        'نادية',
        'سميرة',
        'وفاء',
        'هبة',
        'إيمان',
        'نجلاء',
        'رحاب',
    ];

    /**
     * Format styles
     */
    public const string FORMAT_FULL = 'full';
    public const string FORMAT_FORMAL = 'formal';
    public const string FORMAT_SHORT = 'short';
    public const string FORMAT_INITIALS = 'initials';
    public const string FORMAT_WESTERN = 'western';

    /**
     * Name components
     */
    public const string COMPONENT_PREFIX = 'prefix';
    public const string COMPONENT_FIRST_NAME = 'first_name';
    public const string COMPONENT_FATHER_NAME = 'father_name';
    public const string COMPONENT_GRANDFATHER_NAME = 'grandfather_name';
    public const string COMPONENT_FAMILY_NAME = 'family_name';
    public const string COMPONENT_SUFFIX = 'suffix';
    public const string COMPONENT_KUNYA = 'kunya';
    public const string COMPONENT_LAQAB = 'laqab';
    public const string COMPONENT_NISBA = 'nisba';
}
