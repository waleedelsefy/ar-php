<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Lemmatizer;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\Lemmatizer\Contracts\LemmatizerInterface;
use ArPHP\Core\Modules\Lemmatizer\Services\LemmatizerService;

/**
 * Lemmatizer Module - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Lemmatizer
 */
final class LemmatizerModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?LemmatizerService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'lemmatizer';
    }

    public function register(): void
    {
        $this->service = new LemmatizerService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): LemmatizerInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Get lemma of word
     */
    public function lemmatize(string $word): string
    {
        return $this->getService()->lemmatize($word);
    }

    /**
     * Lemmatize text
     */
    public function lemmatizeText(string $text): string
    {
        return $this->getService()->lemmatizeText($text);
    }

    /**
     * Get word root
     */
    public function getRoot(string $word): string
    {
        return $this->getService()->getRoot($word);
    }

    /**
     * Stem word
     */
    public function stem(string $word): string
    {
        return $this->getService()->stem($word);
    }

    /**
     * Remove prefix
     */
    public function removePrefix(string $word): string
    {
        return $this->getService()->removePrefix($word);
    }

    /**
     * Remove suffix
     */
    public function removeSuffix(string $word): string
    {
        return $this->getService()->removeSuffix($word);
    }

    /**
     * Remove affixes
     */
    public function removeAffixes(string $word): string
    {
        return $this->getService()->removeAffixes($word);
    }

    /**
     * Check for prefix
     */
    public function hasPrefix(string $word): bool
    {
        return $this->getService()->hasPrefix($word);
    }

    /**
     * Check for suffix
     */
    public function hasSuffix(string $word): bool
    {
        return $this->getService()->hasSuffix($word);
    }

    /**
     * Get word pattern
     */
    public function getPattern(string $word): string
    {
        return $this->getService()->getPattern($word);
    }

    public static function getIdentifier(): string
    {
        return 'lemmatizer';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'lemmatize',
            'lemmatizeText',
            'getRoot',
            'stem',
            'removePrefix',
            'removeSuffix',
            'removeAffixes',
            'hasPrefix',
            'hasSuffix',
            'getPattern',
        ];
    }
}
