<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Normalization;

/**
 * Normalization Configuration - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Normalization
 */
final readonly class Config
{
    /**
     * Arabic diacritics (tashkeel)
     */
    public const array DIACRITICS = [
        "\u{064B}", // Fathatan ً
        "\u{064C}", // Dammatan ٌ
        "\u{064D}", // Kasratan ٍ
        "\u{064E}", // Fatha َ
        "\u{064F}", // Damma ُ
        "\u{0650}", // Kasra ِ
        "\u{0651}", // Shadda ّ
        "\u{0652}", // Sukun ْ
        "\u{0653}", // Maddah Above ٓ
        "\u{0654}", // Hamza Above ٔ
        "\u{0655}", // Hamza Below ٕ
        "\u{0656}", // Subscript Alef ٖ
        "\u{0657}", // Inverted Damma ٗ
        "\u{0658}", // Mark Noon Ghunna ٘
        "\u{0659}", // Zwarakay ٙ
        "\u{065A}", // Vowel Sign Small V Above ٚ
        "\u{065B}", // Vowel Sign Inverted Small V Above ٛ
        "\u{065C}", // Vowel Sign Dot Below ٜ
        "\u{065D}", // Reversed Damma ٝ
        "\u{065E}", // Fatha with Two Dots ٞ
        "\u{065F}", // Wavy Hamza Below ٟ
        "\u{0670}", // Superscript Alef ٰ
    ];

    /**
     * Alef variants
     */
    public const array ALEF_VARIANTS = [
        "\u{0622}", // آ Alef with Madda
        "\u{0623}", // أ Alef with Hamza Above
        "\u{0625}", // إ Alef with Hamza Below
        "\u{0627}", // ا Alef
        "\u{0671}", // ٱ Alef Wasla
    ];

    /**
     * Target Alef for normalization
     */
    public const string ALEF_NORMAL = "\u{0627}"; // ا

    /**
     * Ta Marbuta
     */
    public const string TA_MARBUTA = "\u{0629}"; // ة

    /**
     * Ha (target for Ta Marbuta normalization)
     */
    public const string HA = "\u{0647}"; // ه

    /**
     * Alef Maqsura
     */
    public const string ALEF_MAQSURA = "\u{0649}"; // ى

    /**
     * Yaa (target for Alef Maqsura normalization)
     */
    public const string YAA = "\u{064A}"; // ي

    /**
     * Waw variants
     */
    public const array WAW_VARIANTS = [
        "\u{0624}", // ؤ Waw with Hamza
        "\u{0648}", // و Waw
    ];

    /**
     * Target Waw for normalization
     */
    public const string WAW_NORMAL = "\u{0648}"; // و

    /**
     * Yaa variants
     */
    public const array YAA_VARIANTS = [
        "\u{0626}", // ئ Yaa with Hamza
        "\u{064A}", // ي Yaa
        "\u{06CC}", // ی Farsi Yaa
    ];

    /**
     * Tatweel/Kashida
     */
    public const string TATWEEL = "\u{0640}"; // ـ

    /**
     * Arabic-Indic digits
     */
    public const array ARABIC_INDIC_DIGITS = [
        '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩',
    ];

    /**
     * Western digits
     */
    public const array WESTERN_DIGITS = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
    ];

    /**
     * Extended Arabic-Indic digits (Persian/Urdu)
     */
    public const array EXTENDED_ARABIC_DIGITS = [
        '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹',
    ];

    /**
     * Normalization options
     */
    public const string OPTION_DIACRITICS = 'diacritics';
    public const string OPTION_ALEF = 'alef';
    public const string OPTION_TA_MARBUTA = 'ta_marbuta';
    public const string OPTION_ALEF_MAQSURA = 'alef_maqsura';
    public const string OPTION_WAW = 'waw';
    public const string OPTION_YAA = 'yaa';
    public const string OPTION_TATWEEL = 'tatweel';
    public const string OPTION_WHITESPACE = 'whitespace';
    public const string OPTION_NUMBERS = 'numbers';

    /**
     * Number styles
     */
    public const string STYLE_ARABIC = 'arabic';
    public const string STYLE_WESTERN = 'western';

    /**
     * Arabic character range pattern
     */
    public const string ARABIC_RANGE = '\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}';
}
