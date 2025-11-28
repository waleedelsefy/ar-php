<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\EncodingTools;

/**
 * Encoding Tools Configuration - PHP 8.4
 *
 * @package ArPHP\Core\Modules\EncodingTools
 */
final readonly class Config
{
    /**
     * Default encoding
     */
    public const string DEFAULT_ENCODING = 'UTF-8';

    /**
     * Common Arabic encodings
     */
    public const string WINDOWS_1256 = 'Windows-1256';
    public const string ISO_8859_6 = 'ISO-8859-6';
    public const string UTF_8 = 'UTF-8';
    public const string UTF_16 = 'UTF-16';
    public const string UTF_16BE = 'UTF-16BE';
    public const string UTF_16LE = 'UTF-16LE';
    public const string UTF_32 = 'UTF-32';

    /**
     * Supported encodings for Arabic text
     */
    public const array SUPPORTED_ENCODINGS = [
        'UTF-8',
        'UTF-16',
        'UTF-16BE',
        'UTF-16LE',
        'UTF-32',
        'Windows-1256',
        'ISO-8859-6',
        'CP1256',
        'CP864',
        'MacArabic',
    ];

    /**
     * Encoding aliases
     */
    public const array ENCODING_ALIASES = [
        'cp1256' => 'Windows-1256',
        'arabic' => 'ISO-8859-6',
        'iso-8859-6' => 'ISO-8859-6',
        'iso88596' => 'ISO-8859-6',
        'windows1256' => 'Windows-1256',
        'win1256' => 'Windows-1256',
        'utf8' => 'UTF-8',
        'utf-8' => 'UTF-8',
        'utf16' => 'UTF-16',
        'utf-16' => 'UTF-16',
    ];

    /**
     * Arabic Unicode ranges
     */
    public const array ARABIC_UNICODE_RANGES = [
        'arabic' => [0x0600, 0x06FF],
        'arabic_supplement' => [0x0750, 0x077F],
        'arabic_extended_a' => [0x08A0, 0x08FF],
        'arabic_presentation_forms_a' => [0xFB50, 0xFDFF],
        'arabic_presentation_forms_b' => [0xFE70, 0xFEFF],
    ];

    /**
     * BOM (Byte Order Mark) signatures
     */
    public const array BOM_SIGNATURES = [
        'UTF-8' => "\xEF\xBB\xBF",
        'UTF-16BE' => "\xFE\xFF",
        'UTF-16LE' => "\xFF\xFE",
        'UTF-32BE' => "\x00\x00\xFE\xFF",
        'UTF-32LE' => "\xFF\xFE\x00\x00",
    ];

    /**
     * Windows-1256 to Unicode mapping (high bytes 128-255)
     */
    public const array WINDOWS_1256_MAP = [
        128 => 0x20AC, // €
        129 => 0x067E, // پ
        130 => 0x201A, // ‚
        131 => 0x0192, // ƒ
        132 => 0x201E, // „
        133 => 0x2026, // …
        134 => 0x2020, // †
        135 => 0x2021, // ‡
        136 => 0x02C6, // ˆ
        137 => 0x2030, // ‰
        138 => 0x0679, // ٹ
        139 => 0x2039, // ‹
        140 => 0x0152, // Œ
        141 => 0x0686, // چ
        142 => 0x0698, // ژ
        143 => 0x0688, // ڈ
        144 => 0x06AF, // گ
        145 => 0x2018, // '
        146 => 0x2019, // '
        147 => 0x201C, // "
        148 => 0x201D, // "
        149 => 0x2022, // •
        150 => 0x2013, // –
        151 => 0x2014, // —
        152 => 0x06A9, // ک
        153 => 0x2122, // ™
        154 => 0x0691, // ڑ
        155 => 0x203A, // ›
        156 => 0x0153, // œ
        157 => 0x200C, // ZWNJ
        158 => 0x200D, // ZWJ
        159 => 0x06BA, // ں
        160 => 0x00A0, // NBSP
        161 => 0x060C, // ،
        162 => 0x00A2, // ¢
        163 => 0x00A3, // £
        164 => 0x00A4, // ¤
        165 => 0x00A5, // ¥
        166 => 0x00A6, // ¦
        167 => 0x00A7, // §
        168 => 0x00A8, // ¨
        169 => 0x00A9, // ©
        170 => 0x06BE, // ھ
        171 => 0x00AB, // «
        172 => 0x00AC, // ¬
        173 => 0x00AD, // SHY
        174 => 0x00AE, // ®
        175 => 0x00AF, // ¯
        176 => 0x00B0, // °
        177 => 0x00B1, // ±
        178 => 0x00B2, // ²
        179 => 0x00B3, // ³
        180 => 0x00B4, // ´
        181 => 0x00B5, // µ
        182 => 0x00B6, // ¶
        183 => 0x00B7, // ·
        184 => 0x00B8, // ¸
        185 => 0x00B9, // ¹
        186 => 0x061B, // ؛
        187 => 0x00BB, // »
        188 => 0x00BC, // ¼
        189 => 0x00BD, // ½
        190 => 0x00BE, // ¾
        191 => 0x061F, // ؟
        192 => 0x06C1, // ہ
        193 => 0x0621, // ء
        194 => 0x0622, // آ
        195 => 0x0623, // أ
        196 => 0x0624, // ؤ
        197 => 0x0625, // إ
        198 => 0x0626, // ئ
        199 => 0x0627, // ا
        200 => 0x0628, // ب
        201 => 0x0629, // ة
        202 => 0x062A, // ت
        203 => 0x062B, // ث
        204 => 0x062C, // ج
        205 => 0x062D, // ح
        206 => 0x062E, // خ
        207 => 0x062F, // د
        208 => 0x0630, // ذ
        209 => 0x0631, // ر
        210 => 0x0632, // ز
        211 => 0x0633, // س
        212 => 0x0634, // ش
        213 => 0x0635, // ص
        214 => 0x0636, // ض
        215 => 0x00D7, // ×
        216 => 0x0637, // ط
        217 => 0x0638, // ظ
        218 => 0x0639, // ع
        219 => 0x063A, // غ
        220 => 0x0640, // ـ
        221 => 0x0641, // ف
        222 => 0x0642, // ق
        223 => 0x0643, // ك
        224 => 0x00E0, // à
        225 => 0x0644, // ل
        226 => 0x00E2, // â
        227 => 0x0645, // م
        228 => 0x0646, // ن
        229 => 0x0647, // ه
        230 => 0x0648, // و
        231 => 0x00E7, // ç
        232 => 0x00E8, // è
        233 => 0x00E9, // é
        234 => 0x00EA, // ê
        235 => 0x00EB, // ë
        236 => 0x0649, // ى
        237 => 0x064A, // ي
        238 => 0x00EE, // î
        239 => 0x00EF, // ï
        240 => 0x064B, // ً
        241 => 0x064C, // ٌ
        242 => 0x064D, // ٍ
        243 => 0x064E, // َ
        244 => 0x00F4, // ô
        245 => 0x064F, // ُ
        246 => 0x0650, // ِ
        247 => 0x00F7, // ÷
        248 => 0x0651, // ّ
        249 => 0x00F9, // ù
        250 => 0x0652, // ْ
        251 => 0x00FB, // û
        252 => 0x00FC, // ü
        253 => 0x200E, // LRM
        254 => 0x200F, // RLM
        255 => 0x06D2, // ے
    ];
}
