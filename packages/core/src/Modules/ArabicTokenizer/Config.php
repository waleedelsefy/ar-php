<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicTokenizer;

/**
 * Arabic Tokenizer Configuration - PHP 8.4
 *
 * @package ArPHP\Core\Modules\ArabicTokenizer
 */
final readonly class Config
{
    /**
     * Arabic sentence terminators
     */
    public const array SENTENCE_TERMINATORS = [
        '.',
        '!',
        '?',
        '؟',
        '۔',
        '。',
    ];

    /**
     * Arabic punctuation marks
     */
    public const array PUNCTUATION = [
        '،',  // Arabic comma
        '؛',  // Arabic semicolon
        '؟',  // Arabic question mark
        '.',
        ',',
        ';',
        ':',
        '!',
        '?',
        '"',
        "'",
        '(',
        ')',
        '[',
        ']',
        '{',
        '}',
        '-',
        '–',
        '—',
        '«',
        '»',
        '…',
    ];

    /**
     * Word break characters
     */
    public const array WORD_BREAKS = [
        ' ',
        "\t",
        "\n",
        "\r",
        '،',
        '؛',
        '.',
        ',',
        ';',
        ':',
        '!',
        '?',
        '؟',
        '"',
        "'",
        '(',
        ')',
        '[',
        ']',
        '{',
        '}',
        '«',
        '»',
    ];

    /**
     * Token types
     */
    public const string TYPE_WORD = 'word';
    public const string TYPE_NUMBER = 'number';
    public const string TYPE_PUNCTUATION = 'punctuation';
    public const string TYPE_WHITESPACE = 'whitespace';
    public const string TYPE_ARABIC = 'arabic';
    public const string TYPE_LATIN = 'latin';
    public const string TYPE_MIXED = 'mixed';
    public const string TYPE_UNKNOWN = 'unknown';

    /**
     * Arabic letter range (Unicode)
     */
    public const string ARABIC_PATTERN = '[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]';

    /**
     * Word pattern including Arabic
     */
    public const string WORD_PATTERN = '/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}a-zA-Z0-9_]+/u';

    /**
     * Sentence split pattern
     */
    public const string SENTENCE_PATTERN = '/(?<=[.!?؟])\s+/u';

    /**
     * Paragraph split pattern
     */
    public const string PARAGRAPH_PATTERN = '/\n\s*\n/u';
}
