<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\AdvancedKeyboardFix;

/**
 * Advanced Keyboard Fix Configuration - PHP 8.4
 *
 * @package ArPHP\Core\Modules\AdvancedKeyboardFix
 */
final readonly class Config
{
    /**
     * English to Arabic keyboard mapping (QWERTY to Arabic)
     */
    public const array ENGLISH_TO_ARABIC = [
        'q' => 'ض',
        'w' => 'ص',
        'e' => 'ث',
        'r' => 'ق',
        't' => 'ف',
        'y' => 'غ',
        'u' => 'ع',
        'i' => 'ه',
        'o' => 'خ',
        'p' => 'ح',
        '[' => 'ج',
        ']' => 'د',
        'a' => 'ش',
        's' => 'س',
        'd' => 'ي',
        'f' => 'ب',
        'g' => 'ل',
        'h' => 'ا',
        'j' => 'ت',
        'k' => 'ن',
        'l' => 'م',
        ';' => 'ك',
        '\'' => 'ط',
        'z' => 'ئ',
        'x' => 'ء',
        'c' => 'ؤ',
        'v' => 'ر',
        'b' => 'لا',
        'n' => 'ى',
        'm' => 'ة',
        ',' => 'و',
        '.' => 'ز',
        '/' => 'ظ',
        '`' => 'ذ',
        'Q' => 'َ',
        'W' => 'ً',
        'E' => 'ُ',
        'R' => 'ٌ',
        'T' => 'لإ',
        'Y' => 'إ',
        'U' => '\'',
        'I' => '÷',
        'O' => '×',
        'P' => '؛',
        '{' => '<',
        '}' => '>',
        'A' => 'ِ',
        'S' => 'ٍ',
        'D' => ']',
        'F' => '[',
        'G' => 'لأ',
        'H' => 'أ',
        'J' => 'ـ',
        'K' => '،',
        'L' => '/',
        ':' => ':',
        '"' => '"',
        'Z' => '~',
        'X' => 'ْ',
        'C' => '}',
        'V' => '{',
        'B' => 'لآ',
        'N' => 'آ',
        'M' => '\'',
        '<' => ',',
        '>' => '.',
        '?' => '؟',
        '~' => 'ّ',
    ];

    /**
     * Arabic to English keyboard mapping
     */
    public const array ARABIC_TO_ENGLISH = [
        'ض' => 'q',
        'ص' => 'w',
        'ث' => 'e',
        'ق' => 'r',
        'ف' => 't',
        'غ' => 'y',
        'ع' => 'u',
        'ه' => 'i',
        'خ' => 'o',
        'ح' => 'p',
        'ج' => '[',
        'د' => ']',
        'ش' => 'a',
        'س' => 's',
        'ي' => 'd',
        'ب' => 'f',
        'ل' => 'g',
        'ا' => 'h',
        'ت' => 'j',
        'ن' => 'k',
        'م' => 'l',
        'ك' => ';',
        'ط' => '\'',
        'ئ' => 'z',
        'ء' => 'x',
        'ؤ' => 'c',
        'ر' => 'v',
        'لا' => 'b',
        'ى' => 'n',
        'ة' => 'm',
        'و' => ',',
        'ز' => '.',
        'ظ' => '/',
        'ذ' => '`',
        '،' => 'K',
        '؟' => '?',
        '؛' => 'P',
        'أ' => 'H',
        'إ' => 'Y',
        'آ' => 'N',
    ];

    /**
     * Franco-Arabic (Arabizi) to Arabic mapping
     */
    public const array FRANCO_TO_ARABIC = [
        '2' => 'ء',
        '3' => 'ع',
        '5' => 'خ',
        '6' => 'ط',
        '7' => 'ح',
        '8' => 'ق',
        '9' => 'ص',
        "3'" => 'غ',
        "7'" => 'خ',
        "9'" => 'ض',
        'a' => 'ا',
        'b' => 'ب',
        't' => 'ت',
        'th' => 'ث',
        'j' => 'ج',
        'g' => 'ج',
        'h' => 'ه',
        'kh' => 'خ',
        'd' => 'د',
        'dh' => 'ذ',
        'r' => 'ر',
        'z' => 'ز',
        's' => 'س',
        'sh' => 'ش',
        'ch' => 'ش',
        'f' => 'ف',
        'q' => 'ق',
        'k' => 'ك',
        'l' => 'ل',
        'm' => 'م',
        'n' => 'ن',
        'w' => 'و',
        'y' => 'ي',
        'i' => 'ي',
        'e' => 'ي',
        'o' => 'و',
        'u' => 'و',
        'aa' => 'ا',
        'ee' => 'ي',
        'oo' => 'و',
        'ii' => 'ي',
        'uu' => 'و',
    ];

    /**
     * Common Arabic typing mistakes
     */
    public const array TYPING_MISTAKES = [
        'ه ال' => 'هال',
        'ال ه' => 'اله',
        ' ال ' => ' ال',
        'ة ال' => 'ة ال',
        '  ' => ' ',
        'لل' => 'لل',
    ];

    /**
     * Layout types
     */
    public const string LAYOUT_ARABIC = 'arabic';
    public const string LAYOUT_ENGLISH = 'english';
    public const string LAYOUT_MIXED = 'mixed';
    public const string LAYOUT_UNKNOWN = 'unknown';

    /**
     * Detection thresholds
     */
    public const float ARABIC_THRESHOLD = 0.6;
    public const float ENGLISH_THRESHOLD = 0.6;
}
