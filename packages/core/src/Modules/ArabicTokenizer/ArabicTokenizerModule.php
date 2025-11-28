<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicTokenizer;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\ArabicTokenizer\Contracts\ArabicTokenizerInterface;
use ArPHP\Core\Modules\ArabicTokenizer\Services\ArabicTokenizerService;

/**
 * Arabic Tokenizer Module - PHP 8.4
 *
 * Provides tokenization utilities for Arabic text.
 *
 * @package ArPHP\Core\Modules\ArabicTokenizer
 */
final class ArabicTokenizerModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?ArabicTokenizerService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'arabic_tokenizer';
    }

    public function register(): void
    {
        $this->service = new ArabicTokenizerService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): ArabicTokenizerInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Tokenize text into words
     *
     * @return array<string>
     */
    public function tokenize(string $text): array
    {
        return $this->getService()->tokenize($text);
    }

    /**
     * Tokenize into sentences
     *
     * @return array<string>
     */
    public function sentences(string $text): array
    {
        return $this->getService()->sentences($text);
    }

    /**
     * Tokenize into paragraphs
     *
     * @return array<string>
     */
    public function paragraphs(string $text): array
    {
        return $this->getService()->paragraphs($text);
    }

    /**
     * Get word count
     */
    public function wordCount(string $text): int
    {
        return $this->getService()->wordCount($text);
    }

    /**
     * Get character count
     */
    public function charCount(string $text, bool $includeSpaces = false): int
    {
        return $this->getService()->charCount($text, $includeSpaces);
    }

    /**
     * Get sentence count
     */
    public function sentenceCount(string $text): int
    {
        return $this->getService()->sentenceCount($text);
    }

    /**
     * Tokenize with positions
     *
     * @return array<array{token: string, start: int, end: int, type: string}>
     */
    public function tokenizeWithPositions(string $text): array
    {
        return $this->getService()->tokenizeWithPositions($text);
    }

    /**
     * Extract n-grams
     *
     * @return array<string>
     */
    public function ngrams(string $text, int $n = 2): array
    {
        return $this->getService()->ngrams($text, $n);
    }

    /**
     * Get word frequency
     *
     * @return array<string, int>
     */
    public function wordFrequency(string $text): array
    {
        return $this->getService()->wordFrequency($text);
    }

    /**
     * Check if string is word
     */
    public function isWord(string $text): bool
    {
        return $this->getService()->isWord($text);
    }

    public static function getIdentifier(): string
    {
        return 'arabic_tokenizer';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'tokenize',
            'sentences',
            'paragraphs',
            'wordCount',
            'charCount',
            'sentenceCount',
            'tokenizeWithPositions',
            'ngrams',
            'wordFrequency',
            'isWord',
        ];
    }
}
