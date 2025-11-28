<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\DialectNormalizer;

/**
 * DialectNormalizer Config - PHP 8.4
 *
 * Arabic dialect patterns and mappings.
 *
 * @package ArPHP\Core\Modules\DialectNormalizer
 */
final readonly class Config
{
    // Dialect codes
    public const string DIALECT_MSA = 'msa';        // Modern Standard Arabic
    public const string DIALECT_EGYPTIAN = 'egy';   // Egyptian
    public const string DIALECT_LEVANTINE = 'lev';  // Levantine (Syrian, Lebanese, Palestinian, Jordanian)
    public const string DIALECT_GULF = 'glf';       // Gulf (Saudi, Emirati, Kuwaiti, etc.)
    public const string DIALECT_MAGHREBI = 'mag';   // Maghrebi (Moroccan, Algerian, Tunisian)
    public const string DIALECT_IRAQI = 'irq';      // Iraqi

    /** @var array<string> */
    public const array SUPPORTED_DIALECTS = [
        self::DIALECT_MSA,
        self::DIALECT_EGYPTIAN,
        self::DIALECT_LEVANTINE,
        self::DIALECT_GULF,
        self::DIALECT_MAGHREBI,
        self::DIALECT_IRAQI,
    ];

    /** @var array<string, string> */
    public const array DIALECT_NAMES = [
        self::DIALECT_MSA => 'Modern Standard Arabic',
        self::DIALECT_EGYPTIAN => 'Egyptian Arabic',
        self::DIALECT_LEVANTINE => 'Levantine Arabic',
        self::DIALECT_GULF => 'Gulf Arabic',
        self::DIALECT_MAGHREBI => 'Maghrebi Arabic',
        self::DIALECT_IRAQI => 'Iraqi Arabic',
    ];

    // Egyptian dialect markers and mappings
    /** @var array<string, string> */
    public const array EGYPTIAN_TO_MSA = [
        'إيه' => 'ماذا',
        'ايه' => 'ماذا',
        'ازي' => 'كيف',
        'إزاي' => 'كيف',
        'ازاي' => 'كيف',
        'ده' => 'هذا',
        'دي' => 'هذه',
        'دول' => 'هؤلاء',
        'كده' => 'هكذا',
        'ليه' => 'لماذا',
        'فين' => 'أين',
        'امتى' => 'متى',
        'دلوقتي' => 'الآن',
        'بتاع' => 'خاص بـ',
        'بتاعك' => 'خاصتك',
        'بتاعي' => 'خاصتي',
        'عايز' => 'أريد',
        'عايزة' => 'أريد',
        'عاوز' => 'أريد',
        'مش' => 'ليس',
        'مفيش' => 'لا يوجد',
        'حاجة' => 'شيء',
        'برضو' => 'أيضاً',
        'بردو' => 'أيضاً',
        'كمان' => 'أيضاً',
        'هنا' => 'هنا',
        'هناك' => 'هناك',
        'بس' => 'فقط',
        'خلاص' => 'انتهى',
        'طب' => 'حسناً',
        'يعني' => 'أي',
        'أهو' => 'ها هو',
        'حلو' => 'جميل',
        'وحش' => 'سيء',
    ];

    /** @var array<string> Egyptian markers */
    public const array EGYPTIAN_MARKERS = [
        'إزاي', 'ازاي', 'إيه', 'ايه', 'ده', 'دي', 'دول', 'كده',
        'ليه', 'فين', 'امتى', 'دلوقتي', 'بتاع', 'عايز', 'عاوز',
        'مش', 'مفيش', 'برضو', 'بردو',
    ];

    // Levantine dialect markers and mappings
    /** @var array<string, string> */
    public const array LEVANTINE_TO_MSA = [
        'شو' => 'ماذا',
        'كيف' => 'كيف',
        'كيفك' => 'كيف حالك',
        'هلق' => 'الآن',
        'هلأ' => 'الآن',
        'وين' => 'أين',
        'ليش' => 'لماذا',
        'هيك' => 'هكذا',
        'هاد' => 'هذا',
        'هاي' => 'هذه',
        'هدول' => 'هؤلاء',
        'بدي' => 'أريد',
        'بدك' => 'تريد',
        'ما في' => 'لا يوجد',
        'مافي' => 'لا يوجد',
        'مشان' => 'من أجل',
        'منشان' => 'من أجل',
        'يلا' => 'هيا',
        'خلص' => 'انتهى',
        'كتير' => 'كثير',
        'شوي' => 'قليل',
        'هون' => 'هنا',
        'هونيك' => 'هناك',
    ];

    /** @var array<string> Levantine markers */
    public const array LEVANTINE_MARKERS = [
        'شو', 'كيفك', 'هلق', 'هلأ', 'وين', 'ليش', 'هيك',
        'هاد', 'هاي', 'هدول', 'بدي', 'بدك', 'مافي', 'منشان',
    ];

    // Gulf dialect markers and mappings
    /** @var array<string, string> */
    public const array GULF_TO_MSA = [
        'شلون' => 'كيف',
        'شلونك' => 'كيف حالك',
        'وش' => 'ماذا',
        'ايش' => 'ماذا',
        'وين' => 'أين',
        'ليش' => 'لماذا',
        'ابي' => 'أريد',
        'أبي' => 'أريد',
        'ابغى' => 'أريد',
        'أبغى' => 'أريد',
        'حيل' => 'كثير',
        'واجد' => 'كثير',
        'مرة' => 'جداً',
        'زين' => 'جيد',
        'مو' => 'ليس',
        'ما' => 'لا',
        'هني' => 'هنا',
        'هناك' => 'هناك',
        'الحين' => 'الآن',
        'توه' => 'الآن',
        'يالله' => 'هيا',
        'خلاص' => 'انتهى',
    ];

    /** @var array<string> Gulf markers */
    public const array GULF_MARKERS = [
        'شلون', 'شلونك', 'وش', 'ايش', 'ابي', 'أبي', 'ابغى', 'أبغى',
        'حيل', 'واجد', 'زين', 'مو', 'الحين', 'توه',
    ];

    // Maghrebi dialect markers and mappings
    /** @var array<string, string> */
    public const array MAGHREBI_TO_MSA = [
        'كيفاش' => 'كيف',
        'واش' => 'ماذا',
        'فين' => 'أين',
        'علاش' => 'لماذا',
        'هاد' => 'هذا',
        'هادي' => 'هذه',
        'هادو' => 'هؤلاء',
        'دابا' => 'الآن',
        'بغيت' => 'أريد',
        'بغا' => 'يريد',
        'ماشي' => 'ليس',
        'بزاف' => 'كثير',
        'شوية' => 'قليل',
        'هنا' => 'هنا',
        'تما' => 'هناك',
        'لاباس' => 'بخير',
        'صافي' => 'انتهى',
    ];

    /** @var array<string> Maghrebi markers */
    public const array MAGHREBI_MARKERS = [
        'كيفاش', 'واش', 'علاش', 'هاد', 'هادي', 'هادو',
        'دابا', 'بغيت', 'بغا', 'ماشي', 'بزاف', 'لاباس',
    ];

    // Iraqi dialect markers and mappings
    /** @var array<string, string> */
    public const array IRAQI_TO_MSA = [
        'شلون' => 'كيف',
        'شكو' => 'ماذا',
        'شنو' => 'ماذا',
        'وين' => 'أين',
        'ليش' => 'لماذا',
        'هاي' => 'هذه',
        'هذا' => 'هذا',
        'هذول' => 'هؤلاء',
        'هسه' => 'الآن',
        'أريد' => 'أريد',
        'ما' => 'لا',
        'مو' => 'ليس',
        'هواية' => 'كثير',
        'شوية' => 'قليل',
        'هنا' => 'هنا',
        'هناك' => 'هناك',
        'خوش' => 'جيد',
        'زين' => 'جيد',
    ];

    /** @var array<string> Iraqi markers */
    public const array IRAQI_MARKERS = [
        'شكو', 'شنو', 'هسه', 'هواية', 'خوش',
    ];

    // Common dialectal patterns
    /** @var array<string> */
    public const array COMMON_DIALECTAL_FEATURES = [
        // Question words
        'إيه', 'ايه', 'شو', 'وش', 'ايش', 'واش', 'شكو', 'شنو',
        // Negation
        'مش', 'مو', 'ماشي', 'مافي', 'مفيش',
        // Want verbs
        'عايز', 'عاوز', 'بدي', 'ابي', 'ابغى', 'بغيت',
        // Now
        'دلوقتي', 'هلق', 'هلأ', 'الحين', 'توه', 'دابا', 'هسه',
    ];
}
