<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\NER;

/**
 * NER Config - PHP 8.4
 *
 * Named Entity Recognition patterns and lexicons.
 *
 * @package ArPHP\Core\Modules\NER
 */
final readonly class Config
{
    // Entity types
    public const string TYPE_PERSON = 'PERSON';
    public const string TYPE_LOCATION = 'LOCATION';
    public const string TYPE_ORGANIZATION = 'ORGANIZATION';
    public const string TYPE_DATE = 'DATE';
    public const string TYPE_TIME = 'TIME';
    public const string TYPE_MONEY = 'MONEY';
    public const string TYPE_PERCENTAGE = 'PERCENTAGE';
    public const string TYPE_EMAIL = 'EMAIL';
    public const string TYPE_PHONE = 'PHONE';
    public const string TYPE_URL = 'URL';

    /** @var array<string> */
    public const array VALID_TYPES = [
        self::TYPE_PERSON,
        self::TYPE_LOCATION,
        self::TYPE_ORGANIZATION,
        self::TYPE_DATE,
        self::TYPE_TIME,
        self::TYPE_MONEY,
        self::TYPE_PERCENTAGE,
        self::TYPE_EMAIL,
        self::TYPE_PHONE,
        self::TYPE_URL,
    ];

    // Person name prefixes/titles
    /** @var array<string> */
    public const array NAME_PREFIXES = [
        'السيد', 'السيدة', 'الآنسة', 'الأستاذ', 'الأستاذة',
        'الدكتور', 'الدكتورة', 'المهندس', 'المهندسة',
        'الشيخ', 'الشيخة', 'الأمير', 'الأميرة', 'الملك', 'الملكة',
        'الرئيس', 'الوزير', 'القاضي', 'المحامي',
        'أبو', 'أم', 'ابن', 'بنت', 'آل', 'بن', 'عبد',
    ];

    // Common Arabic first names
    /** @var array<string> */
    public const array COMMON_NAMES = [
        // Male names
        'محمد', 'أحمد', 'علي', 'عمر', 'عثمان', 'خالد', 'سعيد', 'يوسف',
        'إبراهيم', 'عبدالله', 'عبدالرحمن', 'صالح', 'حسن', 'حسين', 'فهد',
        'سلطان', 'ناصر', 'فيصل', 'سعود', 'بندر', 'تركي', 'ماجد',
        // Female names
        'فاطمة', 'عائشة', 'مريم', 'خديجة', 'زينب', 'سارة', 'نورة',
        'هند', 'ليلى', 'سلمى', 'هدى', 'منى', 'رنا', 'دانة', 'لمياء',
    ];

    // Location indicators
    /** @var array<string> */
    public const array LOCATION_INDICATORS = [
        'مدينة', 'محافظة', 'منطقة', 'دولة', 'مملكة', 'جمهورية',
        'إمارة', 'سلطنة', 'ولاية', 'مقاطعة', 'إقليم',
        'شارع', 'طريق', 'حي', 'جادة', 'ميدان', 'ساحة',
        'جبل', 'نهر', 'بحر', 'خليج', 'محيط', 'صحراء', 'وادي',
        'شمال', 'جنوب', 'شرق', 'غرب',
    ];

    // Common locations
    /** @var array<string> */
    public const array COMMON_LOCATIONS = [
        // Countries
        'السعودية', 'مصر', 'الإمارات', 'الكويت', 'قطر', 'البحرين', 'عمان',
        'الأردن', 'لبنان', 'سوريا', 'العراق', 'فلسطين', 'اليمن', 'ليبيا',
        'تونس', 'الجزائر', 'المغرب', 'السودان', 'موريتانيا',
        // Cities
        'الرياض', 'جدة', 'مكة', 'المدينة', 'الدمام', 'الطائف',
        'القاهرة', 'الإسكندرية', 'دبي', 'أبوظبي', 'الدوحة', 'الكويت',
        'عمان', 'بيروت', 'دمشق', 'بغداد', 'القدس', 'رام الله',
    ];

    // Organization indicators
    /** @var array<string> */
    public const array ORGANIZATION_INDICATORS = [
        'شركة', 'مؤسسة', 'منظمة', 'هيئة', 'جمعية', 'اتحاد', 'رابطة',
        'بنك', 'مصرف', 'جامعة', 'كلية', 'معهد', 'مدرسة', 'مستشفى',
        'وزارة', 'إدارة', 'مجلس', 'ديوان', 'محكمة',
        'نادي', 'فريق', 'حزب', 'جبهة', 'حركة',
    ];

    // Common organizations
    /** @var array<string> */
    public const array COMMON_ORGANIZATIONS = [
        'الأمم المتحدة', 'جامعة الدول العربية', 'منظمة التعاون الإسلامي',
        'مجلس التعاون الخليجي', 'الاتحاد الأوروبي', 'حلف الناتو',
        'البنك الدولي', 'صندوق النقد الدولي', 'منظمة الصحة العالمية',
        'اليونسكو', 'اليونيسف', 'الصليب الأحمر', 'الهلال الأحمر',
    ];

    // Date patterns
    /** @var array<string> */
    public const array DATE_MONTHS_HIJRI = [
        'محرم', 'صفر', 'ربيع الأول', 'ربيع الثاني', 'جمادى الأولى', 'جمادى الآخرة',
        'رجب', 'شعبان', 'رمضان', 'شوال', 'ذو القعدة', 'ذو الحجة',
    ];

    /** @var array<string> */
    public const array DATE_MONTHS_GREGORIAN = [
        'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
        'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر',
    ];

    /** @var array<string> */
    public const array DATE_INDICATORS = [
        'يوم', 'شهر', 'سنة', 'عام', 'تاريخ',
        'السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة',
        'أمس', 'اليوم', 'غداً', 'غدا',
    ];

    // Currency indicators
    /** @var array<string> */
    public const array CURRENCY_INDICATORS = [
        'ريال', 'درهم', 'دينار', 'جنيه', 'ليرة', 'دولار', 'يورو',
        'ر.س', 'د.إ', 'د.ك', 'ج.م', '$', '€', '£',
    ];

    // Time indicators
    /** @var array<string> */
    public const array TIME_INDICATORS = [
        'ساعة', 'دقيقة', 'ثانية', 'صباحاً', 'مساءً', 'ظهراً', 'ليلاً',
    ];
}
