<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Normalization;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\Normalization\Contracts\NormalizationInterface;
use ArPHP\Core\Modules\Normalization\Services\NormalizationService;

/**
 * Normalization Module - PHP 8.4
 *
 * Provides Arabic text normalization utilities.
 *
 * @package ArPHP\Core\Modules\Normalization
 */
final class NormalizationModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?NormalizationService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'normalization';
    }

    public function register(): void
    {
        $this->service = new NormalizationService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): NormalizationInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Full normalization
     */
    public function normalize(string $text): string
    {
        return $this->getService()->normalize($text);
    }

    /**
     * Remove diacritics
     */
    public function removeDiacritics(string $text): string
    {
        return $this->getService()->removeDiacritics($text);
    }

    /**
     * Normalize Alef
     */
    public function normalizeAlef(string $text): string
    {
        return $this->getService()->normalizeAlef($text);
    }

    /**
     * Normalize Ta Marbuta
     */
    public function normalizeTaMarbuta(string $text): string
    {
        return $this->getService()->normalizeTaMarbuta($text);
    }

    /**
     * Normalize Alef Maqsura
     */
    public function normalizeAlefMaqsura(string $text): string
    {
        return $this->getService()->normalizeAlefMaqsura($text);
    }

    /**
     * Remove tatweel
     */
    public function removeTatweel(string $text): string
    {
        return $this->getService()->removeTatweel($text);
    }

    /**
     * Remove non-Arabic
     */
    public function removeNonArabic(string $text): string
    {
        return $this->getService()->removeNonArabic($text);
    }

    /**
     * Normalize whitespace
     */
    public function normalizeWhitespace(string $text): string
    {
        return $this->getService()->normalizeWhitespace($text);
    }

    /**
     * Normalize numbers
     */
    public function normalizeNumbers(string $text, string $style = 'arabic'): string
    {
        return $this->getService()->normalizeNumbers($text, $style);
    }

    /**
     * Normalize for search
     */
    public function normalizeForSearch(string $text): string
    {
        return $this->getService()->normalizeForSearch($text);
    }

    /**
     * Custom normalization
     *
     * @param array<string, mixed> $options
     */
    public function normalizeCustom(string $text, array $options): string
    {
        return $this->getService()->normalizeCustom($text, $options);
    }

    public static function getIdentifier(): string
    {
        return 'normalization';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'normalize',
            'removeDiacritics',
            'normalizeAlef',
            'normalizeTaMarbuta',
            'normalizeAlefMaqsura',
            'removeTatweel',
            'removeNonArabic',
            'normalizeWhitespace',
            'normalizeNumbers',
            'normalizeForSearch',
            'normalizeCustom',
        ];
    }
}
