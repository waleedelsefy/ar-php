<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Tashkeel;

/**
 * Tashkeel Config - PHP 8.4
 *
 * Arabic diacritics (harakat) definitions.
 *
 * @package ArPHP\Core\Modules\Tashkeel
 */
final readonly class Config
{
    // Individual diacritics
    public const string FATHA = "\u{064E}";         // فتحة
    public const string DAMMA = "\u{064F}";         // ضمة
    public const string KASRA = "\u{0650}";         // كسرة
    public const string SUKOON = "\u{0652}";        // سكون
    public const string SHADDA = "\u{0651}";        // شدة
    public const string FATHATAN = "\u{064B}";      // فتحتان (تنوين فتح)
    public const string DAMMATAN = "\u{064C}";      // ضمتان (تنوين ضم)
    public const string KASRATAN = "\u{064D}";      // كسرتان (تنوين كسر)
    public const string TATWEEL = "\u{0640}";       // تطويل

    // Superscript/subscript
    public const string SUPERSCRIPT_ALEF = "\u{0670}";  // ألف خنجرية
    public const string SUBSCRIPT_ALEF = "\u{0656}";    // ألف تحتية

    // All short vowels (harakat)
    /** @var array<string> */
    public const array SHORT_VOWELS = [
        self::FATHA,
        self::DAMMA,
        self::KASRA,
    ];

    // All tanween
    /** @var array<string> */
    public const array TANWEEN = [
        self::FATHATAN,
        self::DAMMATAN,
        self::KASRATAN,
    ];

    // All diacritics
    /** @var array<string> */
    public const array ALL_DIACRITICS = [
        self::FATHA,
        self::DAMMA,
        self::KASRA,
        self::SUKOON,
        self::SHADDA,
        self::FATHATAN,
        self::DAMMATAN,
        self::KASRATAN,
        self::TATWEEL,
        self::SUPERSCRIPT_ALEF,
        self::SUBSCRIPT_ALEF,
    ];

    // Diacritic names
    /** @var array<string, string> */
    public const array DIACRITIC_NAMES = [
        self::FATHA => 'fatha',
        self::DAMMA => 'damma',
        self::KASRA => 'kasra',
        self::SUKOON => 'sukoon',
        self::SHADDA => 'shadda',
        self::FATHATAN => 'fathatan',
        self::DAMMATAN => 'dammatan',
        self::KASRATAN => 'kasratan',
        self::TATWEEL => 'tatweel',
        self::SUPERSCRIPT_ALEF => 'superscript_alef',
        self::SUBSCRIPT_ALEF => 'subscript_alef',
    ];

    // Arabic names
    /** @var array<string, string> */
    public const array DIACRITIC_NAMES_AR = [
        self::FATHA => 'فَتحة',
        self::DAMMA => 'ضَمّة',
        self::KASRA => 'كَسرة',
        self::SUKOON => 'سُكون',
        self::SHADDA => 'شَدّة',
        self::FATHATAN => 'فَتحتان',
        self::DAMMATAN => 'ضَمّتان',
        self::KASRATAN => 'كَسرتان',
        self::TATWEEL => 'تطويل',
        self::SUPERSCRIPT_ALEF => 'ألف خنجرية',
        self::SUBSCRIPT_ALEF => 'ألف تحتية',
    ];

    // Arabic letters (base consonants)
    /** @var array<string> */
    public const array ARABIC_LETTERS = [
        'ء', 'آ', 'أ', 'ؤ', 'إ', 'ئ', 'ا', 'ب', 'ة', 'ت', 'ث', 'ج', 'ح', 'خ',
        'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ',
        'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ى', 'ي', 'ٱ',
    ];

    // Sun letters (for definite article assimilation)
    /** @var array<string> */
    public const array SUN_LETTERS = [
        'ت', 'ث', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ل', 'ن',
    ];

    // Moon letters
    /** @var array<string> */
    public const array MOON_LETTERS = [
        'ء', 'ب', 'ج', 'ح', 'خ', 'ع', 'غ', 'ف', 'ق', 'ك', 'م', 'ه', 'و', 'ي',
    ];
}
