<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Stopwords;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\Stopwords\Contracts\StopwordsInterface;
use ArPHP\Core\Modules\Stopwords\Services\StopwordsService;

/**
 * Stopwords Module - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Stopwords
 */
final class StopwordsModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?StopwordsService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'stopwords';
    }

    public function register(): void
    {
        $this->service = new StopwordsService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): StopwordsInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Check if word is a stopword
     */
    public function isStopword(string $word): bool
    {
        return $this->getService()->isStopword($word);
    }

    /**
     * Remove stopwords from text
     */
    public function removeStopwords(string $text): string
    {
        return $this->getService()->removeStopwords($text);
    }

    /**
     * Filter stopwords from word array
     *
     * @param array<string> $words
     * @return array<string>
     */
    public function filterStopwords(array $words): array
    {
        return $this->getService()->filterStopwords($words);
    }

    /**
     * Get all stopwords
     *
     * @return array<string>
     */
    public function getStopwords(): array
    {
        return $this->getService()->getStopwords();
    }

    /**
     * Add custom stopwords
     *
     * @param array<string> $words
     */
    public function addStopwords(array $words): void
    {
        $this->getService()->addStopwords($words);
    }

    /**
     * Remove from list
     *
     * @param array<string> $words
     */
    public function removeFromList(array $words): void
    {
        $this->getService()->removeFromList($words);
    }

    /**
     * Reset to default
     */
    public function reset(): void
    {
        $this->getService()->reset();
    }

    /**
     * Get stopwords by category
     *
     * @return array<string>
     */
    public function getByCategory(string $category): array
    {
        return $this->getService()->getByCategory($category);
    }

    /**
     * Count stopwords in text
     */
    public function countStopwords(string $text): int
    {
        return $this->getService()->countStopwords($text);
    }

    public static function getIdentifier(): string
    {
        return 'stopwords';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'isStopword',
            'removeStopwords',
            'filterStopwords',
            'getStopwords',
            'addStopwords',
            'removeFromList',
            'reset',
            'getByCategory',
            'countStopwords',
        ];
    }
}
