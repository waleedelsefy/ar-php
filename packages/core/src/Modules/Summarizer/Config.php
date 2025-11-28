<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Summarizer;

/**
 * Summarizer Config - PHP 8.4
 *
 * Configuration for Arabic text summarization.
 *
 * @package ArPHP\Core\Modules\Summarizer
 */
final readonly class Config
{
    // Minimum text length for summarization
    public const int MIN_TEXT_LENGTH = 100;

    // Default number of sentences to extract
    public const int DEFAULT_SENTENCES = 3;

    // Default summary ratio
    public const float DEFAULT_RATIO = 0.3;

    // Sentence delimiters
    /** @var array<string> */
    public const array SENTENCE_DELIMITERS = [
        '.',
        '。',
        '!',
        '؟',  // Arabic question mark
        '?',
        '؛',  // Arabic semicolon
        '\n',
    ];

    // Arabic sentence end patterns
    public const string SENTENCE_PATTERN = '/[.!؟?؛\n]+/u';

    // Arabic stopwords for keyword extraction
    /** @var array<string> */
    public const array STOPWORDS = [
        'في', 'من', 'على', 'إلى', 'عن', 'مع', 'هذا', 'هذه', 'ذلك', 'تلك',
        'التي', 'الذي', 'الذين', 'اللذين', 'اللتين', 'ما', 'لا', 'لم', 'لن',
        'قد', 'كان', 'كانت', 'كانوا', 'يكون', 'تكون', 'أن', 'إن', 'أو', 'و',
        'ف', 'ب', 'ك', 'ل', 'ال', 'هو', 'هي', 'هم', 'هن', 'أنت', 'أنتم',
        'أنتن', 'نحن', 'أنا', 'كل', 'بعض', 'غير', 'أي', 'أية', 'كلما',
        'حيث', 'إذ', 'إذا', 'لو', 'لولا', 'مثل', 'بين', 'فوق', 'تحت', 'أمام',
        'خلف', 'قبل', 'بعد', 'حتى', 'منذ', 'خلال', 'ضد', 'عند', 'حول',
        'دون', 'سوى', 'إلا', 'لكن', 'لذلك', 'هنا', 'هناك', 'الآن', 'جداً',
        'بشكل', 'يجب', 'يمكن', 'ينبغي', 'ليس', 'ليست', 'ليسوا', 'كما', 'أيضاً',
        'ثم', 'كذلك', 'أما', 'وإن', 'فإن', 'وهو', 'وهي', 'وأن', 'بأن',
    ];

    // Position weight for first sentences
    public const float POSITION_WEIGHT_FIRST = 1.5;

    // Position weight for last sentences
    public const float POSITION_WEIGHT_LAST = 1.2;

    // Keyword weight multiplier
    public const float KEYWORD_WEIGHT = 2.0;

    // Length normalization factor
    public const float LENGTH_FACTOR = 0.1;

    // Arabic diacritics for removal
    /** @var array<string> */
    public const array DIACRITICS = [
        "\u{064E}", "\u{064F}", "\u{0650}", "\u{0651}",
        "\u{0652}", "\u{064B}", "\u{064C}", "\u{064D}",
    ];
}
