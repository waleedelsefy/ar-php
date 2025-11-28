<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Sentiment;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\Sentiment\Contracts\SentimentInterface;
use ArPHP\Core\Modules\Sentiment\Services\SentimentService;

/**
 * Sentiment Module - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Sentiment
 */
final class SentimentModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?SentimentService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'sentiment';
    }

    public function register(): void
    {
        $this->service = new SentimentService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): SentimentInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Analyze sentiment
     *
     * @return array{score: float, label: string, confidence: float}
     */
    public function analyze(string $text): array
    {
        return $this->getService()->analyze($text);
    }

    /**
     * Get sentiment score
     */
    public function getScore(string $text): float
    {
        return $this->getService()->getScore($text);
    }

    /**
     * Get sentiment label
     */
    public function getLabel(string $text): string
    {
        return $this->getService()->getLabel($text);
    }

    /**
     * Check if positive
     */
    public function isPositive(string $text): bool
    {
        return $this->getService()->isPositive($text);
    }

    /**
     * Check if negative
     */
    public function isNegative(string $text): bool
    {
        return $this->getService()->isNegative($text);
    }

    /**
     * Check if neutral
     */
    public function isNeutral(string $text): bool
    {
        return $this->getService()->isNeutral($text);
    }

    /**
     * Get breakdown
     *
     * @return array{positive_words: array<string>, negative_words: array<string>, score: float}
     */
    public function getBreakdown(string $text): array
    {
        return $this->getService()->getBreakdown($text);
    }

    /**
     * Add positive words
     *
     * @param array<string> $words
     */
    public function addPositiveWords(array $words): void
    {
        $this->getService()->addPositiveWords($words);
    }

    /**
     * Add negative words
     *
     * @param array<string> $words
     */
    public function addNegativeWords(array $words): void
    {
        $this->getService()->addNegativeWords($words);
    }

    public static function getIdentifier(): string
    {
        return 'sentiment';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'analyze',
            'getScore',
            'getLabel',
            'isPositive',
            'isNegative',
            'isNeutral',
            'getBreakdown',
            'addPositiveWords',
            'addNegativeWords',
        ];
    }
}
