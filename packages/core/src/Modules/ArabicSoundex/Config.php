<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicSoundex;

/**
 * Configuration for Arabic Soundex Module - PHP 8.4
 *
 * Arabic phonetic mapping based on articulation points (مخارج الحروف)
 *
 * @package ArPHP\Core\Modules\ArabicSoundex
 */
final readonly class Config
{
    /**
     * Arabic Soundex mapping based on phonetic groups
     * Characters in the same group produce similar sounds
     *
     * @var array<string, string>
     */
    public const array SOUNDEX_MAP = [
        // Hamza variants -> A
        'ا' => 'A', 'أ' => 'A', 'إ' => 'A', 'آ' => 'A', 'ء' => 'A', 'ى' => 'A',
        
        // Labial sounds (شفوية) -> B
        'ب' => 'B', 'ف' => 'B', 'و' => 'B',
        
        // Dental sounds (أسنانية) -> C
        'ت' => 'C', 'ث' => 'C', 'ذ' => 'C', 'ظ' => 'C',
        
        // Alveolar sounds (لثوية) -> D
        'د' => 'D', 'ض' => 'D',
        
        // Velar sounds (طبقية) -> E
        'ج' => 'E', 'ش' => 'E', 'ي' => 'E',
        
        // Pharyngeal sounds (حلقية) -> F
        'ح' => 'F', 'خ' => 'F', 'ه' => 'F', 'ع' => 'F', 'غ' => 'F',
        
        // Emphatic sounds (مفخمة) -> G
        'ص' => 'G', 'ط' => 'G',
        
        // Liquid sounds (سائلة) -> H
        'ر' => 'H', 'ل' => 'H',
        
        // Sibilant sounds (صفيرية) -> I
        'س' => 'I', 'ز' => 'I',
        
        // Nasal sounds (أنفية) -> J
        'م' => 'J', 'ن' => 'J',
        
        // Uvular sounds (لهوية) -> K
        'ق' => 'K', 'ك' => 'K',
        
        // Taa Marbuta -> A (like Alif)
        'ة' => 'A',
    ];

    /**
     * Extended Soundex for more precise matching
     * Uses numbers for finer phonetic distinction
     *
     * @var array<string, string>
     */
    public const array SOUNDEX_EXTENDED = [
        'ا' => '0', 'أ' => '0', 'إ' => '0', 'آ' => '0', 'ء' => '0', 'ى' => '0', 'ة' => '0',
        'ب' => '1',
        'ف' => '2',
        'و' => '3',
        'ت' => '4', 'ط' => '4',
        'ث' => '5',
        'ج' => '6',
        'ح' => '7',
        'خ' => '8',
        'د' => '9', 'ض' => '9',
        'ذ' => 'A',
        'ر' => 'B',
        'ز' => 'C',
        'س' => 'D',
        'ش' => 'E',
        'ص' => 'F',
        'ظ' => 'G',
        'ع' => 'H',
        'غ' => 'I',
        'ق' => 'J',
        'ك' => 'K',
        'ل' => 'L',
        'م' => 'M',
        'ن' => 'N',
        'ه' => 'O',
        'ي' => 'P',
    ];

    /**
     * Metaphone mapping for Arabic
     * Based on pronunciation patterns
     *
     * @var array<string, string>
     */
    public const array METAPHONE_MAP = [
        'ا' => 'A', 'أ' => 'A', 'إ' => 'I', 'آ' => 'AA', 'ء' => '',
        'ب' => 'B',
        'ت' => 'T',
        'ث' => 'TH',
        'ج' => 'J',
        'ح' => 'H',
        'خ' => 'KH',
        'د' => 'D',
        'ذ' => 'TH',
        'ر' => 'R',
        'ز' => 'Z',
        'س' => 'S',
        'ش' => 'SH',
        'ص' => 'S',
        'ض' => 'D',
        'ط' => 'T',
        'ظ' => 'TH',
        'ع' => 'A',
        'غ' => 'GH',
        'ف' => 'F',
        'ق' => 'Q',
        'ك' => 'K',
        'ل' => 'L',
        'م' => 'M',
        'ن' => 'N',
        'ه' => 'H',
        'و' => 'W',
        'ي' => 'Y',
        'ى' => 'A',
        'ة' => 'H',
    ];

    /**
     * Romanization mapping for Arabic (Pronunciation)
     * Based on common Arabic romanization standards (ALA-LC / BGN/PCGN)
     *
     * @var array<string, string>
     */
    public const array ROMANIZATION_MAP = [
        'ا' => 'a', 'أ' => 'a', 'إ' => 'i', 'آ' => 'ā', 'ء' => '\'',
        'ب' => 'b',
        'ت' => 't',
        'ث' => 'th',
        'ج' => 'j',
        'ح' => 'ḥ',
        'خ' => 'kh',
        'د' => 'd',
        'ذ' => 'dh',
        'ر' => 'r',
        'ز' => 'z',
        'س' => 's',
        'ش' => 'sh',
        'ص' => 'ṣ',
        'ض' => 'ḍ',
        'ط' => 'ṭ',
        'ظ' => 'ẓ',
        'ع' => 'ʿ',
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
        'ى' => 'ā',
        'ة' => 'a',
    ];

    /**
     * Simple Romanization (ASCII only, more readable)
     *
     * @var array<string, string>
     */
    public const array ROMANIZATION_SIMPLE = [
        'ا' => 'a', 'أ' => 'a', 'إ' => 'i', 'آ' => 'aa', 'ء' => '',
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
        'ع' => 'a',  // عين تُكتب كـ a في بداية الكلمة
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
        'ى' => 'a',
        'ة' => 'a',
    ];

    /**
     * Vowel patterns for smart romanization
     * Short vowels (harakat) are usually not written in Arabic
     *
     * @var array<string, string>
     */
    public const array VOWEL_PATTERNS = [
        "\u{064E}" => 'a',  // Fatha
        "\u{064F}" => 'u',  // Damma
        "\u{0650}" => 'i',  // Kasra
        "\u{064B}" => 'an', // Fathatan (tanween)
        "\u{064C}" => 'un', // Dammatan
        "\u{064D}" => 'in', // Kasratan
    ];

    /**
     * Similar sound groups for fuzzy matching
     *
     * @var array<int, array<string>>
     */
    public const array SIMILAR_GROUPS = [
        // Hamza variants
        ['ا', 'أ', 'إ', 'آ', 'ء', 'ع'],
        // T sounds
        ['ت', 'ط'],
        // TH sounds
        ['ث', 'ذ', 'ظ'],
        // D sounds
        ['د', 'ض'],
        // H sounds
        ['ه', 'ح', 'ة'],
        // KH/GH sounds
        ['خ', 'غ'],
        // S sounds
        ['س', 'ص'],
        // K/Q sounds
        ['ك', 'ق'],
        // Y sounds
        ['ي', 'ى'],
    ];

    /**
     * Characters to remove (diacritics/tashkeel)
     *
     * @var array<string>
     */
    public const array DIACRITICS = [
        "\u{064B}", // Fathatan
        "\u{064C}", // Dammatan
        "\u{064D}", // Kasratan
        "\u{064E}", // Fatha
        "\u{064F}", // Damma
        "\u{0650}", // Kasra
        "\u{0651}", // Shadda
        "\u{0652}", // Sukun
        "\u{0653}", // Maddah
        "\u{0654}", // Hamza above
        "\u{0655}", // Hamza below
        "\u{0670}", // Superscript Alef
    ];

    public const int DEFAULT_CODE_LENGTH = 4;
    public const int DEFAULT_SIMILARITY_THRESHOLD = 70;
}
