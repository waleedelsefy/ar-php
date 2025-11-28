<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Stopwords;

/**
 * Stopwords Config - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Stopwords
 */
final readonly class Config
{
    // Categories
    public const string CATEGORY_PREPOSITIONS = 'prepositions';
    public const string CATEGORY_CONJUNCTIONS = 'conjunctions';
    public const string CATEGORY_PRONOUNS = 'pronouns';
    public const string CATEGORY_ARTICLES = 'articles';
    public const string CATEGORY_PARTICLES = 'particles';
    public const string CATEGORY_COMMON = 'common';
    public const string CATEGORY_VERBS = 'verbs';
    public const string CATEGORY_ADVERBS = 'adverbs';

    /** @var array<string> */
    public const array VALID_CATEGORIES = [
        self::CATEGORY_PREPOSITIONS,
        self::CATEGORY_CONJUNCTIONS,
        self::CATEGORY_PRONOUNS,
        self::CATEGORY_ARTICLES,
        self::CATEGORY_PARTICLES,
        self::CATEGORY_COMMON,
        self::CATEGORY_VERBS,
        self::CATEGORY_ADVERBS,
    ];

    // Prepositions (حروف الجر)
    /** @var array<string> */
    public const array PREPOSITIONS = [
        'من', 'إلى', 'على', 'في', 'عن', 'مع',
        'الى', 'علي', 'ب', 'ل', 'ك', 'بين',
        'حتى', 'منذ', 'خلال', 'عبر', 'دون',
        'فوق', 'تحت', 'أمام', 'خلف', 'قبل', 'بعد',
        'عند', 'لدى', 'نحو', 'ضد', 'حول',
    ];

    // Conjunctions (حروف العطف)
    /** @var array<string> */
    public const array CONJUNCTIONS = [
        'و', 'أو', 'ثم', 'ف', 'بل', 'لكن',
        'أم', 'لا', 'إما', 'حتى', 'أي',
    ];

    // Pronouns (الضمائر)
    /** @var array<string> */
    public const array PRONOUNS = [
        // Personal pronouns
        'أنا', 'نحن', 'أنت', 'أنتِ', 'أنتم', 'أنتما', 'أنتن',
        'هو', 'هي', 'هم', 'هما', 'هن',
        // Demonstrative pronouns
        'هذا', 'هذه', 'هذان', 'هاتان', 'هؤلاء',
        'ذلك', 'تلك', 'ذانك', 'تانك', 'أولئك',
        // Relative pronouns
        'الذي', 'التي', 'اللذان', 'اللتان', 'الذين', 'اللاتي', 'اللواتي',
        'ما', 'من',
    ];

    // Articles and definite markers (أدوات التعريف)
    /** @var array<string> */
    public const array ARTICLES = [
        'ال', 'لل',
    ];

    // Particles (الأدوات)
    /** @var array<string> */
    public const array PARTICLES = [
        // Question particles
        'هل', 'أ', 'ما', 'ماذا', 'من', 'أين', 'متى', 'كيف', 'لماذا', 'كم',
        // Negation
        'لا', 'لن', 'لم', 'ما', 'ليس', 'ليست', 'ليسوا',
        // Conditional
        'إن', 'إذا', 'لو', 'لولا',
        // Others
        'قد', 'سوف', 'س', 'إنما', 'أنما',
    ];

    // Common words (كلمات شائعة)
    /** @var array<string> */
    public const array COMMON = [
        'كان', 'كانت', 'كانوا', 'يكون', 'تكون',
        'أن', 'إن', 'أنه', 'إنه', 'أنها', 'إنها',
        'كل', 'بعض', 'غير', 'أي', 'كلا', 'كلتا',
        'هناك', 'هنا', 'حيث', 'أيضا', 'أيضاً',
        'فقط', 'فإن', 'لأن', 'إذ', 'إذن',
        'مثل', 'نفس', 'ذات', 'جدا', 'جداً',
        'كذلك', 'وكذلك', 'أكثر', 'أقل',
    ];

    // Common verbs (أفعال شائعة)
    /** @var array<string> */
    public const array COMMON_VERBS = [
        'قال', 'قالت', 'قالوا', 'يقول', 'تقول',
        'كان', 'كانت', 'كانوا', 'يكون', 'تكون',
        'أصبح', 'أصبحت', 'بات', 'باتت',
        'جاء', 'جاءت', 'ذهب', 'ذهبت',
        'يجب', 'ينبغي', 'يمكن',
    ];

    // Adverbs (الظروف)
    /** @var array<string> */
    public const array ADVERBS = [
        'الآن', 'اليوم', 'غدا', 'غداً', 'أمس', 'البارحة',
        'دائما', 'دائماً', 'أبدا', 'أبداً', 'مرة', 'أحيانا', 'أحياناً',
        'هنا', 'هناك', 'حيث', 'أين',
        'كثيرا', 'كثيراً', 'قليلا', 'قليلاً',
        'جدا', 'جداً', 'تماما', 'تماماً', 'فقط',
    ];

    /**
     * Get all default stopwords combined
     *
     * @return array<string>
     */
    public static function getAllDefault(): array
    {
        return \array_unique(\array_merge(
            self::PREPOSITIONS,
            self::CONJUNCTIONS,
            self::PRONOUNS,
            self::ARTICLES,
            self::PARTICLES,
            self::COMMON,
            self::COMMON_VERBS,
            self::ADVERBS,
        ));
    }

    /**
     * Get stopwords by category
     *
     * @return array<string>
     */
    public static function getByCategory(string $category): array
    {
        return match ($category) {
            self::CATEGORY_PREPOSITIONS => self::PREPOSITIONS,
            self::CATEGORY_CONJUNCTIONS => self::CONJUNCTIONS,
            self::CATEGORY_PRONOUNS => self::PRONOUNS,
            self::CATEGORY_ARTICLES => self::ARTICLES,
            self::CATEGORY_PARTICLES => self::PARTICLES,
            self::CATEGORY_COMMON => self::COMMON,
            self::CATEGORY_VERBS => self::COMMON_VERBS,
            self::CATEGORY_ADVERBS => self::ADVERBS,
            default => [],
        };
    }
}
