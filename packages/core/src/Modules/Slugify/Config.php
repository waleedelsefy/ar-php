<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Slugify;

/**
 * Slugify Config - PHP 8.4
 *
 * Arabic to Latin transliteration mappings.
 *
 * @package ArPHP\Core\Modules\Slugify
 */
final readonly class Config
{
    // Default separator
    public const string DEFAULT_SEPARATOR = '-';

    // Maximum slug length
    public const int MAX_SLUG_LENGTH = 100;

    // Arabic to Latin transliteration map
    /** @var array<string, string> */
    public const array TRANSLITERATION_MAP = [
        // Hamza variants
        'ء' => '',
        'أ' => 'a',
        'إ' => 'i',
        'آ' => 'aa',
        'ٱ' => 'a',
        'ؤ' => 'o',
        'ئ' => 'e',

        // Main letters
        'ا' => 'a',
        'ب' => 'b',
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
        'ع' => 'a',
        'غ' => 'gh',
        'ف' => 'f',
        'ق' => 'q',
        'ك' => 'k',
        'ل' => 'l',
        'م' => 'm',
        'ن' => 'n',
        'ه' => 'h',
        'و' => 'w',
        'ي' => 'y',

        // Special characters
        'ة' => 'a',     // ta marbuta
        'ى' => 'a',     // alef maqsura
        'لا' => 'la',   // lam-alef

        // Persian/Urdu additions
        'پ' => 'p',
        'چ' => 'ch',
        'ژ' => 'zh',
        'گ' => 'g',
    ];

    // Reverse transliteration map (Latin to Arabic - approximate)
    /** @var array<string, string> */
    public const array REVERSE_TRANSLITERATION_MAP = [
        'aa' => 'آ',
        'th' => 'ث',
        'kh' => 'خ',
        'dh' => 'ذ',
        'sh' => 'ش',
        'gh' => 'غ',
        'ch' => 'چ',
        'zh' => 'ژ',
        'la' => 'لا',
        'a' => 'ا',
        'b' => 'ب',
        't' => 'ت',
        'j' => 'ج',
        'h' => 'ح',
        'd' => 'د',
        'r' => 'ر',
        'z' => 'ز',
        's' => 'س',
        'f' => 'ف',
        'q' => 'ق',
        'k' => 'ك',
        'l' => 'ل',
        'm' => 'م',
        'n' => 'ن',
        'w' => 'و',
        'y' => 'ي',
        'i' => 'ي',
        'o' => 'و',
        'e' => 'ي',
        'u' => 'و',
        'p' => 'پ',
        'g' => 'گ',
    ];

    // Arabic diacritics to remove
    /** @var array<string> */
    public const array DIACRITICS = [
        'ً', 'ٌ', 'ٍ', 'َ', 'ُ', 'ِ', 'ّ', 'ْ', 'ـ',
    ];

    // Characters to remove from slug
    /** @var array<string> */
    public const array REMOVE_CHARACTERS = [
        '،', '؛', '؟', '!', '.', ',', ':', ';', '"', "'",
        '(', ')', '[', ']', '{', '}', '<', '>', '«', '»',
        '/', '\\', '|', '@', '#', '$', '%', '^', '&', '*',
        '+', '=', '~', '`',
    ];

    // Arabic numbers to Western
    /** @var array<string, string> */
    public const array ARABIC_NUMBERS = [
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

    // Valid separator characters
    /** @var array<string> */
    public const array VALID_SEPARATORS = ['-', '_', '.'];
}
