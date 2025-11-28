<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\WordFrequency;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\WordFrequency\Contracts\WordFrequencyInterface;
use ArPHP\Core\Modules\WordFrequency\Services\WordFrequencyService;

/**
 * WordFrequency Module - PHP 8.4
 *
 * @package ArPHP\Core\Modules\WordFrequency
 */
final class WordFrequencyModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?WordFrequencyService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'word-frequency';
    }

    public function register(): void
    {
        $this->service = new WordFrequencyService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): WordFrequencyInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Count word frequencies
     *
     * @return array<string, int>
     */
    public function count(string $text): array
    {
        return $this->getService()->count($text);
    }

    /**
     * Get top words
     *
     * @return array<string, int>
     */
    public function topWords(string $text, int $limit = 10): array
    {
        return $this->getService()->topWords($text, $limit);
    }

    /**
     * Get word count
     */
    public function wordCount(string $text): int
    {
        return $this->getService()->wordCount($text);
    }

    /**
     * Get unique word count
     */
    public function uniqueWordCount(string $text): int
    {
        return $this->getService()->uniqueWordCount($text);
    }

    /**
     * Get character count
     */
    public function characterCount(string $text, bool $includeSpaces = false): int
    {
        return $this->getService()->characterCount($text, $includeSpaces);
    }

    /**
     * Get sentence count
     */
    public function sentenceCount(string $text): int
    {
        return $this->getService()->sentenceCount($text);
    }

    /**
     * Get statistics
     *
     * @return array{words: int, unique_words: int, characters: int, sentences: int, avg_word_length: float}
     */
    public function statistics(string $text): array
    {
        return $this->getService()->statistics($text);
    }

    /**
     * Get frequency percentages
     *
     * @return array<string, float>
     */
    public function frequencyPercent(string $text): array
    {
        return $this->getService()->frequencyPercent($text);
    }

    public static function getIdentifier(): string
    {
        return 'word-frequency';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'count',
            'topWords',
            'wordCount',
            'uniqueWordCount',
            'characterCount',
            'sentenceCount',
            'statistics',
            'frequencyPercent',
        ];
    }
}
