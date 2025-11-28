<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\SpellChecker;

/**
 * SpellChecker Config - PHP 8.4
 *
 * @package ArPHP\Core\Modules\SpellChecker
 */
final readonly class Config
{
    // Edit distance threshold for suggestions
    public const int DEFAULT_THRESHOLD = 2;
    public const int MAX_SUGGESTIONS = 10;

    // Common Arabic words (basic dictionary)
    /** @var array<string> */
    public const array COMMON_WORDS = [
        // Articles and particles
        'في', 'من', 'على', 'إلى', 'عن', 'مع', 'هذا', 'هذه', 'ذلك', 'تلك',
        'الذي', 'التي', 'هو', 'هي', 'هم', 'هن', 'نحن', 'أنا', 'أنت',
        'كان', 'كانت', 'يكون', 'تكون', 'قد', 'لقد', 'قال', 'قالت',

        // Common nouns
        'الله', 'رسول', 'نبي', 'كتاب', 'قرآن', 'سورة', 'آية',
        'يوم', 'ليلة', 'شهر', 'سنة', 'عام', 'وقت', 'ساعة', 'دقيقة',
        'رجل', 'امرأة', 'طفل', 'أب', 'أم', 'أخ', 'أخت', 'ابن', 'بنت',
        'بيت', 'دار', 'مدينة', 'قرية', 'بلد', 'وطن', 'أرض', 'سماء',
        'ماء', 'نار', 'هواء', 'شمس', 'قمر', 'نجم', 'بحر', 'نهر', 'جبل',

        // Common verbs
        'ذهب', 'جاء', 'رأى', 'سمع', 'علم', 'عرف', 'أخذ', 'أعطى',
        'وجد', 'ترك', 'جعل', 'قام', 'جلس', 'مشى', 'ركض', 'نام',
        'أكل', 'شرب', 'كتب', 'قرأ', 'تكلم', 'فهم', 'فكر', 'عمل',

        // Common adjectives
        'كبير', 'صغير', 'طويل', 'قصير', 'جميل', 'قبيح', 'جديد', 'قديم',
        'حار', 'بارد', 'سريع', 'بطيء', 'قوي', 'ضعيف', 'صعب', 'سهل',
        'أبيض', 'أسود', 'أحمر', 'أخضر', 'أزرق', 'أصفر',

        // Numbers
        'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة', 'عشرة',
        'مائة', 'ألف', 'مليون',

        // Common expressions
        'نعم', 'لا', 'شكرا', 'عفوا', 'مرحبا', 'أهلا', 'سلام', 'وداعا',
        'إن', 'أن', 'لكن', 'لأن', 'حتى', 'كي', 'إذا', 'لو', 'عندما', 'حين',

        // More common words
        'شيء', 'كل', 'بعض', 'أي', 'كثير', 'قليل', 'أكثر', 'أقل',
        'أول', 'آخر', 'قبل', 'بعد', 'فوق', 'تحت', 'أمام', 'خلف',
        'الناس', 'الحياة', 'الموت', 'الحق', 'العلم', 'العمل', 'الحب',
    ];

    // Common spelling mistakes and corrections
    /** @var array<string, string> */
    public const array COMMON_CORRECTIONS = [
        'انشاء' => 'إنشاء',
        'ان' => 'أن',
        'اذا' => 'إذا',
        'الى' => 'إلى',
        'انا' => 'أنا',
        'انت' => 'أنت',
        'انتم' => 'أنتم',
        'هاذا' => 'هذا',
        'هاذه' => 'هذه',
        'ضالك' => 'ذلك',
        'لاكن' => 'لكن',
        'سوا' => 'سوى',
        'علي' => 'على',
        'فى' => 'في',
        'مثلا' => 'مثلاً',
        'ايضا' => 'أيضاً',
        'جدا' => 'جداً',
        'شكرن' => 'شكراً',
    ];

    // Arabic diacritics (harakat)
    /** @var array<string> */
    public const array DIACRITICS = [
        'ً', 'ٌ', 'ٍ', 'َ', 'ُ', 'ِ', 'ّ', 'ْ', 'ـ',
    ];

    // Confusable letter pairs (commonly mixed up)
    /** @var array<array{0: string, 1: string}> */
    public const array CONFUSABLE_PAIRS = [
        ['ض', 'ظ'],
        ['ذ', 'ز'],
        ['ث', 'س'],
        ['ق', 'ك'],
        ['ط', 'ت'],
        ['ص', 'س'],
        ['أ', 'ا'],
        ['إ', 'ا'],
        ['آ', 'ا'],
        ['ة', 'ه'],
        ['ى', 'ي'],
    ];

    // Minimum word length for spell check
    public const int MIN_WORD_LENGTH = 2;

    // Maximum word length (avoid processing very long strings)
    public const int MAX_WORD_LENGTH = 50;
}
