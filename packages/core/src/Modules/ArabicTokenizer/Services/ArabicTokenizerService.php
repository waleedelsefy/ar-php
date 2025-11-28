<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicTokenizer\Services;

use ArPHP\Core\Modules\ArabicTokenizer\Config;
use ArPHP\Core\Modules\ArabicTokenizer\Contracts\ArabicTokenizerInterface;
use ArPHP\Core\Modules\ArabicTokenizer\Exceptions\ArabicTokenizerException;

/**
 * Arabic Tokenizer Service - PHP 8.4
 *
 * @package ArPHP\Core\Modules\ArabicTokenizer
 */
final class ArabicTokenizerService implements ArabicTokenizerInterface
{
    /**
     * @inheritDoc
     */
    public function tokenize(string $text): array
    {
        if ($text === '') {
            return [];
        }

        // Match words including Arabic characters
        \preg_match_all(Config::WORD_PATTERN, $text, $matches);

        return \array_filter($matches[0], fn($token) => $token !== '');
    }

    /**
     * @inheritDoc
     */
    public function sentences(string $text): array
    {
        if ($text === '') {
            return [];
        }

        // Split on sentence terminators followed by space or end
        $sentences = \preg_split(Config::SENTENCE_PATTERN, $text, -1, PREG_SPLIT_NO_EMPTY);

        return \array_map('trim', $sentences ?: []);
    }

    /**
     * @inheritDoc
     */
    public function paragraphs(string $text): array
    {
        if ($text === '') {
            return [];
        }

        // Split on double newlines
        $paragraphs = \preg_split(Config::PARAGRAPH_PATTERN, $text, -1, PREG_SPLIT_NO_EMPTY);

        return \array_map('trim', $paragraphs ?: []);
    }

    /**
     * @inheritDoc
     */
    public function wordCount(string $text): int
    {
        return \count($this->tokenize($text));
    }

    /**
     * @inheritDoc
     */
    public function charCount(string $text, bool $includeSpaces = false): int
    {
        if (!$includeSpaces) {
            $text = \preg_replace('/\s/u', '', $text);
        }

        return \mb_strlen($text, 'UTF-8');
    }

    /**
     * @inheritDoc
     */
    public function sentenceCount(string $text): int
    {
        return \count($this->sentences($text));
    }

    /**
     * @inheritDoc
     */
    public function tokenizeWithPositions(string $text): array
    {
        if ($text === '') {
            return [];
        }

        $tokens = [];
        $pattern = '/(' . Config::ARABIC_PATTERN . '+|[a-zA-Z]+|\d+|[^\s\w]|\s+)/u';

        \preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[0] as $match) {
            $token = $match[0];
            $start = $match[1];

            $tokens[] = [
                'token' => $token,
                'start' => $start,
                'end' => $start + \strlen($token),
                'type' => $this->getTokenType($token),
            ];
        }

        return $tokens;
    }

    /**
     * @inheritDoc
     */
    public function ngrams(string $text, int $n = 2): array
    {
        if ($n < 1) {
            throw ArabicTokenizerException::invalidNgramSize($n);
        }

        $words = $this->tokenize($text);

        if (\count($words) < $n) {
            return [];
        }

        $ngrams = [];

        for ($i = 0; $i <= \count($words) - $n; $i++) {
            $ngrams[] = \implode(' ', \array_slice($words, $i, $n));
        }

        return $ngrams;
    }

    /**
     * @inheritDoc
     */
    public function wordFrequency(string $text): array
    {
        $words = $this->tokenize($text);
        $frequency = [];

        foreach ($words as $word) {
            $normalized = $this->normalizeWord($word);

            if (!isset($frequency[$normalized])) {
                $frequency[$normalized] = 0;
            }

            $frequency[$normalized]++;
        }

        // Sort by frequency descending
        \arsort($frequency);

        return $frequency;
    }

    /**
     * @inheritDoc
     */
    public function isWord(string $text): bool
    {
        $text = \trim($text);

        if ($text === '') {
            return false;
        }

        // Check if entire string matches word pattern
        return (bool) \preg_match('/^' . Config::ARABIC_PATTERN . '+$/u', $text)
            || (bool) \preg_match('/^[a-zA-Z]+$/u', $text);
    }

    /**
     * @inheritDoc
     */
    public function splitBy(string $text, string $pattern): array
    {
        $result = @\preg_split($pattern, $text, -1, PREG_SPLIT_NO_EMPTY);

        if ($result === false) {
            throw ArabicTokenizerException::invalidPattern($pattern);
        }

        return $result;
    }

    /**
     * Get token type
     */
    private function getTokenType(string $token): string
    {
        if (\preg_match('/^\s+$/', $token)) {
            return Config::TYPE_WHITESPACE;
        }

        if (\preg_match('/^\d+$/', $token)) {
            return Config::TYPE_NUMBER;
        }

        if (\in_array($token, Config::PUNCTUATION, true)) {
            return Config::TYPE_PUNCTUATION;
        }

        if (\preg_match('/^' . Config::ARABIC_PATTERN . '+$/u', $token)) {
            return Config::TYPE_ARABIC;
        }

        if (\preg_match('/^[a-zA-Z]+$/', $token)) {
            return Config::TYPE_LATIN;
        }

        if (\preg_match('/' . Config::ARABIC_PATTERN . '/u', $token) && \preg_match('/[a-zA-Z]/', $token)) {
            return Config::TYPE_MIXED;
        }

        return Config::TYPE_UNKNOWN;
    }

    /**
     * Normalize word for frequency counting
     */
    private function normalizeWord(string $word): string
    {
        // Remove diacritics
        $diacritics = [
            "\u{064B}", "\u{064C}", "\u{064D}", "\u{064E}",
            "\u{064F}", "\u{0650}", "\u{0651}", "\u{0652}",
        ];

        $word = \str_replace($diacritics, '', $word);

        // Normalize alef variants
        $word = \str_replace(['أ', 'إ', 'آ'], 'ا', $word);

        return \mb_strtolower($word, 'UTF-8');
    }

    /**
     * Get unique words
     *
     * @return array<string>
     */
    public function uniqueWords(string $text): array
    {
        $words = $this->tokenize($text);

        return \array_values(\array_unique(\array_map(
            fn($word) => $this->normalizeWord($word),
            $words
        )));
    }

    /**
     * Get character frequency
     *
     * @return array<string, int>
     */
    public function charFrequency(string $text): array
    {
        $frequency = [];
        $length = \mb_strlen($text, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = \mb_substr($text, $i, 1, 'UTF-8');

            if (\preg_match('/\s/', $char)) {
                continue;
            }

            if (!isset($frequency[$char])) {
                $frequency[$char] = 0;
            }

            $frequency[$char]++;
        }

        \arsort($frequency);

        return $frequency;
    }

    /**
     * Tokenize preserving whitespace
     *
     * @return array<string>
     */
    public function tokenizePreserveWhitespace(string $text): array
    {
        return \preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
    }

    /**
     * Get text statistics
     *
     * @return array{
     *     words: int,
     *     chars: int,
     *     chars_no_space: int,
     *     sentences: int,
     *     paragraphs: int,
     *     unique_words: int,
     *     avg_word_length: float
     * }
     */
    public function getStats(string $text): array
    {
        $words = $this->tokenize($text);
        $wordCount = \count($words);

        $avgLength = 0.0;
        if ($wordCount > 0) {
            $totalLength = \array_sum(\array_map(fn($w) => \mb_strlen($w, 'UTF-8'), $words));
            $avgLength = $totalLength / $wordCount;
        }

        return [
            'words' => $wordCount,
            'chars' => $this->charCount($text, true),
            'chars_no_space' => $this->charCount($text, false),
            'sentences' => $this->sentenceCount($text),
            'paragraphs' => \count($this->paragraphs($text)),
            'unique_words' => \count($this->uniqueWords($text)),
            'avg_word_length' => \round($avgLength, 2),
        ];
    }
}
