<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\BuckwalterTransliteration;

/**
 * Buckwalter Transliteration Configuration - PHP 8.4
 *
 * @package ArPHP\Core\Modules\BuckwalterTransliteration
 */
final readonly class Config
{
    /**
     * Transliteration schemes
     */
    public const string SCHEME_BUCKWALTER = 'buckwalter';
    public const string SCHEME_SAFE_BUCKWALTER = 'safe_buckwalter';
    public const string SCHEME_ISO233 = 'iso233';
    public const string SCHEME_DIN31635 = 'din31635';
    public const string SCHEME_LOC = 'loc';
    public const string SCHEME_PHONETIC = 'phonetic';

    /**
     * Available schemes
     */
    public const array SCHEMES = [
        self::SCHEME_BUCKWALTER,
        self::SCHEME_SAFE_BUCKWALTER,
        self::SCHEME_ISO233,
        self::SCHEME_DIN31635,
        self::SCHEME_LOC,
        self::SCHEME_PHONETIC,
    ];

    /**
     * Buckwalter Arabic to Latin mapping
     */
    public const array BUCKWALTER_ARABIC_TO_LATIN = [
        'ء' => '\'',
        'آ' => '|',
        'أ' => '>',
        'ؤ' => '&',
        'إ' => '<',
        'ئ' => '}',
        'ا' => 'A',
        'ب' => 'b',
        'ة' => 'p',
        'ت' => 't',
        'ث' => 'v',
        'ج' => 'j',
        'ح' => 'H',
        'خ' => 'x',
        'د' => 'd',
        'ذ' => '*',
        'ر' => 'r',
        'ز' => 'z',
        'س' => 's',
        'ش' => '$',
        'ص' => 'S',
        'ض' => 'D',
        'ط' => 'T',
        'ظ' => 'Z',
        'ع' => 'E',
        'غ' => 'g',
        'ف' => 'f',
        'ق' => 'q',
        'ك' => 'k',
        'ل' => 'l',
        'م' => 'm',
        'ن' => 'n',
        'ه' => 'h',
        'و' => 'w',
        'ى' => 'Y',
        'ي' => 'y',
        'ً' => 'F',
        'ٌ' => 'N',
        'ٍ' => 'K',
        'َ' => 'a',
        'ُ' => 'u',
        'ِ' => 'i',
        'ّ' => '~',
        'ْ' => 'o',
        'ٰ' => '`',
        'ـ' => '_',
        '٠' => '0',
        '١' => '1',
        '٢' => '2',
        '٣' => '3',
        '٤' => '4',
        '٥' => '5',
        '٦' => '6',
        '٧' => '7',
        '٨' => '8',
        '٩' => '9',
    ];

    /**
     * Buckwalter Latin to Arabic mapping (reverse)
     */
    public const array BUCKWALTER_LATIN_TO_ARABIC = [
        '\'' => 'ء',
        '|' => 'آ',
        '>' => 'أ',
        '&' => 'ؤ',
        '<' => 'إ',
        '}' => 'ئ',
        'A' => 'ا',
        'b' => 'ب',
        'p' => 'ة',
        't' => 'ت',
        'v' => 'ث',
        'j' => 'ج',
        'H' => 'ح',
        'x' => 'خ',
        'd' => 'د',
        '*' => 'ذ',
        'r' => 'ر',
        'z' => 'ز',
        's' => 'س',
        '$' => 'ش',
        'S' => 'ص',
        'D' => 'ض',
        'T' => 'ط',
        'Z' => 'ظ',
        'E' => 'ع',
        'g' => 'غ',
        'f' => 'ف',
        'q' => 'ق',
        'k' => 'ك',
        'l' => 'ل',
        'm' => 'م',
        'n' => 'ن',
        'h' => 'ه',
        'w' => 'و',
        'Y' => 'ى',
        'y' => 'ي',
        'F' => 'ً',
        'N' => 'ٌ',
        'K' => 'ٍ',
        'a' => 'َ',
        'u' => 'ُ',
        'i' => 'ِ',
        '~' => 'ّ',
        'o' => 'ْ',
        '`' => 'ٰ',
        '_' => 'ـ',
    ];

    /**
     * Safe Buckwalter mapping (XML-safe characters)
     */
    public const array SAFE_BUCKWALTER_ARABIC_TO_LATIN = [
        'ء' => 'C',
        'آ' => 'M',
        'أ' => 'O',
        'ؤ' => 'W',
        'إ' => 'I',
        'ئ' => 'Q',
        'ا' => 'A',
        'ب' => 'b',
        'ة' => 'p',
        'ت' => 't',
        'ث' => 'v',
        'ج' => 'j',
        'ح' => 'H',
        'خ' => 'x',
        'د' => 'd',
        'ذ' => 'V',
        'ر' => 'r',
        'ز' => 'z',
        'س' => 's',
        'ش' => 'c',
        'ص' => 'S',
        'ض' => 'D',
        'ط' => 'T',
        'ظ' => 'Z',
        'ع' => 'E',
        'غ' => 'g',
        'ف' => 'f',
        'ق' => 'q',
        'ك' => 'k',
        'ل' => 'l',
        'م' => 'm',
        'ن' => 'n',
        'ه' => 'h',
        'و' => 'w',
        'ى' => 'Y',
        'ي' => 'y',
        'ً' => 'F',
        'ٌ' => 'N',
        'ٍ' => 'K',
        'َ' => 'a',
        'ُ' => 'u',
        'ِ' => 'i',
        'ّ' => 'B',
        'ْ' => 'o',
    ];

    /**
     * ISO 233 Arabic to Latin mapping
     */
    public const array ISO233_ARABIC_TO_LATIN = [
        'ء' => 'ʾ',
        'آ' => 'ʾā',
        'أ' => 'ʾ',
        'ؤ' => 'ʾ',
        'إ' => 'ʾ',
        'ئ' => 'ʾ',
        'ا' => 'ā',
        'ب' => 'b',
        'ة' => 'ẗ',
        'ت' => 't',
        'ث' => 'ṯ',
        'ج' => 'ǧ',
        'ح' => 'ḥ',
        'خ' => 'ḫ',
        'د' => 'd',
        'ذ' => 'ḏ',
        'ر' => 'r',
        'ز' => 'z',
        'س' => 's',
        'ش' => 'š',
        'ص' => 'ṣ',
        'ض' => 'ḍ',
        'ط' => 'ṭ',
        'ظ' => 'ẓ',
        'ع' => 'ʿ',
        'غ' => 'ġ',
        'ف' => 'f',
        'ق' => 'q',
        'ك' => 'k',
        'ل' => 'l',
        'م' => 'm',
        'ن' => 'n',
        'ه' => 'h',
        'و' => 'w',
        'ى' => 'ỳ',
        'ي' => 'y',
    ];

    /**
     * Simple phonetic mapping
     */
    public const array PHONETIC_ARABIC_TO_LATIN = [
        'ء' => "'",
        'آ' => 'aa',
        'أ' => 'a',
        'ؤ' => 'o',
        'إ' => 'e',
        'ئ' => 'e',
        'ا' => 'a',
        'ب' => 'b',
        'ة' => 'h',
        'ت' => 't',
        'ث' => 'th',
        'ج' => 'j',
        'ح' => 'h',
        'خ' => 'kh',
        'د' => 'd',
        'ذ' => 'dh',
        'ر' => 'r',
        'ز' => 'z',
        'س' => 's',
        'ش' => 'sh',
        'ص' => 's',
        'ض' => 'd',
        'ط' => 't',
        'ظ' => 'z',
        'ع' => "'",
        'غ' => 'gh',
        'ف' => 'f',
        'ق' => 'q',
        'ك' => 'k',
        'ل' => 'l',
        'م' => 'm',
        'ن' => 'n',
        'ه' => 'h',
        'و' => 'w',
        'ى' => 'a',
        'ي' => 'y',
        'ً' => 'an',
        'ٌ' => 'un',
        'ٍ' => 'in',
        'َ' => 'a',
        'ُ' => 'u',
        'ِ' => 'i',
        'ّ' => '',
        'ْ' => '',
    ];
}
