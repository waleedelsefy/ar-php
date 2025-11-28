<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Lemmatizer;

/**
 * Lemmatizer Config - PHP 8.4
 *
 * Arabic morphological patterns and affixes.
 *
 * @package ArPHP\Core\Modules\Lemmatizer
 */
final readonly class Config
{
    // Common prefixes (سوابق)
    /** @var array<string> */
    public const array PREFIXES = [
        'ال',    // definite article
        'و',     // and
        'ف',     // so
        'ب',     // with/by
        'ك',     // like
        'ل',     // for/to
        'لل',    // for the
        'وال',   // and the
        'فال',   // so the
        'بال',   // with the
        'كال',   // like the
        'س',     // will (future)
        'أ',     // question prefix
        'ي',     // verb prefix (he/it)
        'ت',     // verb prefix (she/you)
        'ن',     // verb prefix (we)
    ];

    // Common suffixes (لواحق)
    /** @var array<string> */
    public const array SUFFIXES = [
        'ة',     // ta marbuta
        'ه',     // ha (pronoun)
        'ها',    // her
        'هم',    // them (m)
        'هن',    // them (f)
        'هما',   // them (dual)
        'ك',     // you (s)
        'كم',    // you (pl m)
        'كن',    // you (pl f)
        'كما',   // you (dual)
        'نا',    // us
        'ني',    // me
        'ي',     // my
        'ون',    // plural masculine
        'ين',    // plural masculine (genitive)
        'ان',    // dual nominative
        'ين',    // dual genitive
        'ات',    // plural feminine
        'وا',    // verb suffix (they)
        'تم',    // verb suffix (you pl)
        'تن',    // verb suffix (you f pl)
        'تما',   // verb suffix (you dual)
    ];

    // Verb patterns (أوزان الأفعال)
    /** @var array<string, string> */
    public const array VERB_PATTERNS = [
        'فَعَلَ' => 'فعل',     // I - basic
        'فَعَّلَ' => 'فعل',    // II - intensive
        'فَاعَلَ' => 'فعل',    // III - reciprocal
        'أَفْعَلَ' => 'فعل',   // IV - causative
        'تَفَعَّلَ' => 'فعل',  // V - reflexive of II
        'تَفَاعَلَ' => 'فعل',  // VI - reflexive of III
        'انْفَعَلَ' => 'فعل',  // VII - passive
        'افْتَعَلَ' => 'فعل',  // VIII - reflexive
        'افْعَلَّ' => 'فعل',   // IX - colors/defects
        'اسْتَفْعَلَ' => 'فعل', // X - requestive
    ];

    // Noun patterns (أوزان الأسماء)
    /** @var array<string> */
    public const array NOUN_PATTERNS = [
        'فَعْل',    // faʿl
        'فُعْل',    // fuʿl
        'فِعْل',    // fiʿl
        'فَعَل',    // faʿal
        'فَعِل',    // faʿil
        'فَعُل',    // faʿul
        'فُعَل',    // fuʿal
        'فِعَل',    // fiʿal
        'فَعْلَة',   // faʿla
        'فُعْلَة',   // fuʿla
        'فِعْلَة',   // fiʿla
        'فَعَلَة',   // faʿala
        'فِعَالَة',  // fiʿāla (profession)
        'فَعِيل',   // faʿīl (adjective)
        'فَعُول',   // faʿūl
        'فَاعِل',   // fāʿil (active participle)
        'مَفْعُول',  // mafʿūl (passive participle)
        'مَفْعَل',   // mafʿal (place/time)
        'مِفْعَل',   // mifʿal (instrument)
        'مِفْعَال',  // mifʿāl (instrument)
        'تَفْعِيل',  // tafʿīl (verbal noun II)
        'مُفَاعَلَة', // mufāʿala (verbal noun III)
        'إِفْعَال',  // ifʿāl (verbal noun IV)
        'تَفَعُّل',  // tafaʿʿul (verbal noun V)
        'تَفَاعُل',  // tafāʿul (verbal noun VI)
        'انْفِعَال', // infiʿāl (verbal noun VII)
        'افْتِعَال', // iftiʿāl (verbal noun VIII)
        'اسْتِفْعَال', // istifʿāl (verbal noun X)
    ];

    // Root letters (حروف الجذر)
    public const string ROOT_LETTERS = 'ءابتثجحخدذرزسشصضطظعغفقكلمنهوي';

    // Extra letters that are not part of roots (حروف الزيادة)
    /** @var array<string> */
    public const array EXTRA_LETTERS = ['أ', 'ا', 'و', 'ي', 'ت', 'م', 'ن', 'س', 'ه'];

    // Mnemonic for extra letters: سألتمونيها
    public const string EXTRA_MNEMONIC = 'سألتمونيها';

    // Diacritics to remove
    /** @var array<string> */
    public const array DIACRITICS = [
        'ً', 'ٌ', 'ٍ', 'َ', 'ُ', 'ِ', 'ّ', 'ْ', 'ـ',
    ];

    // Alef variants
    /** @var array<string> */
    public const array ALEF_VARIANTS = ['أ', 'إ', 'آ', 'ٱ'];

    public const string ALEF_NORMAL = 'ا';

    // Short word minimum length
    public const int MIN_WORD_LENGTH = 2;

    // Root length (typically 3, sometimes 4)
    public const int TRILATERAL_ROOT = 3;
    public const int QUADRILATERAL_ROOT = 4;
}
